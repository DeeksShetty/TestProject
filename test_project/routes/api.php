<?php

use App\Http\Controllers\UserController;
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
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::group(['middleware'=>['auth:sanctum']], function(){

    Route::put('update-profile',[UserController::class,'updateProfile']);
    Route::get('profile-detail',[UserController::class,'getProfile']);

    Route::post('create-article',[UserController::class,'createPost'])->middleware('checkRole:writer');//role validation
    Route::put('update-article/{articleId}',[UserController::class,'updatePost'])->middleware('checkRole:writer');//rle validation
    Route::get('list-article',[UserController::class,'getPost']);
    Route::get('single-article/{articleId}',[UserController::class,'getSinglePost']);
    Route::delete('delete-article/{articleId}',[UserController::class,'deletePost'])->middleware('checkRole:writer');//role validation

    Route::post('add-comment',[UserController::class,'addComment'])->middleware('checkRole:editor');//role validation
    // Route::post('list-comment/{articleId}',[UserController::class,'getComments']);

});
