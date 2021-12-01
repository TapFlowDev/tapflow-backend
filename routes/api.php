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
use  App\Http\Controllers\Proposals;
use  App\Http\Controllers\Final_proposals;
use  App\Http\Controllers\InviteUsersController;
use  App\Http\Controllers\ImagesController;
use  App\Http\Controllers\UserLinksController;
use  App\Http\Controllers\UserAttachmentsController;
use  App\Http\Controllers\TeamController;
use  App\Http\Controllers\GroupsLinksController;

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
Route::post('newRegister',[UserController::class,'newRegister']);
Route::post('newLogin',[UserController::class,'newLogin']);

Route::group(['middleware'=>'auth:sanctum'],function(){
  
    Route::post('newLogout',[UserController::class,'newLogout']);

    Route::get('getAllUsers',[UserController::class,'getAllUsers']);
    
    
    
    
});



Route::post('addProposal',[Proposals::class,'Insert']);
Route::post('addFinalProposal',[Final_proposals::class,'Insert']);
Route::get('getCountries',[UserController::class,'get_countries']);
Route::put('UpdateUserInfo',[UserController::class,'UpdateUserInfo']);
Route::put('updateClientBio',[ClientController::class,'update_Bio']);
Route::put('updateFreelancerBio',[FreeLancerController::class,'update_Bio']);
Route::post('updateLinks',[UserLinksController::class,'update_links']);
Route::post('updateAttachment',[UserAttachmentsController::class,'update_attachment']);
Route::post('updateTools',[FreeLancerController::class,'update_tools']);
Route::post('register',[UserController::class,'register']);
Route::get('getTeamInfo/{id}',[TeamController::class,'get_team']);
Route::put('updateGeneralInfo',[TeamController::class,'updateGeneralInfo']);





Route::get('getFreelancerInfo/{id}',[FreeLancerController::class,'get_freelancer_info']);
Route::get('getCategories',[CategoriesController::class,'getCategories']);
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
Route::post('joinWithCode',[InviteUsersController::class,'joinGroupByCode']);
Route::post('saveImage',[ImagesController::class,'Insert']);