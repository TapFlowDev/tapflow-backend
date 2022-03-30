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
use App\Models\Requirement as requirementModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Controllers\Proposals;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\Requirement;
use App\Http\Controllers\ClientController;
use App\Models\Milestone;
use App\Models\Team;

class ProjectController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $rules = array(
            "user_id" => "required|exists:users,id",
            "name" => "required",
            "description" => "required",
            "requirements_description" => "required",
            "budget_type" => "required|gte:0|lt:2",
            "min" => "numeric|multiple_of:5",
            "max" => "numeric|multiple_of:5",
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
            $projectsData = $this->getProjectsInfo($projects);
            $response = Controller::returnResponse(200, "Data Found", $projectsData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }
    }
    function suggestedProjects($agency_id, $offset = 1)
    {
        $limit = 4;
        $page = ($offset - 1) * $limit;
        try {
            $projects =  DB::table('projects_categories')
                ->join('groups_categories', 'projects_categories.sub_category_id', '=', 'groups_categories.sub_category_id')
                ->join('projects', 'projects_categories.project_id', '=', 'projects.id')
                ->select('projects.id', 'projects.id', 'projects.company_id', 'projects.name', 'projects.budget_type', 'projects.min', 'projects.max', 'projects.description', 'projects.requirements_description', 'projects.days', 'projects.created_at')
                ->where('groups_categories.group_id', '=', $agency_id)
                ->where('projects.status', '=', 0)
                ->where('verified', '=', 1)
                ->distinct()
                ->orderBy('updated_at', 'desc')
                ->latest()->offset($page)->limit($limit)
                ->get();
            $projectsData = $this->getProjectsInfo($projects);
            $responseData = $projectsData;
            $response = Controller::returnResponse(200, "Data Found", $responseData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error->getMessage());
            return (json_encode($response));
        }

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
        $final_proposalObj=new Final_proposals;
        foreach ($projects as $keyProj => &$project) {
            $project->company_name = Group::find($project->company_id)->name;
            $company_image =  Company::select('image')->where('group_id', $project->company_id)->get()->first()->image;
            $company_bio =  Company::select('bio')->where('group_id', $project->company_id)->get()->first()->bio;
            $company_field_id =  Company::select('field')->where('group_id', $project->company_id)->get()->first()->field;
            $company_sector_id =  Company::select('sector')->where('group_id', $project->company_id)->get()->first()->sector;
            $user_info = json_decode($clientObj->get_client_info($project->user_id));
            $finalProp=$final_proposalObj->checkIfExists($project->id,$project->agency_id);
            $arr=[$keyProj,$finalProp['exist']];
            if($finalProp['exist'] ==1)
            {
                array_push($arr,$finalProp['status']);
                $final_status=$finalProp['status'];
                if($finalProp ['status'] == 1){
                    unset($projects[$keyProj]);
                   }
            }
            else{
                array_push($arr,$finalProp['status']);
                $final_status=null;}
           
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
            $project->final_proposal_status = $final_status;
        }
        dd($arr);
        return $projects;
    }
    function getAgencyPendingProjects($agency_id, $offset = 1,$limit)
    {
        
        $page = ($offset - 1) * $limit;
        try {
            // $projects = DB::table('proposals')
            //     ->join('final_proposals', function ($join) {
            //         $join->on('proposals.id', '=', 'final_proposals.proposal_id')
            //             ->where('final_proposals.status', '!=', 1);
                       
            //     })
            //     ->join('projects', 'proposals.project_id', '=', 'projects.id')
            //     ->select('projects.*', 'proposals.status as proposal_status', 'final_proposals.status as final_proposal_status')
            //     ->where('proposals.team_id', '=', $agency_id)
            //     ->orderBy('updated_at', 'desc')
            //     ->latest()->offset($page)->limit($limit)
            //     ->distinct()
            //     ->get();
                $projects=DB::table('projects')
                ->join('proposals','proposals.project_id' ,'=','projects.id')
                ->where('proposals.team_id', '=', $agency_id)
                ->select('projects.*', 'proposals.status as proposal_status', 'proposals.team_id as agency_id')
                ->orderBy('updated_at', 'desc')
                ->latest()->offset($page)->limit($limit)
                ->distinct()
                ->get();
                //  $projects1 = DB::table('projects')
                // ->join('proposals', 'proposals.project_id', '=', 'projects.id')
                // ->select('projects.*', 'proposals.status as proposal_status')
                // ->where('proposals.team_id', '=', $agency_id)
                // ->orderBy('updated_at', 'desc')
                // ->latest()->offset($page)->limit($limit)
                // ->distinct()
                // ->get();
                // $projects2 = DB::table('projects')
                // ->join('final_proposals', 'final_proposals.project_id', '=', 'projects.id')
                // ->select('projects.*', 'final_proposals.status as final_proposals_status')
                // ->where('final_proposals.team_id', '=', $agency_id)
                // ->orderBy('updated_at', 'desc')
                // ->latest()->offset($page)->limit($limit)
                // ->distinct()
                // ->get();
               
            $projectInfo = $this->getProjectsInfo($projects);
            $response = Controller::returnResponse(200, "data found", $projectInfo);
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
                ->where('projects.status', '=', 1)
                ->orWhere('projects.status', '=', 4)
                ->distinct()
                ->orderBy('updated_at', 'desc')
                ->latest()->offset($page)->limit($limit)
                ->get();

            $projectInfo = $this->getProjectsInfo($projects);
            $response = Controller::returnResponse(200, "data found", $projectInfo);
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
            $projectData->admins = $admins;
            $projectData->milestones = $milestones;
            $projectData->contract = $contract;
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

                $projectInfo = $this->getProjectsDetails($projects);
                $response = Controller::returnResponse(200, "data found", $projectInfo);
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
            $initial_proposals =  proposal::where('project_id', $project->id)->select('id')->get();
            $final_proposals =  Final_proposal::where('project_id', $project->id)->select('id')->get();
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
        $final_ids = Final_proposal::where('project_id', $id)->where('status', '!=', -1);
        $no_finals = $final_ids->count();
        $init_ids = proposal::where('project_id', $id);
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
                $projects_info = $this->getCompanyActiveProjectsInfo($projects);
                $response = Controller::returnResponse(200, "successful", $projects_info);
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
        // try {
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
        // }catch(Exception $error)
        // {
        //     $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
        //     return (json_encode($response));
        // }
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
            //check if project is not empty
            if (!$project->first()) {
                $response = Controller::returnResponse(401, "unauthrized", []);
                return (json_encode($response));
            }
            $projectInfo = $this->getProjectsInfo($project)->first();
            if ($projectInfo->company_id != $userData['group_id']) {
                $response = Controller::returnResponse(401, "unauthrized", []);
                return (json_encode($response));
            }
            $milestonesInfo = Milestone::where('project_id', $id)->get()->makeHidden(['deliverables', 'created_at', 'updated_at']);
            $remainingAmount = number_format(Milestone::where('project_id', $id)->where('is_paid', '<>', 1)->sum('price'), 2, '.', '');
            $remainingMilestones = Milestone::where('project_id', $id)->where('status', '<>', 3)->count();
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
}
