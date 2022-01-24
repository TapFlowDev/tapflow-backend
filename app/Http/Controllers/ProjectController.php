<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProjectCategoriesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupMembersController;
use App\Http\Controllers\GroupCategoriesController;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\projects_category;
use App\Models\Group;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;


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
        // try {
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
        // } catch (\Exception $error) {
        //     $responseData = $error;
        //     $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
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
            // dd($company_image);
            if (isset($company_image)) {
                $project->company_image = asset('images/companies/') . $company_image;
            } else {
                $project->company_image = asset('images/profile-pic.jpg');
            }
            $project->categories = $projectCategoriesObj->getProjectCategories($project->id);
            $project->duration = Category::find($project->id)->name;
        }
        return $projects;
    }
}
