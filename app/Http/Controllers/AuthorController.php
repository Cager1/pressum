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
    protected static $middlewareExcept = ['index', 'show', 'create', 'allAuthors'];
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
        $author->books()->detach($book);
        return $author->load('books.files', 'user');
    }



    // update author
    public function updateAuthor(Request $request, $id)
    {
        $author = Author::findOrFail($id);

        // log pressum_session cookie

        $request->validate([
        'name' => '|string',
        'last_name' => '|string',
        'orcid' => 'string|nullable|unique:authors,orcid',
        'email' => '|string|unique:authors,email',
        'user_uid' => 'string|nullable|unique:authors,user_uid',
        ]);

        if ($request->user_uid) {
            $user = User::where('uid', $request->user_uid)->first();
            if ($user->role->name === 'Korisnik') {
                response()->json([ 'message' => 'Korisnik mora imati ulogu autora kako bi mu se dodjelio autor.' ], 401);
            }
        }

        if ($author->update($request->all())) {
            return $author;
        } else {
            return response()->json(
                ['message' => 'Došlo je do pogreške kod izmjene autora.',
                    'error' => $author
                ],
                400);
        }
    }
}
