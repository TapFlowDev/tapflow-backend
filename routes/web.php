<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\AdminTool\AdminConroller;
use App\Http\Controllers\AdminTool\AdminCategoriesController;
use App\Http\Controllers\AdminTool\AdminSubCategoryController;
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
    // return redirect('/AdminTool');
    return view('index');
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

Route::get('/AdminTool/dashboard', function () {
    return view('AdminTool.dashboard');
})->middleware(['auth', 'auth.isAdmin']);

Route::prefix('AdminTool')->middleware(['auth', 'auth.isAdmin'])->name('AdminTool.')->group(function () {
    Route::resource('/users', AdminConroller::class);
     Route::resource('/categories', AdminCategoriesController::class);
    // Route::resource('/categories/{$parentId}/subCategories', AdminSubCategoryController::class);
    Route::resource('categories.subCategories', AdminSubCategoryController::class)->shallow();
    // Route::get('', AdminSubCategoryController::class);
    // Route::get('/categories/{catId}/subCategories/{subId}', function (AdminCategoriesController $catId, AdminSubCategoryController $subId) {
    //     //
    // })->name('subCategories.show');
        //   Route::prefix('AdminTool/categories/')
        //   ->middleware(['auth', 'auth.isAdmin'])
        //      ->name('AdminTool.categories.subCategories')
        //     ->group(function (AdminCategoriesController $catId, AdminSubCategoryController $subId = 0 ) {
        //         Route::resource('/{catId}/subCategories', AdminSubCategoryController::class);
        //     });

});

// Route::get('/subCategories/{$id}',[AdminSubCategoryController::class, 'index']);

// Route::prefix('AdminTool/subCategories')->name('AdminTool.subCategories.')->middleware(['auth', 'auth.isAdmin'])->group(function () {
    
//     // Route::get('', AdminSubCategoryController::class);
//      Route::resource('/', AdminSubCategoryController::class);

//      Route::get('/{$id}',[AdminSubCategoryController::class, 'index']);
    
// });


// Route::get('/AdminTool/subCategories/{$id}', [AdminTool\AdminSubCategoryController::class, 'index']);

// Route::view('addAdmin', 'addUser');
// Route::post('addAdmin', AdminAuth::class, 'addAdmin');
