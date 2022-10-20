<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookScience extends  ResourcePivot
{
    use HasFactory;

    protected $fillable = [
        'science_id'
    ];


}
