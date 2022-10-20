<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorBook extends ResourcePivot
{
    use HasFactory;

    protected $fillable = [
        'author_id'
    ];
}
