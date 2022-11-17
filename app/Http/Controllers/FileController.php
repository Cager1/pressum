<?php

namespace App\Http\Controllers;

use App\Models\ResourceFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Image;
use setasign\Fpdi\Fpdi;


class FileController extends Controller
{



    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->except(['show', 'uuidShow']);
    }

    public function upload(Request $request)
    {
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

        return self::uploadFile($file,$book, $name, $folder, $disk);
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
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile(storage_path('app/books/' . $path));
            if ($pageCount > 3) {
                // Add first 3 pages to pdf
                for ($i = 1; $i <= 3; $i++) {
                    $tplId = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tplId);
                    $pdf->AddPage($size['orientation'], $size);
                    $pdf->useTemplate($tplId);
                }
            }
            $pdf->Output(storage_path('app/books/' . $filename), 'F');
            $filename = md5($file) . now()->timestamp . 'cut-version' . '.pdf';


            $cutPdf = ResourceFile::create([
                'name' => $name,
                'filepath' => $path,
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
