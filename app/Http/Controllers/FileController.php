<?php

namespace App\Http\Controllers;

use App\Models\ResourceFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        if ($existing && str_starts_with($file->getMimeType(), 'image') && $folder == 'images') {
            $existing->delete();
        }

        // if file with $book_id exists and has folder of books, delete all of them, and if currently uploaded file is pdf
        $existing = ResourceFile::where('book_id', $book)->where('folder', 'books')->get();
        if ($existing && str_starts_with($file->getMimeType(), 'application/pdf') && $folder == 'books') {
            foreach ($existing as $e) {
                $e->delete();
            }
        }

        // if file with $book_id exists and has folder of chapters, delete all of them
        $existing = ResourceFile::where('book_id', $book)->where('folder', 'chapters')->get();
        if ($existing && $folder == 'chapters') {
            foreach ($existing as $e) {
                $e->delete();
            }
        }

        return self::uploadFile($file,$book, $name, $folder, $disk);
    }

    public function show(Request $request, ResourceFile $resourceFile)
    {
        // check if file can be viewed
        $this->authorize('view', $resourceFile);

        return Storage::response($resourceFile->full_path, $resourceFile->name);
    }

    public function uuidShow(Request $request, $uuid) {
        $resourceFile = ResourceFile::whereUuid($uuid)->firstOrFail();

        //if resource file not image or cut version check policy for view
        if (!str_starts_with($resourceFile->mimetype, 'image/')) {
            Gate::forUser(Auth::user() ?? new \App\Models\User())->authorize('view', $resourceFile);
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
            // First save the original file to a temporary location we can work with
            $tempFilename = md5($file) . now()->timestamp . '.pdf';
            $tempPath = storage_path('app/books/' . $tempFilename);
            file_put_contents($tempPath, file_get_contents($file));

            try {
                // Now we can analyze the saved file
                $pdf = new Fpdi();
                $pageCount = $pdf->setSourceFile($tempPath);
                error_log('Number of pages: ' . $pageCount);

                // Generate the cut version filename
                $cutFilename = md5($file) . now()->timestamp . 'cut-version.pdf';
                $cutPath = storage_path('app/books/' . $cutFilename);

                if ($pageCount > 3) {
                    // Use pdftk for larger documents
                    $pdf2 = new Pdf($tempPath);
                    $result = $pdf2->cat(1, 3)->saveAs($cutPath);
                    if ($result === false) {
                        return response()->json(['message' => $pdf2->getError()], 500);
                    }
                } else {
                    // Use FPDI for smaller documents
                    $pdf = new Fpdi();
                    $pdf->setSourceFile($tempPath);
                    for ($i = 1; $i <= $pageCount; $i++) {
                        $template = $pdf->importPage($i);
                        $pdf->AddPage();
                        $pdf->useTemplate($template);
                    }
                    $pdf->Output($cutPath, 'F');
                }

                $cutPdf = ResourceFile::create([
                    'name' => $name,
                    'filepath' => $cutFilename,
                    'mimetype' => $mimetype,
                    'folder' => $folder,
                    'book_id' => $book,
                    'cut_version' => true,
                ]);
            } catch (\Exception $e) {
                error_log('PDF processing error: ' . $e->getMessage());
                // Handle the error gracefully
            }
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
