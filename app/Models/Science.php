<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Science extends ResourceModel
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected static $data = [
        'name' =>  [ 'validation' => 'required|string|unique:App\Models\Science,name']
    ];

    public function books() {
        return $this->belongsToMany(Book::class);
    }
}
