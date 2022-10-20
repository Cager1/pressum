<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasFile extends ResourceMorphPivot
{
    protected $userstamping = false;

    protected $fillable = [
        'file_id', 'model_id', 'model_type', 'attributes'
    ];

    protected static function booted()
    {
        // Override for now
    }
}
