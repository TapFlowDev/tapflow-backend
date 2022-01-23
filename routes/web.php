<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\AdminTool\AdminConroller;
use App\Http\Controllers\AdminTool\AdminCategoriesController;
use App\Http\Controllers\AdminTool\AdminSubCategoryController;
use App\Http\Controllers\AdminTool\FreeLancerController;
use App\Http\Controllers\AdminTool\ClientsController;
use App\Http\Controllers\AdminTool\TeamsController;
use App\Http\Controllers\AdminTool\CompaniesController;
use App\Http\Controllers\AdminTool\GroupsController;
use App\Http\Controllers\AdminTool\RolesController;
use App\Http\Controllers\AdminTool\AnnouncementsController;
use App\Http\Controllers\AdminTool\DashboardController;
use App\Http\Controllers\AdminTool\EmailController;
use App\Http\Controllers\AdminTool\CategoryTypesController;
use Illuminate\Http\Request;
use App\Mail\SendInvitation;
use Illuminate\Support\Facades\Mail;

// use App\Http\Controllers\AdminTool\AdminSubCategoryController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/AdminTool');
});
Route::get('/AdminTool', function () {
    return view('index');
});


Route::get('/AdminTool/login', function () {
    return view('AdminTool.login');
});

// Route::get('/login', function () {
//  return redirect('/AdminTool/login');
// });

Route::get('/AdminTool/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'auth.isAdmin']);

// Route::get('/AdminTool/dashboard', function () {
//     return view('AdminTool.dashboard');
// })->middleware(['auth', 'auth.isAdmin']);

Route::prefix('AdminTool')->middleware(['auth', 'auth.isAdmin'])->name('AdminTool.')->group(function () {
    // Route::get('/freelancers/sendEmailShow/{id}', [FreeLancerController::class, 'sendEmailShow'])->name('freelancers.sendEmailShow');
    Route::resource('/categoryTypes', CategoryTypesController::class);
    Route::resource('/users', AdminConroller::class);
    Route::resource('categoryTypes.categories', AdminCategoriesController::class)->shallow();
    Route::resource('/freelancers', FreeLancerController::class);
    Route::resource('/clients', ClientsController::class);
    Route::resource('/agencies', TeamsController::class);
    Route::resource('/companies', CompaniesController::class);
    Route::resource('/group', GroupsController::class);
    // Route::post('', [GroupsController::class, 'verifyOrUnVerifyGroup']);
    
    // Route::resource('/categories/{$parentId}/subCategories', AdminSubCategoryController::class);
    Route::resource('categories.subCategories', AdminSubCategoryController::class)->shallow();
    Route::resource('/announcements', AnnouncementsController::class);
    Route::get('sendEmailToUser/{id}', [EmailController::class, 'show'])->name('sendEmailShow.show');
    Route::post('sendEmail',[EmailController::class, 'send'])->name('sendEmail.send');
    
});
Route::get('/r', function (Request $request) {
    return redirect('/api/r/'.$request->r);
});
Route::get('/testForms', function () {
    return view('testForms');
});
Route::get('/checkout', function () {
    return view('checkout');
});
// Route::get('',[InviteUsersController::class,'addUserByToken']);

// Route::get('/emailTest', function(){
//     return new SendInvitation;
// });