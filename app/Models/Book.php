<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends ResourceModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'isbn', 'slug', 'created_by', 'impressum', 'contact', 'locked', 'locked_contact'
    ];

    protected $appends = ['image', 'documents'];

    protected static $data = [
        'name' => [
            'validation' => 'required|string',
        ],
        'isbn' => [
            'validation' => 'required|string',
        ],
        'slug' => [
            'validation' => 'nullable|string',
        ],
        'impressum' => [
            'validation' => 'nullable|string',
        ],
        'created_by' => [
            'validation' => 'nullable|string',
        ],
        'contact' => [
            'validation' => 'nullable|string',
        ],
        'locked' => [
            'validation' => 'nullable|boolean',
        ],
        'locked_contact' => [
            'validation' => 'nullable|string',
        ],
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class)->using(BookCategory::class);
    }

    public function authors() {
        return $this->belongsToMany(Author::class)->using(AuthorBook::class);
    }

    public function sciences() {
        return $this->belongsToMany(Science::class)->using(BookScience::class);
    }

    public function files() {
        return $this->hasMany(ResourceFile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'uid');
    }

    public function purchases()
    {
        return $this->belongsToMany(User::class, 'purchases')->using(Purchase::class);
    }

    public function GetImageAttribute()
    {
        return $this->files->where('folder', 'images')->first();
    }

    // get all files that are not images
    public function GetDocumentsAttribute()
    {
        return $this->files->where('folder', 'books');
    }

}
