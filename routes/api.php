<?php

use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use  App\Http\Controllers\GroupController;
use  App\Http\Controllers\CategoriesController;
use  App\Http\Controllers\ProjectController;
use  App\Http\Controllers\ClientController;
use  App\Http\Controllers\FreeLancerController;
use  App\Http\Controllers\InviteUsersController;

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
  





});

Route::get('getFreelancerInfo/{id}',[FreeLancerController::class,'get_freelancer_info']);
Route::get('getCategories',[CategoriesController::class,'getCategories']);
Route::get('getAllUsers',[UserController::class,'getAllUsers']);
Route::get('getClientInfo/{id}',[ClientController::class,'get_client_info']);
Route::post('addUser',[UserController::class,'add_user']);
Route::post('addTeam',[GroupController::class,'add_group_team']);
Route::post('addCompany',[GroupController::class,'add_group_company']);
Route::post('addFreelancerInfo',[FreeLancerController::class,'Insert_freelancer']);
Route::post('addClientInfo',[ClientController::class,'Insert_client']);
Route::post('Login',[UserController::class,'login']);
Route::post('createProject',[ProjectController::class,'Insert']);
Route::post('sendInvitation',[InviteUsersController::class,'sendInvitation']);
Route::get('r/{token}',[InviteUsersController::class,'getDataByToken']);
Route::post('acceptOrRefuseInvitation',[InviteUsersController::class,'updateInvitation']);