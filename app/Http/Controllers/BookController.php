<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends ResourceController
{
    protected static $modelName = 'Book';

    public function books(Request $request)
    {

        return Book::all()->load('files','authors','sciences');
    }
}
