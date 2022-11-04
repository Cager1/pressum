<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends ResourceModel
{
    use HasFactory;

    protected $model = 'author';

    protected $fillable = [
        'name',
        'last_name',
        'orcid',
        'email',
        'created_by',
        'user_uid',
    ];

    protected static $data = [
        'name' => [
            'validation' => 'required|string',
        ],
        'last_name' => [
            'validation' => 'required|string',
        ],
        'orcid' => [
            'validation' => 'string|nullable|unique:authors,orcid',
        ],
        'email' => [
            'validation' => 'required|email|unique:authors,email',
        ],
        'created_by' => [
            'validation' => 'required|string',
        ],
        'user_uid' => [
            'validation' => 'string|nullable',
        ],
    ];

    public function books(): BelongsToMany {
        return $this->belongsToMany(Book::class);
    }

    // has one user uid
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }


    public function createdBy()  {
        return $this->hasOne(User::class, 'created_by', 'uid');
    }
}
