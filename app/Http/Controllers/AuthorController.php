<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AuthorController extends ResourceController
{
    use ValidatesRequests;
    protected static $modelName = 'Author';

    // return authors with users
    public function allAuthors(Request $request)
    {
        return Author::all()->load('user');
    }

    public function createAuthor(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'last_name' => 'required|string',
            'orcid' => 'string|nullable|unique:authors,orcid',
            'email' => 'required|string|unique:authors,email',
            'created_by' => 'required|string',
            'user_uid' => 'string|nullable|unique:authors,user_uid',
        ]);

        if ($request->user_uid) {
            $user = User::where('uid', $request->user_uid)->first();
            if ($user->role->name === 'Korisnik') {
                return response()->json([
                    'message' => 'Korisnik mora imati ulogu autora kako bi mu se dodjelio autor.',
                ], 403);
            }
        }

        $author = Author::create($request->all());

        return response()->json($author, 201);
    }

    // update author
    public function updateAuthor(Request $request, $id)
    {
        $author = Author::findOrFail($id);

        $request->validate([
        'name' => 'required|string',
        'last_name' => 'required|string',
        'orcid' => 'string|nullable|unique:authors,orcid',
        'email' => 'required|string|unique:authors,email',
        'user_uid' => 'string|nullable|unique:authors,user_uid',
        ]);

        if ($request->user_uid) {
            $user = User::where('uid', $request->user_uid)->first();
            if ($user->role->name === 'Korisnik') {
                response()->json([ 'message' => 'Korisnik mora imati ulogu autora kako bi mu se dodjelio autor.' ], 401);
            }
        }

        if ($author->update($request->all())) {
            return response()->json(['message' => 'Autor uspjesno izmjenjen.'], 201);
        } else {
            return response()->json(['message' => 'Došlo je do pogreške kod izmjene autora.'], 400);
        }
    }
}
