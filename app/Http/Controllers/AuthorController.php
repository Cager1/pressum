<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthorController extends ResourceController
{

    use ValidatesRequests;
    protected static $modelName = 'Author';

    protected static $middlewareCustom = ['auth:sanctum'];
    protected static $middlewareExcept = ['index', 'show', 'allAuthors'];


    // return authors with users
    public function allAuthors(Request $request)
    {
        return Author::all()->load('user', 'books.files');
    }

    // detach book from author
    public function detachBook(Request $request, $authorId, $bookId)
    {
        $author = Author::find($authorId);
        $book = Book::find($bookId);
        if (Auth::user()->can('detachBook', [Author::class, $author, $book])) {
            $author->books()->detach($book);
            return $author->load('user', 'books.files');
        } else {
            return response()->json(['message' => 'You are not authorized to detach book from author.'], 403);
        }
    }

    // store author
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'orcid' => 'required|string',
            'email' => 'required|string|email',
        ]);

        $author = Author::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'orcid' => $request->orcid,
            'email' => $request->email,
            'created_by' => Auth::user()->uid,
        ]);
        return $author;
    }
}
