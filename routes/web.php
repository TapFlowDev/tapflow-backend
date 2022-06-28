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
use App\Http\Controllers\AdminTool\ApplicationsController;
use App\Http\Controllers\AdminTool\DashboardController;
use App\Http\Controllers\AdminTool\EmailController;
use App\Http\Controllers\AdminTool\CategoryTypesController;
use App\Http\Controllers\AdminTool\ClientsRequests;
use App\Http\Controllers\AdminTool\CountriesController;
use App\Http\Controllers\AdminTool\DepositRequestController;
use App\Http\Controllers\AdminTool\DummyCompaines;
use App\Http\Controllers\AdminTool\DummyProjects;
use App\Http\Controllers\AdminTool\FeaturesController;
use App\Http\Controllers\AdminTool\ProjectsController;
use App\Http\Controllers\AdminTool\FromOptions;
use App\Http\Controllers\AdminTool\InitialProposals;
use App\Http\Controllers\AdminTool\NotificationSettings;
use App\Http\Controllers\AdminTool\PrioritiesController;
use App\Http\Controllers\AdminTool\SkillsController;
use App\Http\Controllers\AdminTool\StaticDataController;
use App\Http\Controllers\AdminTool\WalletsController;
use App\Http\Controllers\AdminTool\WalletsTransactionsController;
use App\Http\Controllers\AdminTool\WithdrawlRequestsController;
use App\Http\Controllers\MailChimpController;
use App\Http\Controllers\NotificationsSettings;
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

Route::get('/AdminTool/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'auth.isAdmin']);

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
    Route::resource('categories.subCategories', AdminSubCategoryController::class)->shallow();
    Route::resource('/announcements', AnnouncementsController::class);
    Route::resource('/projects', ProjectsController::class);
    Route::resource('/dummyCompanies', DummyCompaines::class);
    Route::resource('/dummyProjects', DummyProjects::class);
    Route::resource('/formOptions', FromOptions::class);
    Route::resource('/clientsRequests', ClientsRequests::class);
    Route::resource('/staticData', StaticDataController::class);
    Route::resource('/notificationSettings', NotificationSettings::class);
    Route::resource('companies.depositRequests', DepositRequestController::class)->shallow();
    Route::resource('/initialProposals', InitialProposals::class);
    Route::resource('wallet.transactions', WalletsTransactionsController::class)->shallow();
    Route::resource('agencies.withdrawal', WithdrawlRequestsController::class);
    Route::resource('/priorities', PrioritiesController::class);
    Route::resource('/countries', CountriesController::class);
    Route::resource('/skills', SkillsController::class);
    Route::resource('/features', FeaturesController::class);
    Route::resource('/applications', ApplicationsController::class);
    Route::get('/addSkillsCSV', [SkillsController::class, 'addByCSV'])->name('skills.showAddByCSV');
    Route::post('/addSkillsCSV', [SkillsController::class, 'createByCSV'])->name('skills.creatAddByCSV');
    Route::get('/checkSkill/{skill}', [SkillsController::class, 'isSkillExsits']);

    Route::get('/addFeatureCSV', [FeaturesController::class, 'addByCSV'])->name('features.showAddByCSV');
    Route::post('/addFeatureCSV', [FeaturesController::class, 'createByCSV'])->name('features.creatAddByCSV');
    Route::get('/checkFeature/{feature}', [FeaturesController::class, 'isFeatureExsits']);

    // Route::resource('agencies.wallets', WalletsController::class)->shallow();
    Route::get('sendEmailToUser/{id}', [EmailController::class, 'show'])->name('sendEmailShow.show');
    // Route::get('waitingList', [AdminConroller::class, 'waitingList'])->name('waitingList.index');
    Route::post('sendEmail', [EmailController::class, 'send'])->name('sendEmail.send');
    Route::get('/recommendProject/{id}', [ProjectsController::class, 'recommendProject'])->name('recommendProject.show');
    Route::post('/filterAgenciesByProjectCategories', [ProjectsController::class, 'filterAgenciesByProjectCategories'])->name('filterAgenciesByProjectCategories.filter');
    Route::post('sendEmailAgencies/{id}', [ProjectsController::class, 'sendAgenciesEmail'])->name('sendEmailAgencies.send');
    Route::post('sendEmailAgency/{id}', [ProjectsController::class, 'sendAgencyEmail'])->name('sendEmailAgency.send');
    Route::post('verifyProject/{id}', [ProjectsController::class, 'verifyProject'])->name('verifyProject.update');
    Route::get('hideContent/{id}', [StaticDataController::class, 'hideContent'])->name('hideContent.hideContent');
    Route::get('showContent/{id}', [StaticDataController::class, 'showContent'])->name('showContent.showContent');
    //
    Route::post('/wallet/create', [WalletsController::class, 'create'])->name('wallet.create');
    Route::get('/agencyExportCsv', [GroupsController::class, 'agencyExportCsv'])->name('agecies.exportCsv');
    Route::post('removeMatch/{id}', [ProjectsController::class, 'removeMatch'])->name('removeMatch.destroy');

});
Route::get('/r', function (Request $request) {
    return redirect('/api/r/' . $request->r);
});
// Route::get('/testForms', function () {
//     return view('testForms');
// });
Route::get('/checkout', function () {
    return view('checkout');
});
Route::get('/donePayment', function () {
    return view('donePayment');
});
Route::get('/mailchimptest', [MailChimpController::class, 'test']);

// Route::get('/notifications', [NotificationsSettings::class, 'test']);
// Route::get('/testEst', [Proposals::class, 'testPropsal']);

// Route::get('/testForms', function(){
//     return view('testForms');
// });