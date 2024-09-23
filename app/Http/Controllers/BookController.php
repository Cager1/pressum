<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class BookController extends ResourceController
{
    protected static $modelName = 'Book';

//    protected static $middlewareCustom = ['auth:sanctum'];
//    protected static $middlewareExcept = ['index', 'show','getBookBySlug'];

    public function books(Request $request)
    {

        return Book::all()->load('files','authors','sciences');
    }


    // Get books frphom last 6 months
    public function booksLastSixMonths(Request $request)
    {
        return Book::selectRaw('COUNT(*) as count, YEAR(created_at) year, MONTH(created_at) month')
            ->groupBy('year', 'month')
            ->get();
    }

    // Get last 10 added books
    public function booksLastTen(Request $request)
    {
        return Book::orderBy('created_at', 'desc')->limit(10)->get()->load('files','authors','sciences');
    }

    public function getBookBySlug(Request $request, $slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();
        // if user owns book or is admin
        if ($user = Auth::user()) {
            if ($user->role->permissions->contains('name', 'view_file') || $user->uid == $book->created_by) {
                return $book->load('files','authors','sciences');
            }
        }


        // query of book by slug with authors and sciences and files
        // Only if file is cut version or mimetype is image or user has purchased the book
        $book = Book::where('slug', $slug)
            ->with(['authors', 'sciences', 'files' => function ($query) {
                $query->where(function ($query) {
                    $query->where('mimetype', 'like', 'image/%')
                        ->orWhere('cut_version', true)
                        ->orWhereHas('book.purchases', function ($query) {
                            $query->where('user_id', Auth::id());
                        });
                });
            }])
            ->firstOrFail();
        return $book;
    }

    // create book
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'isbn' => 'required',
            'contact' => 'nullable',
        ]);

        $slug = Str::slug($request->name, '-');
        // check if slug already %like in books
        $slugCount = Book::where('slug', 'like', $slug . '%')->count();
        if ($slugCount > 0) {
            $slugCount += 1;
            $slug .= '-' . $slugCount ;
        }


        $book = Book::create([
            'name' => $request->name,
            'isbn' => $request->isbn,
            'contact' => $request->contact,
            'slug' => $slug,
            'created_by' => Auth::user()->uid,
        ]);
        if ($request->sciences) {
            $book->sciences()->sync($request->sciences);
        }
        if ($request->authors) {
            $book->authors()->sync($request->authors);
        }
        return $book->load('authors','sciences');
    }

    // update book
    public function update(Request $request, $id)
    {
        $slug = Str::slug($request->name, '-');
        // check if slug already %like in books
        $slugCount = Book::where('slug', 'like', $slug . '%')->count();
        if ($slugCount > 0) {
            $slugCount += 1;
            $slug .= '-' . $slugCount ;
        }

        $request->merge([
            'slug' => $slug,
        ]);

        return parent::update($request, $id);
    }


    // user purchases a book
    public function purchase(Request $request, $bookId){
        $book = Book::where('id', $bookId)->firstOrFail();
        if (Auth::user()->can('purchase', $book)) {
            $user = Auth::user();
            // find book by id
            // attach book to user
            // return json user

            // if user already purchased book
            if ($user->purchases()->where('book_id', $bookId)->exists()) {
                return response()->json([
                    'message' => 'You already purchased this book',
                ], 422);
            }
            $book->purchases()->attach($user->id);

            return response()->json([
                'user' => $user->purchases,
                'message' => "Korisnik je kupio knjigu"
            ]);
        }
        else {
            return response()->json([
                'message' => "Nemate ovlasti za kupovinu knjige"
            ], 403);
        }
    }
}
