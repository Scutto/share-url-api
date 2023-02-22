<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UrlController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/not_logged', function() {
    return collect(['error' => 'User not logged in'])->toJson();
})->name('not_logged');

Route::name('user.')->prefix('user')->group(function () {
    Route::post('post/login', [UserController::class, 'postLogin'])->name('post.login');
    Route::middleware('auth')->post('post/logout', [UserController::class, 'postLogout'])->name('post.logout');
    Route::middleware('auth')->post('post/follow', [UserController::class, 'postFollowUser'])->name('post.toggle.follow');
});

Route::name('url.')->middleware('auth')->prefix('url')->group(function () {
    Route::get('get/all', [UrlController::class, 'getAllUrls'])->name('get.all');
    Route::get('get/followed', [UrlController::class, 'getUrlsFollowed'])->name('get.by_followed');
    Route::get('get/creator/{creatorId}', [UrlController::class, 'getUrlsByCreatorId'])->name('get.by_creator');
    Route::get('get/search/{tags}', [UrlController::class, 'getUrlsByTags'])->name('get.tags');
    
    Route::post('post/new', [UrlController::class, 'postCreateNewUrl'])->name('post.new');
    Route::post('post/like', [UrlController::class, 'postLikeUrl'])->name('post.toggle.like');
});