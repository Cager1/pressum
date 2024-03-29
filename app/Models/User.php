<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uid', 'branch', 'first_name', 'last_name', 'name', 'email', 'role_id' , 'banned'
    ];

    protected static $data = [

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // user has one role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // has one author
    public function author()
    {
            return $this->hasOne(Author::class, 'user_uid', 'uid');
    }

    // has many books
    public function books()
    {
        return $this->hasMany(Book::class, 'created_by', 'uid');
    }

    public function purchases()
    {
        return $this->belongsToMany(Book::class, 'purchases');
    }
}
