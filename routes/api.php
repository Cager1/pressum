<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScienceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
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

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

$resources = [
    'books' => BookController::class,
    'authors' => AuthorController::class,
    'sciences' => ScienceController::class,
    'permissions' => PermissionController::class,
    'categories' => CategoryController::class,

];

Route::get('getNumberOfBooksInSciences', [ScienceController::class, 'getScienceWithBooksCount']);

// get all users
Route::get('users', [UserController::class, 'index']);
Route::get('usersLastSixMonths', [UserController::class, 'usersLastSixMonths']);
Route::get('latestUsers', [UserController::class, 'usersLastTen']);
Route::get('searchUsers/{user}', [UserController::class, 'search']);
Route::put('updateUserRole', [UserController::class, 'updateRole']);
Route::put('banUser', [UserController::class, 'ban']);

// create author
Route::post('createAuthor', [AuthorController::class, 'createAuthor']);
// update author
Route::put('updateAuthor/{id}', [AuthorController::class, 'updateAuthor'])->middleware('is.author');
// search author
Route::get('searchAuthor/{author}', [AuthorController::class, 'searchAuthors']);
// return all authors with their users
Route::get('authorUsers', [AuthorController::class, 'allAuthors']);
// detach book form author
Route::put('detachBook/{authorId}/{bookId}', [AuthorController::class, 'detachBook']);
// detach book from user
Route::put('detachBookFromUser/{userUID}/{bookID}', [UserController::class, 'detachBook']);
// purchase book
Route::post('books/purchase/{book_id}', [BookController::class, 'purchase']);

// get one role
Route::get('getRole/{id}', [RoleController::class, 'getRole']);
// get all roles
Route::get('userRoles', [RoleController::class, 'userRoles']);
// get all roles with user count
Route::get('rolesWithUserCount', [RoleController::class, 'rolesWithUserCount']);
// create new role
Route::post('createRole', [RoleController::class, 'create']);
// delete role
Route::delete('deleteRole/{id}', [RoleController::class, 'delete']);

Route::get('/booksRelations', [App\Http\Controllers\BookController::class, 'books']);
Route::get('/booksLastSixMonths', [App\Http\Controllers\BookController::class, 'booksLastSixMonths']);
Route::get('/latestBooks', [App\Http\Controllers\BookController::class, 'booksLastTen']);
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



// temporary login
Route::get('login/{uid}', [OAuthController::class, 'login']);
