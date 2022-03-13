<?php

namespace App\Http\Controllers;

use App\Models\ResourceFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Image;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['saml'])->except(['show', 'uuidShow']);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string',
            'file' => 'required|file',
            'attributes' => 'nullable|json',
            'folder' => 'nullable|string'
        ]);

        $folder = $request->folder ?? '';
        $disk = 'local';
        $file = $request->file('file');
        $name = $request->get('name');

        return self::uploadFile($file, $name, $folder, $disk);
    }

    public function show(Request $request, ResourceFile $resourceFile)
    {
        return Storage::response($resourceFile->full_path, $resourceFile->name);
    }

    public function uuidShow(Request $request, $uuid) {
        $resourceFile = ResourceFile::whereUuid($uuid)->firstOrFail();

        return Storage::response($resourceFile->full_path, $resourceFile->name);
    }

    public function destroy(Request $request, ResourceFile $resourceFile)
    {
        $resourceFile->delete();

        return response()->json(['message' => 'Success'], 204);
    }

    public static function uploadFile($file, $name = null, $folder = '', $disk = 'local', $jpg = false)
    {
        $mimetype = $file->getMimeType();

        $filename = md5($file) . now()->timestamp;

        if ($jpg && substr($mimetype, 0, 5) == 'image') {
            $img = Image::make($file->getRealPath())->encode('jpg');
            $filename .= '.jpg';
            $filepath = "{$folder}/{$filename}";
            Storage::disk($disk)->put($filepath, (string) $img);
            $path = basename($filepath);
            $mimetype = $img->mime();
        } else {
            $filename .= '.' . $file->getClientOriginalExtension();
            $path = basename(Storage::disk($disk)->putFileAs($folder, $file, $filename));
        }

        if (!$name)
            $name = $filename;

        $uploaded = ResourceFile::create([
            'name' => $name,
            'filepath' => $path,
            'mimetype' => $mimetype,
            'folder' => $folder
        ]);

        return $uploaded;
    }
}
