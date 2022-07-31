<?php

namespace App\Models;

use App\Traits\AttachesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Wildside\Userstamps\Userstamps;

class ResourceFile extends ResourceModel
{
    use AttachesUuid;

    protected $table = 'files';

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
}
