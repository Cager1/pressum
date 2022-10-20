<?php

namespace App\Http\Controllers;

use App\Models\Science;
use Illuminate\Http\Request;

class ScienceController extends ResourceController
{
    protected static $modelName = 'Science';

    // Get science and number of books that belong to that science
    public function getScienceWithBooksCount(Request $request)
    {
        return Science::withCount('books')->get();
    }


}
