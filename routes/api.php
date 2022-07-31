<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ScienceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$resources = [
    'books' => BookController::class,
    'authors' => AuthorController::class,
    'sciences' => ScienceController::class,

];

Route::get('/booksRelations', [App\Http\Controllers\BookController::class, 'books']);
Route::get('/booksBySlug/{slug}', [App\Http\Controllers\BookController::class, 'getBookBySlug']);
// Files
Route::post('/files', [App\Http\Controllers\FileController::class, 'upload']);
Route::delete('/files/{resourceFile}', [App\Http\Controllers\FileController::class, 'destroy']);
Route::get('/files/{uuid}', [App\Http\Controllers\FileController::class, 'uuidShow']);

foreach ($resources as $resource => $controller) {
    Route::get($resource . '/form', $controller . '@getFormData');
    Route::get($resource . '/{id}/{relation}', $controller . '@indexRelation');
    Route::post($resource . '/{id}/{relation}', $controller . '@manageRelation');
}

// API resources
Route::apiResources(
    $resources
);
