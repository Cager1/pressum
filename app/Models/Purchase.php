<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends ResourcePivot
{
    use HasFactory;

    protected $fillable = [
        'book_id', 'author_id',
    ];
}
