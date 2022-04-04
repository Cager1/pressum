<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends ResourceModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'last_name',
        'orchid',
        'email'
    ];

    protected static $data = [
        'name' => [
            'validation' => 'required|string',
        ],
        'last_name' => [
            'validation' => 'required|string',
        ],
        'orcid' => [
            'validation' => 'string|nullable',
        ],
        'email' => [
            'validation' => 'required|string',
        ],
    ];

    public function books() {
        return $this->belongsToMany(Book::class);
    }
}
