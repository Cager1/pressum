<?php

namespace App\Models;

use App\Traits\AttachesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Wildside\Userstamps\Userstamps;

class ResourceFile extends ResourceModel
{
    use AttachesUuid;

    protected $table = 'files';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name', 'attributes',
        'filepath', 'folder',
        'mimetype', 'book_id',
        'cut_version',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
        });

        static::updating(function ($model) {
        });

        static::deleting(function ($model) {
        });
    }

    protected $appends = ['file_url'];

    public static function index(Request $request, &$query)
    {
        // return all images
        $query->where('mimetype', 'like', 'image/%');
    }

    public function getFileUrlAttribute()
    {
        return URL::to('/api/files/' . $this->uuid);
    }

    public function getFullPathAttribute()
    {
        return $this->folder . '/' . $this->filepath;
    }

    public function delete()
    {
        Storage::delete($this->filepath);
        parent::delete();
    }

    public function getResponse() {
        return Storage::response($this->full_path, $this->name);
    }

    public function book() {
        return $this->belongsTo(Book::class);
    }
}
