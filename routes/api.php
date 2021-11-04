<?php

use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use  App\Http\Controllers\GroupController;
use  App\Http\Controllers\CategoriesController;

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

// Route::get('/token', function (Request $request) {
//     $token = $request->session()->token();

//     $token = csrf_token();

// });

Route::group(['middleware'=>'auth:sanctum'],function(){
    Route::get('getFreelancerInfo/{id}',[UserController::class,'get_freelancer_info']);
    Route::get('getClientInfo/{id}',[UserController::class,'get_client_info']);
    
    Route::get('logout',[UserController::class,'logout']);
});

Route::get('getCategories',[CategoriesController::class,'getCategories']);
Route::get('getAllUsers',[UserController::class,'getAllUsers']);


Route::post('addUser',[UserController::class,'add_user']);
Route::post('addFreelancerInfo',[UserController::class,'Insert_freelancer']);
Route::post('addClientInfo',[UserController::class,'Insert_client']);
Route::post('Login',[UserController::class,'login']);