<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookController extends ResourceController
{
    protected static $modelName = 'Book';

    public function books(Request $request)
    {

        return Book::all()->load('files','authors','sciences');
    }

    // Get books from last 6 months
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
        return Book::where('slug', $slug)->firstOrFail()->load('files','authors','sciences');
    }
}
