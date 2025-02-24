<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends ResourceModel
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected static $data = [
        'name' => [
            'validation' => 'string',
        ],
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

}
