<?php

use App\Http\Controllers\AdminTool\CompaniesController;
use App\Http\Controllers\AdminTool\GroupsController;
use App\Http\Controllers\AdminTool\ProjectsController;
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
use App\Http\Controllers\BillingInfoController;
use App\Http\Controllers\ClientsRequestsController;
use  App\Http\Controllers\ContactUsController;
use  App\Http\Controllers\WalletsController;
use  App\Http\Controllers\ResetPasswordController;
use  App\Http\Controllers\WaitingListController;
use  App\Http\Controllers\NewCountriesController;
use  App\Http\Controllers\WalletsTransactionsController;
use  App\Http\Controllers\GroupMembersController;
use  App\Http\Controllers\TasksController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FormOptionsController;
use App\Http\Controllers\Milestones;
use App\Http\Controllers\ContentDataController;
use App\Http\Controllers\DepositRequestController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\HireDeveloperProposalsController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\Requirement;
use App\Http\Controllers\SkillsController;
use App\Notifications\RealTimeMessageNotification;
use App\Http\Controllers\WithdrawlRequestController;
use App\Models\Milestone;
use phpDocumentor\Reflection\ProjectFactory;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\HireDeveloperFinalProposalController;

// use App\Http\Controllers\PaymentController;

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
// for testing test
// Route::post('GeneratePdf', [Final_proposals::class, 'GeneratePdf']);
Route::post('SendDraft', [Final_proposals::class, 'SendDraft']);
Route::post('acceptFinalProposal', [Final_proposals::class, 'acceptFinalProposal']);
Route::get('testtest/{id}', [Final_proposals::class, 'testtest']);
Route::get('updateMilestonesPrices/{m}/{f}', [Milestones::class, 'updateMilestonesPrices']);
Route::get('canSubmit/{id}', [Milestones::class, 'canSubmit']);

Route::get('getPendingProjectInfo/{id}', [ProjectController::class, 'getPendingProjectInfo']);

Route::post('createWallet', [WalletsController::class, 'Insert']);
Route::post('Deposit', [WalletsTransactionsController::class, 'deposit']);
Route::post('Withdraw', [WalletsTransactionsController::class, 'withdraw']);
Route::post('updateFinalProposal', [Final_proposals::class, 'updateFinalProposal']);
Route::post('updateTasks', [TasksController::class, 'updateTasks']);
// Route::post('checkoutPayment', [PaymentController::class, 'checkout']);
// Route::get('companyPendingProjects/{offset}', [ProjectController::class, 'getCompanyPendingProjects']);

// apis does not need user token 
Route::post('newRegister', [UserController::class, 'newRegister']);
Route::post('newLogin', [UserController::class, 'newLogin']);
Route::post('register', [UserController::class, 'Register']);
Route::post('addUser', [UserController::class, 'add_user']);
Route::post('Login', [UserController::class, 'login']);
Route::post('addRequest', [ClientsRequestsController::class, 'Insert']);
Route::post('clientRegester', [UserController::class, 'clientSignUpProcess']);

// Route::get('getAnnouncements/{offset}', [AnnouncementsController::class, 'getAnnouncementsByLimit']);



// Route::post('addTeam', [GroupController::class, 'add_group_team']);

Route::post('createWallet', [WalletsController::class, 'Insert']);
// testing
// Route::get('printInvoiceTestTT', [PaymentsController::class, 'printInvoice']);



// Route::post('addCountries', [NewCountriesController::class, 'Insert']);


Route::post('forgetPassword', [ResetPasswordController::class, 'sendLinkResetPassword']);
Route::post('reset-password', [ResetPasswordController::class, 'resetPasswordCheck']);
Route::post('contactUS', [ContactUsController::class, 'Insert']);
Route::post('waitingList', [WaitingListController::class, 'Insert']);

Route::get('getCountries', [NewCountriesController::class, 'getCountries']);
Route::get('getCategories', [CategoriesController::class, 'getCategories']);
Route::get('getTimeDurations', [CategoriesController::class, 'getTimeDurations']);
Route::get('getAgencyTargets', [CategoriesController::class, 'getTargetCompanies']);
Route::get('getSectors', [CategoriesController::class, 'getSectors']);
Route::get('getCountryById/{id}', [NewCountriesController::class, 'getCountryById']);
Route::get('deleteMilestonesByProposalId/{id}', [Milestones::class, 'deleteMilestonesByProposalId']);
Route::get('getGroupNameAndImage/{id}', [GroupController::class, 'getGroupNameAndImage']);
Route::get('getQuestions', [FormOptionsController::class, 'getData']);
Route::get('getDemoLink', [ContentDataController::class, 'getDemoLink']);
Route::get('getTerms', [ContentDataController::class, 'getTerms']);
Route::get('getPriorities', [PriorityController::class, 'getPriorities']);
Route::get('getBudget', [CategoriesController::class, 'getBudget']);
Route::get('getHourlyRate', [CategoriesController::class, 'getHourlyRate']);
Route::get('getSeniority', [CategoriesController::class, 'getSeniority']);
Route::get('getSkill/{skill}', [SkillsController::class, 'search']);
Route::get('getLeadtime', [CategoriesController::class, 'getLeadtime']);
Route::get('getDeveloperHours', [CategoriesController::class, 'getDeveloperHours']);
Route::get('getServices', [CategoriesController::class, 'getServices']);
Route::get('getFeature/{feature}', [FeatureController::class, 'search']);

Route::post('addCountries', [NewCountriesController::class, 'Insert']);
Route::get('getPaymentSettlement', [CategoriesController::class, 'PaymentSettlement']);
Route::get('getTrialPeriod', [CategoriesController::class, 'TrialPeriod']);
Route::get('getResourceReplacement', [CategoriesController::class, 'ResourceReplacement']);
Route::get('getCancellationNoticePeriod', [CategoriesController::class, 'CancellationNoticePeriod']);


// Route::get('getSuggestedProjects/{agency_id}/{offset}', [ProjectController::class, 'suggestedProjects']);
Route::get('getNumberOfProjectForCompany/{id}', [ProjectController::class, 'getNumberOfProjectForCompany']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('getFinalProposalByProjectIdAndTeamId', [Final_proposals::class, 'getProposalByProjectIdAndTeamId']);



    // Route::post('createStripeUser', [PaymentController::class, 'createUserStripe']);
    Route::post('submitMilestone', [Milestones::class, 'submitMilestone']);
    Route::get('getClientInfo/{id}', [ClientController::class, 'get_client_info']);
    Route::get('getFreelancerInfo/{id}', [FreeLancerController::class, 'get_freelancer_info']);
    Route::get('getTeamInfo/{id}', [TeamController::class, 'get_team']);
    Route::get('getCompanyInfo/{id}', [CompanyController::class, 'getCompany']);

    Route::get('getAnnouncements/{offset}', [AnnouncementsController::class, 'getAnnouncementsByLimit']);
    Route::get('r/{token}', [InviteUsersController::class, 'getDataByToken']);
    Route::get('logout', [UserController::class, 'newLogout']);
    Route::post('UpdateUserInfo', [UserController::class, 'UpdateUserInfo']);
    Route::post('updateUserLinks', [UserLinksController::class, 'update_links']);
    Route::get('checkTokenExpiration', [UserController::class, 'checkTokenExpiration']);
    Route::post('updateUserCategories', [UserCategoriesController::class, 'updateUserCategories']);
    Route::post('updateAttachment', [UserAttachmentsController::class, 'update_attachment']);
    Route::post('joinWithCode', [InviteUsersController::class, 'joinGroupByCode']);
    Route::post('sendInvitation', [InviteUsersController::class, 'sendInvitation']);
    Route::post('acceptOrRefuseInvitation', [InviteUsersController::class, 'updateInvitation']);
    Route::post('removeUser', [GroupMembersController::class, 'removeUserFromGroup']);
    Route::get('project/{id}', [ProjectController::class, 'getProject']);
    Route::get('getFullFinalProposalById/{id}', [Final_proposals::class, 'getFullFinalProposalById']);
    Route::post('submitFinalProposal', [Final_proposals::class, 'submitFinalProposal']);
    // Route::get('getAllUsers', [UserController::class, 'getAllUsers']);
    // Route::post('saveImage', [ImagesController::class, 'Insert']);
    Route::get('updateTerms', [UserController::class, 'updateTerms']);
    Route::post('getMilestoneById', [Milestones::class, 'getMilestoneById']);
    Route::post('downloadSubmissionFile', [Milestones::class, 'downloadSubmissionFile']);
    Route::get('projectMilestons/{id}', [ProjectController::class, 'getProjectMilestones']);
    Route::post('printInvoice', [WalletsTransactionsController::class, 'printInvoice']);
    Route::post('printMilestoneInvoice', [Milestones::class, 'printMilestoneInvoice']);
    // new apis
    Route::get('projectInfo/{id}', [ProjectController::class, 'newGetProject']);
    Route::get('getProposals/{id}/{offset}/{limit}', [ProjectController::class, 'getInitailProposalsProjectId']);
    Route::get('getFinalProposals/{id}', [ProjectController::class, 'getFinalProposalsProjectId']);
    Route::get('getFinalProposals/{id}/{offset}/{limit}', [ProjectController::class, 'getFinalProposalsProjectId']);

});
Route::group(['middleware' => ['auth.isAgency', 'auth:sanctum']], function () {
    Route::post('addTeam', [GroupController::class, 'add_group_team']);
    Route::get('getTeamCategories/{id}', [GroupCategoriesController::class, 'getTeamCategories']);

    Route::post('addProposal', [Proposals::class, 'Insert']);
    Route::post('submitProposal', [HireDeveloperProposalsController::class, 'Insert']);
    Route::post('saveFinalProposal', [Final_proposals::class, 'saveFinalProposal']);
    Route::post('updateTeamCategories', [GroupCategoriesController::class, 'updateTeamCategories']);
    Route::post('updateFreelancerBio', [FreeLancerController::class, 'update_Bio']);
    Route::post('updateTools', [FreeLancerController::class, 'update_tools']);
    Route::post('updateGeneralInfo', [TeamController::class, 'updateGeneralInfo']);
    Route::post('updateTeamBio', [TeamController::class, 'updateTeamBio']);
    Route::post('updateTeamLink', [TeamController::class, 'updateLink']);
    Route::post('updateTeamLinks', [GroupsLinksController::class, 'updateTeamLinks']);
    Route::post('addFreelancerInfo', [FreeLancerController::class, 'Insert_freelancer']);
    Route::post('updateTeamImage', [TeamController::class, 'updateTeamImage']);
    Route::post('updateFreelancerImage', [FreeLancerController::class, 'updateFreelancerImage']);
    Route::get('getSuggestedProjects/{agency_id}/{offset}/{limit}', [ProjectController::class, 'suggestedProjects']);
    Route::get('getSuggestedProjects/{agency_id}', [ProjectController::class, 'suggestedProjects']);
    Route::post('exploreProject/{offset}/{limit}', [ProjectController::class, 'exploreProject']);
    Route::get('agencyPendingProjects/{agency_id}', [ProjectController::class, 'getAgencyPendingProjects']);
    Route::get('agencyPendingProjects/{agency_id}/{offset}/{limit}', [ProjectController::class, 'getAgencyPendingProjects']);
    Route::get('agencyActiveProjects/{agency_id}/', [ProjectController::class, 'getAgencyActiveProjects']);
    Route::get('agencyActiveProjects/{agency_id}/{offset}/{limit}', [ProjectController::class, 'getAgencyActiveProjects']);
    Route::get('getAgencyActiveProject/{id}', [ProjectController::class, 'getAgencyActiveProject']);
    Route::get('getAgencyPendingProject/{id}', [ProjectController::class, 'getAgencyPendingProject']);
    Route::post('addFinalProposal', [Final_proposals::class, 'Insert']);
    Route::get('getFinalProposalByProjectIdAndTeamId/{project_id}/{team_id}', [Final_proposals::class, 'getFinalProposalByProjectIdAndTeamId']);
    Route::post('updateMilestone', [Milestones::class, 'updateMilestone']);
    Route::post('addMilestone', [Milestones::class, 'Insert']);
    Route::post('deleteMilestone', [Milestones::class, 'deleteMilestone']);
    Route::get('getMilestonesByProposalId/{id}', [Milestones::class, 'getMilestones']);
    Route::post('addSubmissionLinks', [Milestones::class, 'addSubmissionLinks']);
    Route::post('addBillingInfo', [BillingInfoController::class, 'Insert']);
    Route::post('updateBillingInfo/{id}', [BillingInfoController::class, 'Update']);
    Route::get('billingInfo', [BillingInfoController::class, 'getBillingInfo']);
    Route::get('agencyTransactions/{offset}/{limit}', [WalletsTransactionsController::class, 'getAgencyTransactions']);
    Route::post('withdraw', [WithdrawlRequestController::class, 'Insert']);
    Route::get('withdrawRequests/{offset}/{limit}', [WithdrawlRequestController::class, 'getWithdrawlRequests']);
    //new apis
    Route::get('exploreProjects/{type}/{offset}/{limit}', [ProjectController::class, 'newExploreProject']);
    Route::post('addResource', [ResourcesController::class, 'Insert']);
    Route::post('updateResource', [ResourcesController::class, 'Update']);
    Route::post('deleteResource', [ResourcesController::class, 'Delete']);
    Route::post('getResources', [ResourcesController::class, 'contractResources']);
    Route::post('getContractResources', [ResourcesController::class, 'contractResources']);
    Route::post('addHireDeveloperFinalProposal', [HireDeveloperFinalProposalController::class, 'Insert']);
    Route::post('saveHireDeveloperFinalProposal', [HireDeveloperFinalProposalController::class, 'save']);
    Route::post('getHireDeveloperFinalProposal', [HireDeveloperFinalProposalController::class, 'getContract']);
    Route::post('submitContract', [HireDeveloperFinalProposalController::class, 'submitContract']);
    Route::post('getContractWithResources', [HireDeveloperFinalProposalController::class, 'getContractWithResources']);
    Route::post('checkIfValidToSubmit', [ResourcesController::class, 'checkIfValidToSubmit']);
});
Route::group(['middleware' => ['auth.isClient', 'auth:sanctum']], function () {
    Route::post('addCompany', [GroupController::class, 'add_group_company']);
    Route::post('updateClientBio', [ClientController::class, 'update_Bio']);
    Route::post('addClientInfo', [ClientController::class, 'Insert_client']);
    Route::post('createProject', [ProjectController::class, 'Insert']);
    Route::get('postedProjects/{company_id}/{offset}/{limit}', [ProjectController::class, 'getCompanyPendingProjects']);
    Route::get('postedProjectDetails/{project_id}/{company_id}', [ProjectController::class, 'getCompanyPendingProjectDetails']);
    Route::get('getProjectFinalProposalsById/{id}/{offset}/{limit}', [Final_proposals::class, 'getProjectProposalsById']);
    Route::get('getProjectProposalsById/{id}/{offset}/{limit}', [Proposals::class, 'getProjectProposalsById']);
    Route::get('getCompanyActiveProjects/{company_id}/{offset}/{limit}', [ProjectController::class, 'getCompanyActiveProjects']);
    Route::get('getCompanyActiveProjectDetails/{project_id}/{company_id}', [ProjectController::class, 'getCompanyActiveProjectDetails']);
    Route::post('updateCompanyBio', [CompanyController::class, 'updateCompanyBio']);
    Route::post('updateBasicInfo', [CompanyController::class, 'updateBasicInfo']);
    Route::post('updateLink', [CompanyController::class, 'updateLink']);
    Route::post('updateFieldSector', [CompanyController::class, 'updateFieldSector']);
    Route::post('updateCompanyImage', [CompanyController::class, 'updateCompanyImage']);
    Route::post('updateClientImage', [ClientController::class, 'updateClientImage']);
    Route::get('getFinalProposalById/{id}', [Final_proposals::class, 'getProposalDetailsById']);
    Route::post('acceptFinalProposal', [Final_proposals::class, 'acceptFinalProposal']);
    Route::post('rejectFinalProposal', [Final_proposals::class, 'rejectFinalProposal']);
    Route::post('rejectProposal', [proposals::class, 'rejectProposal']);
    Route::post('acceptProposal', [proposals::class, 'acceptProposal']);
    Route::post('reviseFinalProposal', [Final_proposals::class, 'reviseFinalProposal']);
    Route::get('dashboardProposals/{offset}/{limit}', [Proposals::class, 'getClientPropsals']);
    Route::post('payMilestone/', [PaymentsController::class, 'payMilestone']);
    Route::get('transactions/{offset}/{limit}', [WalletsTransactionsController::class, 'getCompanyTransactions']);
    Route::post('depositRequest', [DepositRequestController::class, 'Insert']);
    Route::get('payMilestoneDetails/{id}', [Milestones::class, 'payMilestoneDetails']);
    Route::post('acceptSubmission', [Milestones::class, 'acceptSubmission']);
    Route::post('reviewSubmission', [Milestones::class, 'reviseSubmission']);
    Route::get('deposits/{offset}/{limit}', [DepositRequestController::class, 'getDeposits']);
    Route::post('printDepositDeails', [DepositRequestController::class, 'printDepositDeails']);
    // new apis
    Route::post('acceptHireProposal', [HireDeveloperProposalsController::class, 'acceptProposal']);
    Route::post('rejectHireProposal', [HireDeveloperProposalsController::class, 'rejectProposal']);
    Route::get('allProjects', [ProjectController::class, 'getAllProjectsClient']);
    Route::post('rejectContract', [HireDeveloperFinalProposalController::class, 'rejectContract']);
    Route::post('reviewContract', [HireDeveloperFinalProposalController::class, 'reviewContract']);
    Route::post('acceptContract', [HireDeveloperFinalProposalController::class, 'acceptContract']);
    Route::get('getContractDetails/{contractId}', [HireDeveloperFinalProposalController::class, 'getContractWithResourcesClient']);
    Route::post('getHires', [ResourcesController::class, 'getHires']);

});
