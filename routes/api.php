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
use  App\Http\Controllers\GroupCategoriesController;
use  App\Http\Controllers\UserCategoriesController;
use  App\Http\Controllers\AnnouncementsController;
use  App\Http\Controllers\ContactUsController;
use  App\Http\Controllers\WalletsController;
use  App\Http\Controllers\ResetPasswordController;
use  App\Http\Controllers\WaitingListController;
use  App\Http\Controllers\NewCountriesController;
use  App\Http\Controllers\WalletsTransactionsController;
use  App\Http\Controllers\Milestones;


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
Route::post('newRegister', [UserController::class, 'newRegister']);
Route::post('newLogin', [UserController::class, 'newLogin']);
Route::post('register', [UserController::class, 'Register']);
Route::post('addUser', [UserController::class, 'add_user']);
Route::post('Login', [UserController::class, 'login']);
Route::get('getAnnouncements/{offset}', [AnnouncementsController::class, 'getAnnouncementsByLimit']);

Route::post('addCountries', [NewCountriesController::class, 'Insert']);
Route::get('getCountryById/{id}', [NewCountriesController::class, 'getCountryById']);
Route::get('getCountries', [NewCountriesController::class, 'getCountries']);


Route::post('acceptFinalProposal', [Final_proposals::class, 'acceptFinalProposal']);
Route::post('addTeam', [GroupController::class, 'add_group_team']);
Route::post('addFinalProposal', [Final_proposals::class, 'Insert']);    
Route::post('createWallet', [WalletsController::class, 'Insert']);    
Route::post('Deposit', [WalletsTransactionsController::class, 'deposit']);
Route::post('Withdraw', [WalletsTransactionsController::class, 'withdraw']);
Route::post('forgetPassword', [ResetPasswordController::class, 'sendLinkResetPassword']);
Route::post('reset-password', [ResetPasswordController::class, 'resetPasswordCheck']);
Route::post('contactUS', [ContactUsController::class, 'Insert']);
Route::post('waitingList', [WaitingListController::class, 'Insert']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('getFreelancerInfo/{id}', [FreeLancerController::class, 'get_freelancer_info']);
    Route::get('getTeamInfo/{id}', [TeamController::class, 'get_team']);
    Route::get('getCategories', [CategoriesController::class, 'getCategories']);
    Route::get('getTeamCategories/{id}', [GroupCategoriesController::class, 'getTeamCategories']);
    Route::get('getAllUsers', [UserController::class, 'getAllUsers']);
    Route::get('getClientInfo/{id}', [ClientController::class, 'get_client_info']);
    Route::get('r/{token}', [InviteUsersController::class, 'getDataByToken']);
    
    Route::post('newLogout', [UserController::class, 'newLogout']);
    Route::post('addProposal', [Proposals::class, 'Insert']);
    Route::post('updateTeamCategories', [GroupCategoriesController::class, 'updateTeamCategories']);
    Route::post('UpdateUserInfo', [UserController::class, 'UpdateUserInfo']);
    Route::post('updateClientBio', [ClientController::class, 'update_Bio']);
    Route::post('updateFreelancerBio', [FreeLancerController::class, 'update_Bio']);
    Route::post('updateUserLinks', [UserLinksController::class, 'update_links']);
    Route::post('updateUserCategories', [UserCategoriesController::class, 'updateUserCategories']);
    Route::post('updateAttachment', [UserAttachmentsController::class, 'update_attachment']);
    Route::post('updateTools', [FreeLancerController::class, 'update_tools']);
    Route::post('updateGeneralInfo', [TeamController::class, 'updateGeneralInfo']);
    Route::post('updateTeamBio', [TeamController::class, 'updateTeamBio']);
    Route::post('updateTeamLink', [TeamController::class, 'updateLink']);
    Route::post('updateTeamLinks', [GroupsLinksController::class, 'updateTeamLinks']);
  
    Route::post('addCompany', [GroupController::class, 'add_group_company']);
    Route::post('addFreelancerInfo', [FreeLancerController::class, 'Insert_freelancer']);
    Route::post('addClientInfo', [ClientController::class, 'Insert_client']);
    Route::post('createProject', [ProjectController::class, 'Insert']);
    Route::post('saveImage', [ImagesController::class, 'Insert']);
    Route::post('joinWithCode', [InviteUsersController::class, 'joinGroupByCode']);
    Route::post('sendInvitation', [InviteUsersController::class, 'sendInvitation']);
    Route::post('acceptOrRefuseInvitation', [InviteUsersController::class, 'updateInvitation']);
    Route::post('updateTeamImage', [TeamController::class, 'updateTeamImage']);
    Route::post('updateFreelancerImage', [FreeLancerController::class, 'updateFreelancerImage']);
});