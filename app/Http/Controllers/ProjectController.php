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
            $project = Project::create($req->except(['requirements_description']) + ["company_id" => $userGroupInfo->group_id]);
            $project_id = $project->id;
            $reqs=$requirementObj->Insert(json_decode($req->requirements_description),$project_id,$req->user_id);
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

    function exploreProject(Request $req, $offset = 1,$limit=4)
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
            })->where('status', '<', 1)->distinct()->latest()->offset($page)->limit($limit)->get();
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
                ->distinct()
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
        foreach ($projects as $keyProj => &$project) {
            $project->company_name = Group::find($project->company_id)->name;
            $company_image =  Company::select('image')->where('group_id', $project->company_id)->get()->first()->image;
            $company_bio =  Company::select('bio')->where('group_id', $project->company_id)->get()->first()->bio;
            $company_field_id =  Company::select('field')->where('group_id', $project->company_id)->get()->first()->field;
            $company_sector_id =  Company::select('sector')->where('group_id', $project->company_id)->get()->first()->sector;
            $user_info=json_decode($clientObj->get_client_info($project->user_id));
            $admin_info=array('first_name'=>$user_info->first_name,"role"=>$user_info->role);
            if (isset($user_info->image)) {
                $admin_info['image'] = asset("images/companies/" . $user_info->image);
            } else {
                $admin_info['image'] = asset('images/profile-pic.jpg');
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
            $project->company_field = Category::find((int)$company_field_id)->name;
            $project->company_sector = Category::find((int)$company_sector_id)->name;
            $project->requirments_description = $requirementsObj->getRequirementsByProjectId($project->id)->pluck('description')->toArray();
            $project->admin_info = $admin_info;
        return $projects;
    }
    function getAgencyPendingProjects($agency_id, $offset = 1)
    {
        $limit = 4;
        $page = ($offset - 1) * $limit;
        try {
            $projects = DB::table('projects')
                ->join('proposals', 'projects.id', '=', 'proposals.project_id')
                ->select('projects.*')
                ->where('proposals.team_id', '=', $agency_id)
                ->where('proposals.status', '<', 2)
                ->where('projects.status', '=', 0)
                ->distinct()
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

    function getAgencyActiveProjects($agency_id, $offset = 1)
    {
        $limit = 4;
        $page = ($offset - 1) * $limit;
        try {
            $projects = DB::table('projects')
                ->select('projects.*')
                ->where('projects.team_id', '=', $agency_id)
                ->where('projects.status', '=', 1)
                ->distinct()
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
        try {
            $projectData = $this->getProjectsInfo(Project::where('id', '=', $id)->get())->first();
            $projectData->final_proposal_id = DB::table('final_proposals')->select('id')->where('project_id', '=', $projectData->id)->where('status', '=', 1)->pluck('id')->first();
            if ($projectData->team_id == '' || $projectData->status < 1 || $projectData->final_proposal_id == '') {
                $response = Controller::returnResponse(500, "project is not active", []);
                return (json_encode($response));
            }
            $milestones = $milestonesObj->getMilestoneByProposalId($projectData->final_proposal_id);
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
            $proposal = $proposalsObj->getProposalByProjectAndTeamId($projectData->id, $team_id);
            $proposal_id = $proposal->id;
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

    function getCompanyPendingProjects(Request $req, $company_id, $offset = 1,$limit)
    {
        $userData = $req->user();
        $GroupControllerObj = new GroupController;
        $group_id = $GroupControllerObj->getGroupIdByUserId($userData->id);
        if ($group_id == $company_id) {

             
            $page = ($offset - 1) * $limit;
            try {
                $projects = DB::table('projects')
                    ->select('id', 'user_id', 'company_id', 'name', 'min', 'max', 'description', 'status', 'days', 'budget_type', 'created_at',)
                    ->where('projects.company_id', '=', $company_id)
                    ->where('projects.status', '=', 0)
                    ->distinct()
                    ->latest()->offset($page)->limit($limit)
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
    function getCompanyPendingProjectDetails(Request $req, $project_id,$company_id)
    {
        $userData = $req->user();
        $GroupControllerObj = new GroupController;
        $group_id = $GroupControllerObj->getGroupIdByUserId($userData->id);
        if ($group_id == $company_id) 
        {       try{
               $project= $this->getPendingProjectInfo($project_id);
               $response = Controller::returnResponse(200, "successful", $project);
               return (json_encode($response));
        }catch(Exception $error)
        {
            $response = Controller::returnResponse(500, "Something Wrong", $error->getMessage());
            return (json_encode($response));
        }
        } 
        else {
            $response = Controller::returnResponse(422, "You are trying to get another company data", []);
            return (json_encode($response));
        }
    }
    function getPendingProjectInfo($id)
    {
        $project=Project::find($id);
        $requirementsObj= new Requirement;
        $projectCategoriesObj = new ProjectCategoriesController;
        $clientControllersObj = new ClientController;
        $project->requirements=$requirementsObj->getRequirementsByProjectId($id);
        $project->categories = $projectCategoriesObj->getProjectCategories($id);
        $project->duration = Category::find((int)$project->days)->name;
        $user =json_decode($clientControllersObj->get_client_info((int)$project->user_id))->data;
        $final_ids=Final_proposal::where('project_id',$id);
        $no_finals=$final_ids->count(); 
        $init_ids=proposal::where('project_id',$id);
        $no_init=$init_ids->count();
        $project->final_proposals_number=$no_finals;
        $project->initial_proposals_number=$no_init;
        $project->admin=array(
            "id"=>$user->id,
            "first_name"=>$user->first_name,
            "last_name"=>$user->last_name,
            "role"=>$user->role,
            "image"=>$user->image,
        );
        return $project;
    }
}
