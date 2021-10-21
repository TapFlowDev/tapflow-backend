<?php

use Illuminate\Support\Facades\Route;
use AdminTool\AdminConroller;
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
    
    Route::get('/AdminTool/dashboard', function () {
        return view('AdminTool.dashboard');
    })->middleware(['auth','auth.isAdmin']);
Route::prefix('AdminTool')->middleware(['auth','auth.isAdmin'])->name('AdminTool.')->group(function () {
    Route::resource('/users', AdminConroller::class);
    // Route::get('/register', AdminConroller::class);
    
});


// Route::view('addAdmin', 'addUser');
// Route::post('addAdmin', AdminAuth::class, 'addAdmin');
