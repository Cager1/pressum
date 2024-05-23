<?php

namespace App\Http\Controllers;

use App\Models\ResourceFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Image;
use mikehaertl\pdftk\Pdf;
use setasign\Fpdi\Fpdi;


class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->except(['uuidShow']);
    }

    public function upload(Request $request)
    {
        error_log('upload');
        $request->validate([
            'name' => 'nullable|string',
            'file' => 'required|file',
            'attributes' => 'nullable|json',
            'folder' => 'nullable|string',
            'book_id' => 'nullable|numeric'
        ]);

        $folder = $request->folder ?? '';
        $disk = 'local';
        $file = $request->file('file');
        $name = $request->get('name');
        $book = $request->book_id;

        // if file with $book_id exists and has folder of images, delete it, and if currently uploaded file is image
        $existing = ResourceFile::where('book_id', $book)->where('folder', 'images')->first();
        if ($existing && str_starts_with($file->getMimeType(), 'image')) {
            $existing->delete();
        }

        // if file with $book_id exists and has folder of books, delete all of them, and if currently uploaded file is pdf
        $existing = ResourceFile::where('book_id', $book)->where('folder', 'books')->get();
        if ($existing && str_starts_with($file->getMimeType(), 'application/pdf')) {
            foreach ($existing as $e) {
                $e->delete();
            }
        }

        return self::uploadFile($file,$book, $name, $folder, $disk);
    }

    public function show(Request $request, ResourceFile $resourceFile)
    {
        // user can view
        ResourceFile::checkPolicy('view', $resourceFile);

        return Storage::response($resourceFile->full_path, $resourceFile->name);
    }

    public function uuidShow(Request $request, $uuid) {
        $resourceFile = ResourceFile::whereUuid($uuid)->firstOrFail();

        //if resource file not image or cut version check policy for view
        if (!str_starts_with($resourceFile->mimetype, 'image/') && !$resourceFile->cut_version) {
            ResourceFile::checkPolicy('view', $resourceFile);
        }

        return Storage::response($resourceFile->full_path, $resourceFile->name);
    }

    public function destroy(Request $request, ResourceFile $resourceFile)
    {
        $resourceFile->delete();

        return response()->json(['message' => 'Success'], 204);
    }

    public static function uploadFile($file, $book, $name = null, $folder = '', $disk = 'local', $jpg = false)
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

        $cutPdf= null;
        // If file is pdf, cut all but first page
        if ($mimetype == 'application/pdf') {
            $filename = md5($file) . now()->timestamp . '.pdf';
            $pdf = new Pdf(storage_path('app/books/' . $filename));
            $data = $pdf->getData();
            if (preg_match('/NumberOfPages: (\d+)/', $data, $m)) {
                $number = $m[1];
                if ($number > 5) {
                    $pdf2 = new Pdf(storage_path('app/books/' . $filename));
                    $filename = md5($file) . now()->timestamp . 'cut-version.pdf';
                    $result = $pdf2->cat(1,5)->saveAs(storage_path('app/books/' . $filename));
                    if ($result === false) {
                        return response()->json(['message' => $pdf2->getError()], 500);
                    }
                } else {
                    $filename = md5($file) . now()->timestamp . 'cut-version.pdf';
                    $pdf->saveAs(storage_path('app/books/' . $filename));
                }
            }

            $cutPdf = ResourceFile::create([
                'name' => $name,
                'filepath' => $filename,
                'mimetype' => $mimetype,
                'folder' => $folder,
                'book_id' => $book,
                'cut_version' => true,
            ]);
        }

        if (!$name)
            $name = $filename;

        $uploaded = ResourceFile::create([
            'name' => $name,
            'filepath' => $path,
            'mimetype' => $mimetype,
            'folder' => $folder,
            'book_id' => $book,
        ]);

        return [$uploaded, $cutPdf];
    }
}
