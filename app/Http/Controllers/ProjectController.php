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
use App\Models\Group_member;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Controllers\Proposals;

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
            $project = Project::create($req->all() + ["company_id" => $userGroupInfo->group_id]);
            $project_id = $project->id;
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
            $response = Controller::returnResponse(500, 'There Is Error Occurred', $error);
            return json_encode($response);;
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

    function exploreProject(Request $req, $offset = 1)
    {
        $limit = 4;
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
            })->where('status', '=', 0)->distinct()->latest()->offset($page)->limit($limit)->get();
            // return $projects;
            $projectsData = $this->getProjectsInfo($projects);
            $response = Controller::returnResponse(200, "Data Found", $projectsData);
            return (json_encode($response));
        } catch (\Exception $error) {
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
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
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
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
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return (json_encode($response));
        }
    }
    private function getProjectsInfo($projects)
    {
        $projectCategoriesObj = new ProjectCategoriesController;
        foreach ($projects as $keyProj => &$project) {
            $project->company_name = Group::find($project->company_id)->name;
            $company_image =  Company::select('image')->where('group_id', $project->company_id)->get()->first()->image;
            $company_bio =  Company::select('bio')->where('group_id', $project->company_id)->get()->first()->bio;
            // dd($company_image);
            if (isset($company_image)) {
                $project->company_image = asset('images/companies/') . $company_image;
            } else {
                $project->company_image = asset('images/profile-pic.jpg');
            }
            $project->categories = $projectCategoriesObj->getProjectCategories($project->id);
            $project->company_bio = $company_bio;
            $project->duration = Category::find((int)$project->days)->name;
        }
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
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
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
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
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
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
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
            $team_id = Group_member::where("user_id", $userData->id)->select('group_id')
                ->first();
            $proposalsObj = new Proposals;
            $proposal = $proposalsObj->getProposalByProjectAndTeamId($projectData->id, $team_id->group_id);
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
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return (json_encode($response));
        }
    }
}
