<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProjectCategoriesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Milestones;
use App\Http\Controllers\GroupMembersController;
use App\Http\Controllers\GroupCategoriesController;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\projects_category;
use App\Models\Group;
use App\Models\Category;
use App\Models\Company;
use App\Models\proposal;
use App\Models\Final_proposal;
use App\Models\Group_member;
use App\Models\Agency_active_project;
use App\Models\Requirement as requirementModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Controllers\Proposals;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\Requirement;
use App\Http\Controllers\ClientController;
use App\Models\hire_developer_proposals;
use App\Models\Milestone;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Arr;
use App\Http\Controllers\CompanyController;
use GuzzleHttp\Handler\Proxy;


class ProjectController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        // $reqArray = $req->all();
        // // $testData = gettype($reqArray['priorities']);
        // return (json_encode($reqArray));
        try {

            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $reqArray = $req->all();
            $reqArray['user_id'] = $userData['user_id'];
            $projectResponse = $this->addProjectSignUp($reqArray, 1);
            if ($projectResponse['error']) {
                return json_encode($projectResponse['error']);
            }
            $responseData = array(
                "project_id" => $projectResponse['project']['id'],
            );
            $response = Controller::returnResponse(200, 'project created successfully', $responseData);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There Is Error Occurred', $error->getMessage());
            return json_encode($response);
        }
        // $userInfo = $userObj->getUserById($req->user_id);
        /*
        $rules = array(
            "user_id" => "required|exists:users,id",
            "name" => "required",
            "description" => "required",
            "requirements_description" => "required",
            "budget_type" => "required|gte:0|lt:2",
            "min" => "numeric",
            "max" => "numeric",
            "days" => "required|exists:categories,id",
            "needs" => "required",
            "design" => "required"
        );
        $userObj = new UserController;
        $groupMemberObj = new GroupMembersController;
        $requirementObj = new Requirement;
        $userInfo = $userObj->getUserById($req->user_id);
        $userGroupInfo = $groupMemberObj->getMemberInfoByUserId($req->user_id);
        if ($userGroupInfo == '') {
            $validation = array("type" => array(
                "user must have company to add project"
            ));
            $response = Controller::returnResponse(101, 'Validation Error', $validation);
            return json_encode($response);
        }
        $userPrivilege = $userGroupInfo->privileges;
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        }
        if ((int)$userInfo->type != 2 && (int)$userPrivilege != 1) {
            $validation = array("type" => array(
                "user cant add project"
            ));
            $response = Controller::returnResponse(101, 'Validation Error', $validation);
            return json_encode($response);
        }
        try {
            $ProjectCategoriesObj = new ProjectCategoriesController;
            // print_r($data);
            $project = Project::create($req->except(['requirements_description', 'needs', 'design']) + ["company_id" => $userGroupInfo->group_id, 'BA' => $req->needs, 'design' => $req->design]);
            $project_id = $project->id;
            $reqs = $requirementObj->Insert(json_decode($req->requirements_description), $project_id, $req->user_id);
            // if (!isset($req->postman)) {
            //     $postman = 0;
            // } else {
            //     $postman = 1;
            // }
            $cats = json_decode($req->categories);
            if (isset($cats)) {
                foreach ($cats as $key => $value) {
                    $categoryArr = array();
                    foreach ($value->subCat as $keySub => $subValue) {
                        $categoryArr[$keySub]['project_id'] = $project_id;
                        $categoryArr[$keySub]['category_id'] = $value->catId;
                        $categoryArr[$keySub]['sub_category_id'] = $subValue;
                    }
                    $add_cat = $ProjectCategoriesObj->addMultiRows($categoryArr);
                    if ($add_cat == 500) {
                        $delProject = Project::where('id', $project_id)->delete();
                        $response = Controller::returnResponse(500, 'add cast error', []);
                        return json_encode($response);
                    }
                }
            }
            // if (env('APP_ENV') !== 'local' && $postman < 1) {
            //     $cats = json_decode($req->categories);
            //     if (isset($cats)) {
            //         foreach ($cats as $key => $value) {
            //             $categoryArr = array();
            //             foreach ($value->subCat as $keySub => $subValue) {
            //                 $categoryArr[$keySub]['project_id'] = $project_id;
            //                 $categoryArr[$keySub]['category_id'] = $value->catId;
            //                 $categoryArr[$keySub]['sub_category_id'] = $subValue;
            //             }
            //             $add_cat = $ProjectCategoriesObj->addMultiRows($categoryArr);
            //             if ($add_cat == 500) {
            //                 $delProject = Project::where('id', $project_id)->delete();
            //                 $response = Controller::returnResponse(500, 'add cast error', []);
            //                 return json_encode($response);
            //             }
            //         }
            //     }
            // } else {
            //     $cats = $req->categories;
            //     if (isset($cats)) {
            //         foreach ($cats as $key => $value) {
            //             $categoryArr = array();
            //             foreach ($value['subCat'] as $keySub => $subValue) {
            //                 $categoryArr[$keySub]['project_id'] = $project_id;
            //                 $categoryArr[$keySub]['category_id'] = $value['catId'];
            //                 $categoryArr[$keySub]['sub_category_id'] = $subValue;
            //             }
            //             $add_cat = $ProjectCategoriesObj->addMultiRows($categoryArr);
            //             if ($add_cat == 500) {
            //                 $delProject = Project::where('id', $project_id)->delete();
            //                 $response = Controller::returnResponse(500, 'add cast error', []);
            //                 return json_encode($response);
            //             }
            //         }
            //     }
            // }

            $responseData = array(
                "project_id" => $project->id,
            );
            $response = Controller::returnResponse(200, 'project created successfully', $responseData);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There Is Error Occurred', $error->getMessage());
            return json_encode($response);
        }
        */
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }

    function exploreProject(Request $req, $offset = 1, $limit = 4)
    {

        $page = ($offset - 1) * $limit;
        try {


            $subCats = $req->subCats;
            $max = $req->max;
            $min = $req->min;
            $duration = $req->duration;
            if ($max < 1) {
                $max = null;
                $min = null;
            }

            $projects = Project::when($subCats, function ($query, $subCats) {
                $projectIds = projects_category::select('project_id')->whereIn('sub_category_id', $subCats)->distinct()->pluck('project_id')->toArray();
                return $query->whereIn('id', $projectIds);
            })->when($max, function ($query, $max) {
                return $query->where('max', '<=', $max);
            })->when($min, function ($query, $min) {
                return $query->where('min', '>=', $min);
            })->when($duration, function ($query, $duration) {
                return $query->whereIn('days', $duration);
            })->where('status', '<', 1)->where('verified', '=', 1)->distinct()->latest()->offset($page)->limit($limit)->get();
            // return $projects;
            $projectsCounter = Project::when($subCats, function ($query, $subCats) {
                $projectIds = projects_category::select('project_id')->whereIn('sub_category_id', $subCats)->distinct()->pluck('project_id')->toArray();
                return $query->whereIn('id', $projectIds);
            })->when($max, function ($query, $max) {
                return $query->where('max', '<=', $max);
            })->when($min, function ($query, $min) {
                return $query->where('min', '>=', $min);
            })->when($duration, function ($query, $duration) {
                return $query->whereIn('days', $duration);
            })->where('status', '<', 1)->where('verified', '=', 1)
                ->count();
            $projectsData = $this->getProjectsInfo($projects);

            $responseData = array('allData' => $projectsData, 'counter' => $projectsCounter);

            $response = Controller::returnResponse(200, "Data Found", $responseData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function suggestedProjects($agency_id, $offset = 1, $limit)
    {

        $page = ($offset - 1) * $limit;
        // try {
        $projects =  DB::table('projects_categories')
            ->join('groups_categories', 'projects_categories.sub_category_id', '=', 'groups_categories.sub_category_id')
            ->join('projects', 'projects_categories.project_id', '=', 'projects.id')
            ->select('projects.id', 'projects.id', 'projects.company_id', 'projects.name', 'projects.user_id', 'projects.budget_type', 'projects.min', 'projects.max', 'projects.description', 'projects.days', 'projects.created_at', 'projects.updated_at')
            ->where('groups_categories.group_id', '=', $agency_id)
            ->where('projects.status', '=', 0)
            ->where('verified', '=', 1)
            ->distinct()
            ->orderBy('updated_at', 'desc')
            ->latest()->offset($page)->limit($limit)
            ->get();
        $projectsCount =  DB::table('projects_categories')
            ->join('groups_categories', 'projects_categories.sub_category_id', '=', 'groups_categories.sub_category_id')
            ->join('projects', 'projects_categories.project_id', '=', 'projects.id')
            ->select('projects.id')
            ->where('groups_categories.group_id', '=', $agency_id)
            ->where('projects.status', '=', 0)
            ->where('verified', '=', 1)
            ->distinct()
            ->get();
        $projectsCounter = $projectsCount->count();
        $projectsData = $this->getProjectsInfo($projects);


        $responseData = array('allData' => $projectsData, 'counter' => $projectsCounter);

        $response = Controller::returnResponse(200, "Data Found", $responseData);
        return (json_encode($response));
        // } catch (\Exception $error) {
        //     $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
        //     return (json_encode($response));
        // }

        // ->pluck('project_id');

        // if(count($projects)<1){
        //     $projects = DB::table('projects_categories')
        //     ->join('groups_categories', 'projects_categories.category_id', '=', 'groups_categories.category_id')
        //     ->join('projects', 'projects_categories.project_id', '=', 'projects.id')
        //     ->select('projects.id', 'projects.id' , 'projects.company_id' , 'projects.name', 'projects.budget_type', 'projects.min', 'projects.max', 'projects.description', 'projects.requirements_description', 'projects.days')->distinct()
        //     ->where('groups_categories.group_id', '=', $agency_id)
        //     ->where('projects.status', '=', 0)
        //     ->latest()
        //     ->get();
        //     // ->pluck('project_id');
        // }
        // $projects = array_column($projects, 'project_id');
        //    DB::table('projects_categories')->select('project_id')->distinct()
        //     ->whereIn('category_id', [1])
        //     ->get();
        //DB::table('groups_categories')->select('project_id')->distinct()->where('group_id', $agency_id)->get()
        // return $projectsData;
    }
    function getProject($id)
    {
        try {
            $project = Project::where('id', $id)->get();
            $projectInfo = $this->getProjectsInfo($project)->first();
            $response = Controller::returnResponse(200, "data found", $projectInfo);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function getProjectsInfo($projects)
    {
        $projectCategoriesObj = new ProjectCategoriesController;
        $requirementsObj = new Requirement;
        $clientObj = new ClientController;
        $countriesObj = new NewCountriesController;

        foreach ($projects as $keyProj => &$project) {
            $project->company_name = Group::find($project->company_id)->name;
            $companyAdminId = Group_member::where('group_id', '=', $project->company_id)->where('privileges', '=', 1)->first()->user_id;
            $companyAdminEmail = User::select('email')->where('id', '=', $companyAdminId)->first()->email;
            $project->company_email = $companyAdminEmail;
            $company_image =  Company::select('image')->where('group_id', $project->company_id)->get()->first()->image;
            $company_bio =  Company::select('bio')->where('group_id', $project->company_id)->get()->first()->bio;
            $company_field_id =  Company::select('field')->where('group_id', $project->company_id)->get()->first()->field;
            $company_sector_id =  Company::select('sector')->where('group_id', $project->company_id)->get()->first()->sector;
            $company_country_id =  Company::select('country')->where('group_id', $project->company_id)->get()->first()->country;
            $user_info = json_decode($clientObj->get_client_info($project->user_id));


            $admin_info = array('first_name' => $user_info->data->first_name, "role" => $user_info->data->role);
            if (isset($user_info->image)) {
                $admin_info['image'] = asset("images/companies/" . $user_info->image);
            } else {
                $admin_info['image'] = asset('images/profile-pic.jpg');
            }
            if ($company_field_id != '' && $company_field_id != null) {
                $project->company_field = Category::find((int)$company_field_id)->name;
            }
            if ($company_sector_id != '' && $company_sector_id != null) {
                $project->company_sector = Category::find((int)$company_sector_id)->name;
            }
            if ($company_country_id != '' && $company_country_id != null) {
                $countriesData = $countriesObj->getCountryFlag($company_country_id);
                $project->company_country_flag = $countriesData->flag;
                $project->company_country_name = $countriesData->name;
                $project->company_country_code = $countriesData->code;
            }

            // dd($company_image);
            if (isset($company_image)) {
                $project->company_image = asset("images/companies/" . $company_image);
            } else {
                $project->company_image = asset('images/profile-pic.jpg');
            }
            $project->categories = $projectCategoriesObj->getProjectCategories($project->id);
            $project->company_bio = $company_bio;
            $project->duration = Category::find((int)$project->days)->name;
            $project->requirments_description = $requirementsObj->getRequirementsByProjectId($project->id)->pluck('description')->toArray();
            $project->requirmentsDetails = $requirementsObj->getRequirementsAndHourlyRateByProjectId($project->id);
            $project->admin_info = $admin_info;
        }
        return $projects;
    }
    function getAgencyPendingProjects($agency_id, $offset = 1, $limit)
    {

        $page = ($offset - 1) * $limit;
        try {

            // $initProjects =Proposal::where('team_id',$agency_id)->select('project_id','status')->offset($page)->limit($limit)
            // ->distinct()->get();
            // $projects_id=$initProjects->pluck('project_id')->toArray();
            // $status=1;
            // $projects = DB::table('projects')
            // ->leftJoin('final_proposals','final_proposals.project_id','=','projects.id')
            //     ->select('projects.*', 'final_proposals.status as final_proposal_status')
            // ->whereIn('projects.id',$projects_id)
            //     ->where('final_proposals.status','<>',1)
            //     ->orderBy('updated_at', 'desc')
            //     ->offset($page)->limit($limit)
            //     ->distinct()
            //     ->get();
            // $projects=DB::table('projects')
            // ->join('proposals','proposals.project_id' ,'=','projects.id')
            // ->where('proposals.team_id', '=', $agency_id)
            // ->select('projects.*', 'proposals.status as proposal_status', 'proposals.team_id as agency_id')
            // ->orderBy('updated_at', 'desc')
            // ->latest()->offset($page)->limit($limit)
            // ->distinct()
            // ->get();
            $projects1 = DB::table('projects')
                ->join('proposals', 'proposals.project_id', '=', 'projects.id')
                ->select('projects.*', 'proposals.status as proposal_status')
                ->where('proposals.team_id', '=', $agency_id)
                ->where('projects.status', '<>', 1)
                ->where('projects.status', '<>', 4)
                // ->orderBy('updated_at', 'desc')
                ->offset($page)->limit($limit)
                ->distinct()
                ->get();
            // $projects2 = DB::table('projects')
            // ->join('final_proposals', 'final_proposals.project_id', '=', 'projects.id')
            // ->select('projects.id as project_id','final_proposals.status as final_proposal_status')
            // ->where('final_proposals.team_id', '=', $agency_id)
            // ->where('final_proposals.status','<>',1)
            // // ->orderBy('updated_at', 'desc')
            // ->offset($page)->limit($limit)
            // ->distinct()
            // ->get();
            //     // // print_r(['project11'=> $projects1]);
            $projectIds1 = $projects1->pluck('project_id')->toArray();
            //    $projectIds2=$projects2->pluck('project_id')->toArray();
            $projects2 = DB::table('projects')
                ->leftJoin('final_proposals', function ($join) {
                    $join->on('projects.id', '=', 'final_proposals.project_id')

                        ->where('final_proposals.status', '<>', 1);
                })
                ->select('projects.*', 'final_proposals.team_id as agency_id', 'final_proposals.status as final_proposal_status')
                ->whereIn('projects.id', $projectIds1)
                ->where('final_proposals.team_id', '=', $agency_id)
                // ->where('final_proposals.status','!=',1)
                // ->orderBy('updated_at', 'desc')
                ->offset($page)->limit($limit)
                ->distinct()
                ->get();
            // // print_r(['project22'=> $projects2]);
            $project1Counter = DB::table('projects')
                ->join('proposals', 'proposals.project_id', '=', 'projects.id')
                ->select('projects.*', 'proposals.status as proposal_status')
                ->where('proposals.team_id', '=', $agency_id)
                ->where('projects.status', '<>', 1)
                ->where('projects.status', '<>', 4)
                ->distinct()
                ->get();
            $projects2Counter = DB::table('projects')
                ->leftJoin('final_proposals', function ($join) {
                    $join->on('projects.id', '=', 'final_proposals.project_id')

                        ->where('final_proposals.status', '<>', 1);
                })
                ->select('projects.*', 'final_proposals.team_id as agency_id', 'final_proposals.status as final_proposal_status')
                ->whereIn('projects.id', $projectIds1)
                ->where('final_proposals.team_id', '=', $agency_id)
                ->distinct()
                ->get();
            $projectsCounter = (int)$projects2Counter->count() + (int)$project1Counter->count();
            $projects = array_merge($projects1->toArray(), $projects2->toArray());
            // $projects=['init'=>$projectIds1,'final'=>$projectIds2];

            // $response = Controller::returnResponse(200, "data found", $projects);
            // return (json_encode($response));


            $projectInfo = $this->getProjectsInfo2($projects, $agency_id);
            $responseData = array('allData' => $projectInfo, 'counter' => $projectsCounter);

            $response = Controller::returnResponse(200, "data found", $responseData);
            return (json_encode($response));
        } catch (\Exception $error) {

            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }

    function getAgencyActiveProjects($agency_id, $offset = 1, $limit)
    {

        $page = ($offset - 1) * $limit;
        try {
            $projects = DB::table('projects')
                ->select('projects.*')
                ->where('projects.team_id', '=', $agency_id)
                ->whereIn('projects.status', [1, 4])
                ->distinct()
                ->orderBy('updated_at', 'desc')
                ->offset($page)->limit($limit)
                ->get();
            $projectsCounter = DB::table('projects')
                ->select('projects.*')
                ->where('projects.team_id', '=', $agency_id)
                ->whereIn('projects.status', [1, 4])
                ->count();

            $projectInfo = $this->getProjectsInfo($projects);
            $responseData = array('allData' => $projectInfo, 'counter' => $projectsCounter);
            $response = Controller::returnResponse(200, "data found", $responseData);
            return (json_encode($response));
        } catch (\Exception $error) {

            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getAgencyActiveProject($id)
    {
        $milestonesObj = new Milestones;
        $finalProposalsObj = new Final_proposals;
        $teamControllersObj = new TeamController;
        try {
            $projectData = $this->getProjectsInfo(Project::where('id', '=', $id)->get())->first();
            $projectData->final_proposal_id = DB::table('final_proposals')->select('id')->where('project_id', '=', $projectData->id)->where('status', '=', 1)->pluck('id')->first();
            if ($projectData->team_id == '' || $projectData->status < 1 || $projectData->final_proposal_id == '') {
                $response = Controller::returnResponse(500, "project is not active", []);
                return (json_encode($response));
            }
            $milestones = $milestonesObj->getMilestoneByProposalId($projectData->final_proposal_id);
            $contract = $finalProposalsObj->getProposalById($projectData->final_proposal_id);

            $admins = DB::table('group_members')
                ->join('users', 'group_members.user_id', '=', 'users.id')
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.role')
                ->where('users.deleted', '=', 0)
                ->where('users.status', '=', 1)
                ->where('group_members.group_id', '=', $projectData->company_id)
                ->where('group_members.privileges', '=', 1)
                ->get();
            foreach ($admins as &$admin) {
                $userData = DB::table('clients')->where('user_id', $admin->id)->get()->first();
                if (isset($userData->image)) {
                    $admin->image =  asset('images/users/' . $userData->image);
                } else {
                    $admin->image  = asset('images/profile-pic.jpg');
                }
            }
            $team = $teamControllersObj->get_team_info($contract->team_id);
            if (isset($team->image)) {
                $team->image = asset("images/companies/" . $team->image);
            } else {
                $team->image = asset('images/profile-pic.jpg');
            }
            $projectData->admins = $admins;
            $projectData->milestones = $milestones;
            $projectData->contract = $contract;
            $projectData->contract->agency_info = $team;
            $response = Controller::returnResponse(200, "data found", $projectData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getAgencyPendingProject(Request $req, $id)
    {
        $userData = $req->user();

        try {
            $projectData = $this->getProjectsInfo(Project::where('id', '=', $id)->get())->first();
            if ($projectData->team_id != '' || $projectData->status > 0) {
                $response = Controller::returnResponse(500, "project is not pending", []);
                return (json_encode($response));
            }

            $GroupControllerObj = new GroupController;
            $team_id = $GroupControllerObj->getGroupIdByUserId($userData->id);
            $proposalsObj = new Proposals;
            $FProposalsObj = new Final_proposals;
            $proposal = $proposalsObj->getProposalByProjectAndTeamId($projectData->id, $team_id);
            $FProposal = $FProposalsObj->checkIfExists($projectData->id, $team_id);

            $proposal_id = $proposal->id;
            $proposal_status = $proposal->status;
            if ($FProposal['exist'] == 1) {
                $final_proposal_type = $FProposal['type'];
                $final_proposal_status = $FProposal['status'];
                $projectData->final_proposal_type = $final_proposal_type;
                $projectData->final_proposal_status = $final_proposal_status;
            } else {
                $projectData->final_proposal_type = '0';
                $projectData->final_proposal_status = null;
            }
            $admins = DB::table('group_members')
                ->join('users', 'group_members.user_id', '=', 'users.id')
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.role')
                ->where('users.deleted', '=', 0)
                ->where('users.status', '=', 1)
                ->where('group_members.group_id', '=', $projectData->company_id)
                ->where('group_members.privileges', '=', 1)
                ->get();
            foreach ($admins as &$admin) {
                $userData = DB::table('clients')->where('user_id', $admin->id)->get()->first();
                if (isset($userData->image)) {
                    $admin->image =  asset('images/users/' . $userData->image);
                } else {
                    $admin->image  = asset('images/profile-pic.jpg');
                }
            }
            $projectData->proposal_id = $proposal_id;
            $projectData->proposal_status = $proposal_status;
            $projectData->admins = $admins;

            $response = Controller::returnResponse(200, "data found", $projectData);
            return (json_encode($response));
        } catch (\Exception $error) {

            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getNumberOfProjectForCompany($id)
    {
        $projects = DB::table('projects')
            ->where('company_id', $id)
            ->select('id')->get();
        $numberOfProjects = $projects->count();
        return ($numberOfProjects);
    }

    function getCompanyPendingProjects(Request $req, $company_id, $offset = 1, $limit)
    {
        $userData = $req->user();
        $GroupControllerObj = new GroupController;
        $group_id = $GroupControllerObj->getGroupIdByUserId($userData->id);
        if ($group_id == $company_id) {


            $page = ($offset - 1) * $limit;
            try {
                $projects = DB::table('projects')
                    ->select('*')
                    ->where('projects.company_id', '=', $company_id)
                    ->where('projects.status', '=', 0)
                    ->orderBy('updated_at', 'desc')
                    ->latest()->offset($page)->limit($limit)
                    ->distinct()
                    ->get();
                $projectsCounter = DB::table('projects')
                    ->select('*')
                    ->where('projects.company_id', '=', $company_id)
                    ->where('projects.status', '=', 0)
                    ->distinct()
                    ->get();

                $projectInfo = $this->getProjectsDetails($projects);
                $responseData = array('allData' => $projectInfo, 'counter' => $projectsCounter->count());
                $response = Controller::returnResponse(200, "data found", $responseData);
                return (json_encode($response));
            } catch (\Exception $error) {

                $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
                return (json_encode($response));
            }
        } else {
            $response = Controller::returnResponse(422, "You are trying to get another company data", []);
            return (json_encode($response));
        }
    }
    function getProjectsDetails($projects)
    {

        foreach ($projects as $keyProj => &$project) {
            $project->duration = Category::find((int)$project->days)->name;
            $initial_proposals =  proposal::where('project_id', $project->id)->where('status', '!=', 1)->select('id')->get();
            $final_proposals =  Final_proposal::where('project_id', $project->id)->where('status', '!=', -1)->where('status', '!=', 1)->select('id')->get();
            $project->initial_proposal_number = count($initial_proposals);
            $project->final_proposal_number = count($final_proposals);
        }
        return $projects;
    }
    function getCompanyPendingProjectDetails(Request $req, $project_id, $company_id)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['exist'] == 1) {
                if ($userData['group_id'] == $company_id) {
                    $isExist = $this->ifExist($project_id);
                    if ($isExist['exist'] == 1) {
                        $project = $this->getPendingProjectInfo($project_id);
                        $response = Controller::returnResponse(200, "successful", $project);
                        return (json_encode($response));
                    } else {
                        $response = Controller::returnResponse(422, "this project does not exist", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "trying to get data for another company", []);
                    return (json_encode($response));
                }
            } else {
                $response = Controller::returnResponse(422, "the user does not have company", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "Something Wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getPendingProjectInfo($id)
    {
        $project = Project::find($id);
        $requirementsObj = new Requirement;
        $projectCategoriesObj = new ProjectCategoriesController;
        $clientControllersObj = new ClientController;
        $project->requirements = $requirementsObj->getRequirementsByProjectId($id);
        $project->categories = $projectCategoriesObj->getProjectCategories($id);
        $project->duration = Category::find((int)$project->days)->name;
        $user = json_decode($clientControllersObj->get_client_info((int)$project->user_id))->data;
        $final_ids = Final_proposal::where('project_id', $id)->where('status', '!=', -1)->where('status', '!=', 1);
        $no_finals = $final_ids->count();
        $init_ids = proposal::where('project_id', $id)->where('status', '!=', 1);
        $no_init = $init_ids->count();
        $project->final_proposals_number = $no_finals;
        $project->initial_proposals_number = $no_init;
        $project->admin = array(
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "role" => $user->role,
            "image" => $user->image,
        );
        return $project;
    }
    function getCompanyActiveProjects(Request $req, $company_id, $offset, $limit)
    {
        $userData = $req->user();
        try {
            $page = ($offset - 1) * $limit;
            $GroupControllerObj = new GroupController;
            $group_id = $GroupControllerObj->getGroupIdByUserId($userData->id);
            if ($group_id == $company_id) {
                $projects = DB::table('projects')
                    ->where('company_id', '=', $company_id)
                    ->where(function ($query) {
                        $query->where('status', '=', 1)->orWhere('status', '=', 4);
                    })
                    ->select('projects.*')
                    ->distinct()
                    ->orderBy('updated_at', 'desc')
                    ->latest()->offset($page)->limit($limit)
                    ->get();
                $projectsCounter = DB::table('projects')
                    ->where('company_id', '=', $company_id)
                    ->where(function ($query) {
                        $query->where('status', '=', 1)->orWhere('status', '=', 4);
                    })
                    ->select('projects.*')
                    ->distinct()
                    ->get();


                $projects_info = $this->getCompanyActiveProjectsInfo($projects);
                $responseData = array('allData' => $projects_info, 'counter' => $projectsCounter->count());
                $response = Controller::returnResponse(200, "successful", $responseData);
                return (json_encode($response));
            } else {
                $response = Controller::returnResponse(422, "You are trying to get another company data", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function getCompanyActiveProjectsInfo($projects)
    {
        foreach ($projects as $keyProj => &$project) {
            $project->agency_name = Group::find($project->team_id)->name;
            $agency_image =  Team::select('image')->where('group_id', $project->team_id)->get()->first()->image;
            $project->company_name = Group::find($project->company_id)->name;
            $company_image =  Company::select('image')->where('group_id', $project->company_id)->get()->first()->image;
            if (isset($agency_image)) {
                $project->agency_image = asset("images/companies/" . $agency_image);
            } else {
                $project->agency_image = asset('images/profile-pic.jpg');
            }
            if (isset($company_image)) {
                $project->company_image = asset("images/companies/" . $company_image);
            } else {
                $project->company_image = asset('images/profile-pic.jpg');
            }
        }
        return $projects;
    }
    function getCompanyActiveProjectDetails(Request $req, $project_id, $company_id)
    {
        $userData = $req->user();
        try {
            $GroupControllerObj = new GroupController;
            $group_id = $GroupControllerObj->getGroupIdByUserId($userData->id);
            if ($group_id == $company_id) {
                $project_info = $this->getCompanyActiveProjectDetailsInfo($project_id);
                $response = Controller::returnResponse(200, "successful", $project_info);
                return (json_encode($response));
            } else {
                $response = Controller::returnResponse(422, "You are trying to get another company data", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function getCompanyActiveProjectDetailsInfo($id)
    {
        $project = Project::where('id', $id)->first();
        $requirementsObj = new Requirement;
        $projectCategoriesObj = new ProjectCategoriesController;
        $teamControllersObj = new TeamController;
        $freelancersControllersObj = new FreeLancerController;
        $finalProposalControllersObj = new Final_proposals;

        $project->requirements = $requirementsObj->getRequirementsByProjectId($id);
        $project->categories = $projectCategoriesObj->getProjectCategories($id);
        $project->duration = Category::find((int)$project->days)->name;
        // $user_id =$proposalsControllersObj->getProposalByProjectAndTeamId((int)$project->id,$project->team_id)->user_id;

        $team = $teamControllersObj->get_team_info($project->team_id);
        // $Exist=$finalProposalControllersObj->checkIfExists($project->team_id,$id);
        // if($Exist['exist']==1){
        $final_proposal = $finalProposalControllersObj->getProposalDetailsByProject_id($id);
        $project->contract = $final_proposal;



        $user = json_decode($freelancersControllersObj->get_freelancer_info($final_proposal->user_id))->data;
        // if (isset($user->image)) {
        //     $user->image = asset("images/users/" . $user->image);
        // } else {
        //     $user->image = asset('images/profile-pic.jpg');
        // }
        if (isset($team->image)) {
            $team->image = asset("images/companies/" . $team->image);
        } else {
            $team->image = asset('images/profile-pic.jpg');
        }

        $project->admin = array(
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "role" => $user->role,
            "image" => $user->image,
        );
        $project->agency_info = array(
            "id" => $team->id,
            "name" => $team->name,
            "image" => $user->image,
        );
        return $project;
        // }
        // else{
        //      $project->contract = 'no contract';
        //     if (isset($team->image)) {
        //         $team->image = asset("images/companies/" . $team->image);
        //     } else {
        //         $team->image = asset('images/profile-pic.jpg');
        //     }

        //     $project->agency_info = array(
        //         "id" => $team->id,
        //         "name" => $team->name,

        //     );
        //     return $project;}
    }
    private function ifExist($id)
    {
        $project = DB::table('projects')
            ->where('id', $id)
            ->first();
        if ($project === null) {
            return ['exist' => 0];
        } else {
            return ['exist' => 1, 'project' => $project];
        }
    }
    function getProjectCompanyId($id)
    {
        $company_id = Project::where('id', $id)->select('company_id')->first();
        return ($company_id->company_id);
    }

    function getProjectMilestones(Request $req, $id)
    {
        try {
            $milestonesObj = new Milestones;
            $userData = $this->checkUser($req);
            $project = Project::where('id', $id)->get();
            $finalProposal = Final_proposal::where('project_id', '=', $id)->where('status', '=', '1')->first();
            //check if project is not empty
            if (!$project->first()) {
                $response = Controller::returnResponse(401, "unauthrized", []);
                return (json_encode($response));
            }
            if (!$finalProposal) {
                $response = Controller::returnResponse(401, "unauthrized", []);
                return (json_encode($response));
            }
            $projectInfo = $this->getProjectsInfo($project)->first();
            if ($userData['type'] == 2) {
                if ($projectInfo->company_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthrized", []);
                    return (json_encode($response));
                }
            } elseif ($userData['type'] == 1) {
                if ($projectInfo->team_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthrized", []);
                    return (json_encode($response));
                }
            }
            $milestonesInfo = Milestone::where('project_id', $id)->where('final_proposal_id', '=', $finalProposal->id)->get()->makeHidden(['deliverables', 'created_at', 'updated_at']);
            $remainingAmount = number_format(Milestone::where('project_id', $id)->where('final_proposal_id', '=', $finalProposal->id)->where('is_paid', '<>', 1)->sum('price'), 2, '.', '');
            $remainingMilestones = Milestone::where('project_id', $id)->where('final_proposal_id', '=', $finalProposal->id)->where('status', '<>', 3)->count();
            $responseData = array(
                'projectInfo' => $projectInfo,
                'milestones' => $milestonesInfo,
                'remainingAmount' => $remainingAmount,
                'remainingMilestones' => $remainingMilestones
            );
            $response = Controller::returnResponse(200, "data found", $responseData);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function getProjectsInfo2($projects, $agency_id)
    {

        $projectCategoriesObj = new ProjectCategoriesController;
        $requirementsObj = new Requirement;
        $clientObj = new ClientController;
        $finalPropObj = new Final_proposals;
        $initPropObj = new Proposals;
        $countriesObj = new NewCountriesController;

        foreach ($projects as $keyProj => &$project) {
            $project->company_name = Group::find($project->company_id)->name;
            $companyAdminId = Group_member::where('group_id', '=', $project->company_id)->where('privileges', '=', 1)->first()->user_id;
            $companyAdminEmail = User::select('email')->where('id', '=', $companyAdminId)->first()->email;
            $project->company_email = $companyAdminEmail;
            $company_image =  Company::select('image')->where('group_id', $project->company_id)->get()->first()->image;
            $company_bio =  Company::select('bio')->where('group_id', $project->company_id)->get()->first()->bio;
            $company_field_id =  Company::select('field')->where('group_id', $project->company_id)->get()->first()->field;
            $company_sector_id =  Company::select('sector')->where('group_id', $project->company_id)->get()->first()->sector;
            $company_country_id =  Company::select('country')->where('group_id', $project->company_id)->get()->first()->country;

            $user_info = json_decode($clientObj->get_client_info($project->user_id));
            $finalProp = $finalPropObj->checkIfExists($project->id, $agency_id);
            $initProp = $initPropObj->checkIfProposalExists($project->id, $agency_id);
            $proposal_status = $initProp['status'];
            if ($finalProp['exist'] == 1) {
                $finalStatus = $finalProp['status'];
            } else {
                $finalStatus = null;
            }
            $admin_info = array('first_name' => $user_info->data->first_name, "role" => $user_info->data->role);
            if (isset($user_info->image)) {
                $admin_info['image'] = asset("images/companies/" . $user_info->image);
            } else {
                $admin_info['image'] = asset('images/profile-pic.jpg');
            }
            if ($company_field_id != '' && $company_field_id != null) {
                $project->company_field = Category::find((int)$company_field_id)->name;
            }
            if ($company_sector_id != '' && $company_sector_id != null) {
                $project->company_sector = Category::find((int)$company_sector_id)->name;
            }
            if ($company_country_id != '' && $company_country_id != null) {
                $countriesData = $countriesObj->getCountryFlag($company_country_id);
                $project->company_country_flag = $countriesData->flag;
                $project->company_country_name = $countriesData->name;
                $project->company_country_code = $countriesData->code;
            }

            // dd($company_image);
            if (isset($company_image)) {
                $project->company_image = asset("images/companies/" . $company_image);
            } else {
                $project->company_image = asset('images/profile-pic.jpg');
            }
            $project->categories = $projectCategoriesObj->getProjectCategories($project->id);
            $project->company_bio = $company_bio;
            $project->duration = Category::find((int)$project->days)->name;
            $project->requirments_description = $requirementsObj->getRequirementsByProjectId($project->id)->pluck('description')->toArray();
            $project->admin_info = $admin_info;
            $project->final_proposal_status =  $finalStatus;
            $project->proposal_status =   $proposal_status;
        }
        return $projects;
    }

    function addProjectSignUp($req, $insert = 0)
    {
        // return ($req['requirements_description']);

        $userObj = new UserController;
        $groupMemberObj = new GroupMembersController;
        $requirementObj = new Requirement;
        $ProjectCategoriesObj = new ProjectCategoriesController;
        $projectPriortyObj = new ProjectPriorityController;
        $skillsObj = new ProjectSkillsController;
        $projectServicesObj = new ProjectServiceController;
        $returnData['error'] = [];
        $returnData['project'] = [];


        $userInfo = $userObj->getUserById($req['user_id']);
        $userGroupInfo = $groupMemberObj->getMemberInfoByUserId($req['user_id']);
        if ($insert == 1) {

            $rules = array(
                "user_id" => "required|exists:users,id",
                "name" => "required",
                // "description" => "required",
                "requirements_description" => "required",
                "budget_type" => "required|gte:0|lt:4",
                "min" => "numeric",
                "max" => "numeric",
                "days" => "required|exists:categories,id",
                "needs" => "required",
                "design" => "required",
                "type" => "required|gt:0|lt:4",
                "start_project" => "required|exists:categories,id",
            );

            $validator = Validator::make($req, $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response['error'] = Controller::returnResponse(101, "Validation Error", $responseData);
                return $response;
            }
            // if ($req['type'] == 3) {
            //     if (count($req['skills']) > 3) {
            //         $responseData = array(
            //             'skills' => 'skills must be less than 4'
            //         );
            //         $response['error'] = Controller::returnResponse(101, "Validation Error", $responseData);
            //         return $response;
            //     }
            //     if (count($req['skills']) < 1) {
            //         $responseData = array(
            //             'skills' => 'skills are required'
            //         );
            //         $response['error'] = Controller::returnResponse(101, "Validation Error", $responseData);
            //         return $response;
            //     }
            // }
        }

        try {
            // return $req;
            $req['company_id'] = $userGroupInfo->group_id;
            $projectArr = $req;
            unset($projectArr['requirements_description']);
            unset($projectArr['categories']);
            unset($projectArr['category']);
            unset($projectArr['skills']);
            unset($projectArr['priorities']);
            unset($projectArr['services']);
            $project = Project::create($projectArr);
            $project_id = $project->id;
            $requirementsDescriptionArr = array();
            if ((int)$req['type'] == 3) {
                $requirementsDescriptionArr = $req['requirements_description'];
            } else {
                foreach ($req['requirements_description'] as $keyR => $valR) {
                    $requirementsDescriptionArr[] = $valR['name'];
                }
                $services = $projectServicesObj->Insert($project_id, $req['services']);
            }
            $reqs = $requirementObj->Insert($requirementsDescriptionArr, $project_id, $req['user_id']);
            $priority = $projectPriortyObj->Insert($project_id, $req['priorities']);

            // if ($req['type'] == 3) {
            //     $skills = $skillsObj->Insert(($req['skills']), $project_id);
            // }
            // if ($priority['status'] == 500) {
            //     $responseData = $priority['msg'];
            //     $response['error']  = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            //     return $response;
            // }
            $categoryArr = array();
            $categoryArr['project_id'] = $project_id;
            $categoryArr['category_id'] = $req['category'];
            $categoryArr['sub_category_id'] = 0;
            projects_category::insert($categoryArr);
            // $cats = $req['categories'];
            // if (isset($cats)) {
            //     foreach ($cats as $key => $value) {
            //         $categoryArr = array();
            //         $categoryArr[$key]['project_id'] = $project_id;
            //         $categoryArr[$key]['category_id'] = $value['catId'];
            //         $categoryArr[$key]['sub_category_id'] = 0;
            //         // foreach ($value['subCat'] as $keySub => $subValue) {
            //         //     $categoryArr[$keySub]['project_id'] = $project_id;
            //         //     $categoryArr[$keySub]['category_id'] = $value['catId'];
            //         //     $categoryArr[$keySub]['sub_category_id'] = $subValue;
            //         // }
            //         $add_cat = $ProjectCategoriesObj->addMultiRows($categoryArr);
            //         if ($add_cat == 500) {
            //             // $delProject = Project::where('id', $project_id)->delete();
            //             $response['error']  = Controller::returnResponse(500, "add cat error", []);
            //             return $response;
            //         }
            //     }
            // }
            $returnData['project'] = $project->toArray();
            return $returnData;
            /* 
            $cats = json_decode($req['categories']);
            if (isset($cats)) {
                foreach ($cats as $key => $value) {
                    $categoryArr = array();
                    foreach ($value->subCat as $keySub => $subValue) {
                        $categoryArr[$keySub]['project_id'] = $project_id;
                        $categoryArr[$keySub]['category_id'] = $value->catId;
                        $categoryArr[$keySub]['sub_category_id'] = $subValue;
                    }
                    $add_cat = $ProjectCategoriesObj->addMultiRows($categoryArr);
                    if ($add_cat == 500) {
                        $delProject = Project::where('id', $project_id)->delete();
                        $response['error']  = Controller::returnResponse(500, "add cat error", []);
                        return $response;
                    }
                }
            }
            */

            // if (env('APP_ENV') !== 'local' && $postman < 1) {
            //     $cats = json_decode($req->categories);
            //     if (isset($cats)) {
            //         foreach ($cats as $key => $value) {
            //             $categoryArr = array();
            //             foreach ($value->subCat as $keySub => $subValue) {
            //                 $categoryArr[$keySub]['project_id'] = $project_id;
            //                 $categoryArr[$keySub]['category_id'] = $value->catId;
            //                 $categoryArr[$keySub]['sub_category_id'] = $subValue;
            //             }
            //             $add_cat = $ProjectCategoriesObj->addMultiRows($categoryArr);
            //             if ($add_cat == 500) {
            //                 $delProject = Project::where('id', $project_id)->delete();
            //                 $response = Controller::returnResponse(500, 'add cast error', []);
            //                 return json_encode($response);
            //             }
            //         }
            //     }
            // } else {
            //     $cats = $req->categories;
            //     if (isset($cats)) {
            //         foreach ($cats as $key => $value) {
            //             $categoryArr = array();
            //             foreach ($value['subCat'] as $keySub => $subValue) {
            //                 $categoryArr[$keySub]['project_id'] = $project_id;
            //                 $categoryArr[$keySub]['category_id'] = $value['catId'];
            //                 $categoryArr[$keySub]['sub_category_id'] = $subValue;
            //             }
            //             $add_cat = $ProjectCategoriesObj->addMultiRows($categoryArr);
            //             if ($add_cat == 500) {
            //                 $delProject = Project::where('id', $project_id)->delete();
            //                 $response = Controller::returnResponse(500, 'add cast error', []);
            //                 return json_encode($response);
            //             }
            //         }
            //     }
            // }

            // $responseData = array(
            //     "project_id" => $project->id,
            // );
            // $response = Controller::returnResponse(200, 'project created successfully', $responseData);
            // return json_encode($response);
        } catch (Exception $error) {
            $responseData = $error->getMessage();
            $response['error']  = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return $response;
        }
    }
    function getCompanyInfoByProjectId($project_id)
    {
        $company_obj=new CompanyController;
        $company_id=Project::where('id',$project_id)->select('company_id')->first()->company_id;
        return $company_obj-> get_company_info ($company_id);
    }
    function getCompanyProjectAdmin($project_id)
    {
        return Project::where('id',$project_id)->select('user_id')->first()->user_id;
    }

    function newExploreProject(Request $req, $type = 3, $offset = 1, $limit = 4)
    {
        $userData = $this->checkUser($req);
        $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
        if (!$condtion) {
            $response = Controller::returnResponse(401, "unauthorized user", []);
            return (json_encode($response));
        }
        try {
            $page = ($offset - 1) * $limit;

            $conditionArray = [
                ['type', '=', $type],
                ['verified', '=', 1],
                ['visible', '=', 1],
            ];
            if ($type != 3) {
                $conditionArray[] = ['status', '<', 1];
            }
            if ($type < 2) {
                $conditionArray2 = [
                    ['type', '=', 0],
                    ['verified', '=', 1],
                    ['visible', '=', 1],
                ];
                $projects = Project::where($conditionArray)->orWhere($conditionArray2)->distinct()->latest()->offset($page)->limit($limit)->get();
                $projectsCounter = Project::where($conditionArray)->orWhere($conditionArray2)->count();
            } else {
                $projects = Project::where($conditionArray)->distinct()->latest()->offset($page)->limit($limit)->get();
                $projectsCounter = Project::where($conditionArray)->count();
            }
            // return $projects;
            $projectsData = $this->newGetProjectsInfo($projects);

            $responseData = array('allData' => $projectsData, 'counter' => $projectsCounter);

            $response = Controller::returnResponse(200, "Data Found", $responseData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function newGetProjectsInfo($projects, $groupId = 0, $groupType = 0)
    {
        $projectCategoriesObj = new ProjectCategoriesController;
        $requirementsObj = new Requirement;
        $clientObj = new ClientController;
        $countriesObj = new NewCountriesController;

        foreach ($projects as $keyProj => &$project) {
            $project->company_name = Group::find($project->company_id)->name;
            $companyAdminId = Group_member::where('group_id', '=', $project->company_id)->where('privileges', '=', 1)->first()->user_id;
            $companyAdminEmail = User::select('email')->where('id', '=', $companyAdminId)->first()->email;
            $project->company_email = $companyAdminEmail;
            $company_image =  Company::select('image')->where('group_id', $project->company_id)->get()->first()->image;
            $company_bio =  Company::select('bio')->where('group_id', $project->company_id)->get()->first()->bio;
            $company_field_id =  Company::select('field')->where('group_id', $project->company_id)->get()->first()->field;
            $company_sector_id =  Company::select('sector')->where('group_id', $project->company_id)->get()->first()->sector;
            $company_country_id =  Company::select('country')->where('group_id', $project->company_id)->get()->first()->country;
            $user_info = json_decode($clientObj->get_client_info($project->user_id));
            $admin_info = array('first_name' => $user_info->data->first_name, "role" => $user_info->data->role);
            if (isset($user_info->image)) {
                $admin_info['image'] = asset("images/companies/" . $user_info->image);
            } else {
                $admin_info['image'] = asset('images/profile-pic.jpg');
            }
            // if ($company_field_id != '' && $company_field_id != null) {
            //     $project->company_field = Category::find((int)$company_field_id)->name;
            // }
            // if ($company_sector_id != '' && $company_sector_id != null) {
            //     $project->company_sector = Category::find((int)$company_sector_id)->name;
            // }
            if ($company_country_id != '' && $company_country_id != null) {
                $countriesData = $countriesObj->getCountryFlag($company_country_id);
                $project->company_country_flag = $countriesData->flag;
                $project->company_country_name = $countriesData->name;
                $project->company_country_code = $countriesData->code;
            }

            // dd($company_image);
            if (isset($company_image)) {
                $project->company_image = asset("images/companies/" . $company_image);
            } else {
                $project->company_image = asset('images/profile-pic.jpg');
            }
            $duration = Category::find((int)$project->days);
            $budget = Category::find((int)$project->budget_id);
            $startProject = Category::find((int)$project->start_project);
            $project->duration = ($duration ? $duration->name : "unset");
            $project->budget = ($budget ? $budget->name : "$$project->min - $$project->max");
            $project->startProject = ($startProject ? $startProject->name : "unset");
            $project->company_bio = $company_bio;
            $project->admin_info = $admin_info;
            $project->requirments_description = $requirementsObj->getRequirementsByProjectId($project->id)->pluck('description')->toArray();
            $project->requirments = $requirementsObj->getRequirementsAlldataByProjectId($project->id);
            if ($project->type == 3) {
                $requirmentsDetails = $requirementsObj->getHireDevRequirmentData($project->id);
                $project->requirmentsDetails = $requirmentsDetails['reqArr'];
                $project->requirmentsSkills = $requirmentsDetails['skills'];
                $hireDeveloperProposalsObj = new HireDeveloperProposalsController;
                $hireDeveloperFinalProposalsObj = new HireDeveloperFinalProposalController;
                if ($groupId > 0 && $groupType == 1) {
                    // hire developer final obj 
                    $initProp = $hireDeveloperProposalsObj->checkIfProposalExists($project->id, $groupId);
                    $proposal_status = $initProp['status'];
                    $project->proposal_status = $proposal_status;
                    $finalStatus = 0;
                    $finalExists = 0;
                    if (isset($initProp['proposal_id'])) {
                        $finalProp = $hireDeveloperFinalProposalsObj->checkIfExists($initProp['proposal_id'], $groupId);
                        $finalStatus = $finalProp['status'];
                        $finalExists = $finalProp['exist'];
                    }
                    $project->final_proposal_status =  $finalStatus;
                    $project->final_proposal_exist =  $finalExists;
                    
                    $project->proposal_id = (isset($initProp['proposal_id']) ? $initProp['proposal_id'] : null);
                    $project->final_proposal_id = (isset($finalProp['final_proposal_id']) ? $finalProp['final_proposal_id'] : null);
                    $progressArray = array(
                        "apply" => $initProp['exist'],
                        "discuss" => 0,
                        "contract" => $finalStatus,
                        "onboard" => 0,
                    );
                    $project->progressArray = $progressArray;
                } elseif ($groupId > 0 && $groupType == 2) {
                    $projectAgencyMatchObj = new ProjectAgencyMatchController;
                    $initPropCount = $hireDeveloperProposalsObj->getCountByProjectId($project->id);
                    $finalPropCount = $hireDeveloperProposalsObj->getContractsCountByProjectId($project->id);
                    $hiresPropCount = $hireDeveloperProposalsObj->getHiresCountByProjectId($project->id);
                    $agencyMatchCount = $projectAgencyMatchObj->getMatchCountByProjectId($project->id);
                    $progressArray = array(
                        "matches" => $agencyMatchCount,
                        "proposals" => $initPropCount,
                        "contracts" => $finalPropCount,
                        "hires" => $hiresPropCount,
                    );
                    $project->progressArray = $progressArray;
                }
            } else {
                $project->categories = $projectCategoriesObj->getProjectCategories($project->id);
                if ($groupId > 0 && $groupType == 1) {
                    $finalPropObj = new Final_proposals;
                    $initPropObj = new Proposals;
                    $finalProp = $finalPropObj->checkIfExists($project->id, $groupId);
                    $initProp = $initPropObj->checkIfProposalExists($project->id, $groupId);
                    $proposal_status = $initProp['status'];
                    $finalStatus = $finalProp['status'];
                    // if ($finalProp['exist'] == 1) {
                    // } else {
                    //     $finalStatus = 0;
                    // }
                    $project->final_proposal_status =  $finalStatus;
                    $project->proposal_status =   $proposal_status;
                    $project->proposal_id = (isset($initProp['proposal_id']) ? $initProp['proposal_id'] : null);
                    $project->final_proposal_id = (isset($finalProp['final_proposal_id']) ? $finalProp['final_proposal_id'] : null);
                    $progressArray = array(
                        "apply" => $initProp['exist'],
                        "discuss" => 0,
                        "contract" => $finalStatus,
                        "onboard" => 0,
                    );
                    $project->progressArray = $progressArray;
                } elseif ($groupId > 0 && $groupType == 2) {
                    $projectAgencyMatchObj = new ProjectAgencyMatchController;
                    $finalPropObj = new Final_proposals;
                    $initPropObj = new Proposals;
                    $milestonesObj = new Milestones;
                    $initPropCount = $initPropObj->getCountByProjectId($project->id);
                    $finalPropCount = $finalPropObj->getCountByProjectId($project->id);
                    $milestonesCount = $milestonesObj->getCountByProjectId($project->id);
                    $agencyMatchCount = $projectAgencyMatchObj->getMatchCountByProjectId($project->id);
                    $progressArray = array(
                        "matches" => $agencyMatchCount,
                        "proposals" => $initPropCount,
                        "contracts" => $finalPropCount,
                        "milestones" => $milestonesCount,
                    );
                    $project->progressArray = $progressArray;
                }
            }
        }
        return $projects;
    }
    function newGetProject(Request $req, $id)
    {
        try {
            $userData = $this->checkUser($req);
            $project = Project::where('id', $id)->get();
            $projectInfo = $this->newGetProjectsInfo($project, $userData['group_id'], $userData['type'])->first();
            $response = Controller::returnResponse(200, "data found", $projectInfo);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getInitailProposalsProjectId(Request $req, $id, $offset = 0, $limit = 4)
    {
        try {
            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            if ($userData['type'] == 1) {
                $agencyId = $userData['group_id'];
                $project = Project::where('id', '=', $id)->first();
            } else {
                $agencyId = 0;
                $project = Project::where('id', '=', $id)->where('company_id', '=', $userData['group_id'])->first();
            }
            $page = ($offset - 1) * $limit;

            if (!$project) {
                $response = Controller::returnResponse(422, 'Project does not exsist', []);
                return (json_encode($response));
            }

            if ($project->type == 3) {
                $hireDeveloperProposalsObj = new HireDeveloperProposalsController;
                $proposals = $hireDeveloperProposalsObj->getProposalsByProjectIdTeamId($id, $agencyId, $page, $limit);
            } else {
                $proposalsObj = new Proposals;
                $proposals = $proposalsObj->getProposalsByProjectId($id, $agencyId, $page, $limit);
            }
            $response = Controller::returnResponse(200, "data found", $proposals);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getAllProjectsClient(Request $req)
    {
        try {
            $userData = $this->checkUser($req);
            $project = Project::where('company_id', $userData['group_id'])->latest()->get();
            $projectInfo = $this->newGetProjectsInfo($project, $userData['group_id'], $userData['type']);
            $response = Controller::returnResponse(200, "data found", $projectInfo);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getFinalProposalsProjectId(Request $req, $id, $offset = 0, $limit = 0)
    {
        try {
            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            if ($userData['type'] == 1) {
                $agencyId = $userData['group_id'];
                $project = Project::where('id', '=', $id)->first();
            } else {
                $agencyId = 0;
                $project = Project::where('id', '=', $id)->where('company_id', '=', $userData['group_id'])->first();
            }
            $page = ($offset - 1) * $limit;

            if (!$project) {
                $response = Controller::returnResponse(422, 'Project does not exsist', []);
                return (json_encode($response));
            }

            if ($project->type == 3) {
                $hireDeveloperFinalProposalsObj = new HireDeveloperFinalProposalController;
                $hireDeveloperProposalsObj = new HireDeveloperProposalsController;
                $proposalIds = $hireDeveloperProposalsObj->getAcceptedProposalByProjectId($id, $agencyId);
                if (count($proposalIds) < 1) {
                    $response = Controller::returnResponse(200, "data found", []);
                    return (json_encode($response));
                }
                $finalproposals = $hireDeveloperFinalProposalsObj->getContractData($proposalIds, $agencyId, $page, $limit);
            } else {
                $finalproposalsObj = new Final_proposals;
                if ($agencyId > 0) {
                    $proposalObj = new Proposals;
                    $init_proposal = $proposalObj->getProposalInfo($id, $agencyId);
                    if ($init_proposal['exist'] != 1) {
                        $response = Controller::returnResponse(422, 'you do not have initial proposal ', []);
                        return json_encode($response);
                    }
                }

                $finalproposals = $finalproposalsObj->newGetFinalProposalByProjectIdAndTeamId($id, $agencyId,  $page, $limit);
            }
            $response = Controller::returnResponse(200, "data found", $finalproposals);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getAgencyProjects(Request $req, $offset = 0, $limit = 4)
    {
        try {
            $userData = $this->checkUser($req);
            $page = ($offset - 1) * $limit;
            $initialFinalHireSelect = ['projects.*', 'hire_developer_proposals.id as proposal_id', 'hire_developer_proposals.status as initial_status', 'hire_developer_final_proposals.id as contract_id', 'hire_developer_final_proposals.status as final_status'];
            $initialFinalSelect = ['projects.*', 'proposals.id as proposal_id', 'proposals.status as initial_status', 'final_proposals.id as contract_id', 'final_proposals.status as final_status'];
            // $initialProposals = proposal::select('id', 'project_id', 'status as initial_status')->where('team_id', '=', $userData['group_id'])->get();
            // $initialProposalsHireDevelopers = hire_developer_proposals::select('id', 'project_id', 'status as initial_status')->where('team_id', '=', $userData['group_id'])->get();
            $initailFinalHire = DB::table('hire_developer_proposals')
                ->leftJoin('hire_developer_final_proposals', 'hire_developer_proposals.id', '=', 'hire_developer_final_proposals.proposal_id')
                ->join('projects', 'projects.id', '=', 'hire_developer_proposals.project_id')
                ->select($initialFinalHireSelect)
                ->where('hire_developer_proposals.team_id', '=', $userData['group_id'])
                ->get();

            $initailFinal = DB::table('proposals')
                ->leftJoin('final_proposals', 'proposals.id', '=', 'final_proposals.proposal_id')
                ->join('projects', 'projects.id', '=', 'proposals.project_id')
                ->select($initialFinalSelect)
                ->where('proposals.team_id', '=', $userData['group_id'])
                ->get();
            $allProposals = $initailFinalHire->merge($initailFinal);
            $allProjects = $allProposals->sortDesc()->splice($page, $limit);
            $projectsCounter = $allProposals->count();
            $projectInfo = $this->newGetProjectsInfo($allProjects, $userData['group_id'], $userData['type']);
            // $projectIds = $allProposals->pluck('project_id')->toArray();
            // sort($projectIds);
            // return $projectInfo;
            $returnData = [
                'allData' => $projectInfo,
                'count' => $projectsCounter
            ];
            $response = Controller::returnResponse(200, "data found", $returnData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function newAgencyActiveProjects(Request $req, $offset = 0, $limit = 4)
    {
        try {
            $userData = $this->checkUser($req);
            $page = ($offset - 1) * $limit;
            $agencyActiveProjectIds = Agency_active_project::select('project_id')->where('group_id', '=', $userData['group_id'])->pluck('project_id')->toArray();
            // return $agencyActiveProjectIds;
            $projects = DB::table('projects')
                ->select('projects.*')
                ->where(function ($query) use ($userData, $agencyActiveProjectIds) {
                    $query->where('projects.team_id', '=', $userData['group_id'])
                        ->orWhereIn('projects.id', $agencyActiveProjectIds);
                })
                ->whereIn('projects.status', [1, 4])
                ->distinct()
                ->orderBy('updated_at', 'desc')
                ->offset($page)->limit($limit)
                ->get();
            // return $projects;
            $projectsCounter = DB::table('projects')
                ->select('projects.*')
                ->where(function ($query) use ($userData, $agencyActiveProjectIds) {
                    $query->where('projects.team_id', '=', $userData['group_id'])
                        ->orWhereIn('projects.id', $agencyActiveProjectIds);
                })
                ->whereIn('projects.status', [1, 4])
                ->count();
            $projectInfo = $this->newGetProjectsInfo($projects, $userData['group_id'], $userData['type']);

            $returnData = [
                'allData' => $projectInfo,
                'count' => $projectsCounter
            ];
            $response = Controller::returnResponse(200, "data found", $returnData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function newAgencyPendingProjects(Request $req, $offset = 0, $limit = 4)
    {
        try {
            $userData = $this->checkUser($req);
            $page = ($offset - 1) * $limit;
            $initialFinalHireSelect = ['projects.*', 'hire_developer_proposals.id as proposal_id', 'hire_developer_proposals.status as initial_status', 'hire_developer_final_proposals.id as contract_id', 'hire_developer_final_proposals.status as final_status'];
            $initialFinalSelect = ['projects.*', 'proposals.id as proposal_id', 'proposals.status as initial_status', 'final_proposals.id as contract_id', 'final_proposals.status as final_status'];
            $agencyActiveProjectIds = Agency_active_project::select('project_id')->where('group_id', '=', $userData['group_id'])->pluck('project_id')->toArray();

            // $initialProposals = proposal::select('id', 'project_id', 'status as initial_status')->where('team_id', '=', $userData['group_id'])->get();
            // $initialProposalsHireDevelopers = hire_developer_proposals::select('id', 'project_id', 'status as initial_status')->where('team_id', '=', $userData['group_id'])->get();
            $initailFinalHire = DB::table('hire_developer_proposals')
                ->leftJoin('hire_developer_final_proposals', 'hire_developer_proposals.id', '=', 'hire_developer_final_proposals.proposal_id')
                ->join('projects', 'projects.id', '=', 'hire_developer_proposals.project_id')
                ->select($initialFinalHireSelect)
                ->where('hire_developer_proposals.team_id', '=', $userData['group_id'])
                ->whereNotIn('projects.id', $agencyActiveProjectIds)
                ->get();

            $initailFinal = DB::table('proposals')
                ->leftJoin('final_proposals', 'proposals.id', '=', 'final_proposals.proposal_id')
                ->join('projects', 'projects.id', '=', 'proposals.project_id')
                ->select($initialFinalSelect)
                ->where('proposals.team_id', '=', $userData['group_id'])
                ->where('projects.status', '=', 0)
                ->get();
            $allProposals = $initailFinalHire->merge($initailFinal);
            $projectsCounter = $allProposals->count();
            $allProjects = $allProposals->sortDesc()->splice($page, $limit);
            $projectInfo = $this->newGetProjectsInfo($allProjects, $userData['group_id'], $userData['type']);
            // $projectIds = $allProposals->pluck('project_id')->toArray();
            // sort($projectIds);
            // return $projectInfo;
            $returnData = [
                'allData' => $projectInfo,
                'count' => $projectsCounter
            ];
            $response = Controller::returnResponse(200, "data found", $returnData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
}
