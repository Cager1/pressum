<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends ResourceModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'isbn',
    ];

    protected static $data = [
        'name' => [
            'validation' => 'required|string',
        ],
        'isbn' => [
            'validation' => 'required|string',
        ]
    ];

    public function authors() {
        return $this->belongsToMany(Author::class);
    }

    public function sciences() {
        return $this->belongsToMany(Science::class);
    }

    public function files() {
        return $this->hasMany(ResourceFile::class);
    }

}
