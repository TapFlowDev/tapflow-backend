<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\resources;
use App\Models\hire_developer_final_proposal;
use Exception;
use App\Models\hire_developer_proposals;
use Illuminate\Support\Facades\DB;
use App\Models\Project;


class ResourcesController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            }
            $rules = array(
                "job_function" => "required",
                "name" => "required",
                "starting_date" => "required",
                "hours" => "required|numeric",
                "rate" => "required|numeric",
                "contract_id" => "required|exists:hire_developer_final_proposals,id"
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            } else {
                $team_id = hire_developer_final_proposal::where('id', $req->contract_id)->select('team_id')->first()->team_id;
                if ($team_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthorized", []);
                    return (json_encode($response));
                }
                if(str_contains($req->end_date, 'N')){
                    $endDate= NULL;
                }else{
                    $endDate = $req->end_date;
                }
                $resource = resources::create($req->except(['end_date', 'image']) + ['end_date'=> $endDate]);
                if ($req->hasFile('image')) {
                    $destPath = 'images/users';
                    $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
                    $img = $req->image;

                    $img->move(public_path($destPath), $imageName);
                    $this->updateFiles($resource->id, $imageName, 'image');
                }
                $response = Controller::returnResponse(200, "success", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    //update row according to row id
    function Update(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $rules = array(
                    "resource_id" => "required",
                    "job_function" => "required",
                    "name" => "required",
                    "starting_date" => "required",
                    "hours" => "required|numeric",
                    // "end_date" => "required|date|date_format:Y-m-d",
                    "rate" => "required|numeric",
                );
                $validators = Validator::make($req->all(), $rules);
                if ($validators->fails()) {
                    $responseData = $validators->errors();
                    $response = Controller::returnResponse(101, "Validation Error", $responseData);
                    return (json_encode($response));
                }
                $contract_id = resources::where('id', $req->resource_id)->select('contract_id')->first()->contract_id;
                $team_id = hire_developer_final_proposal::where('id', $contract_id)->select('team_id')->first()->team_id;
                if ($team_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthorized", []);
                    return (json_encode($response));
                }

                if(str_contains($req->end_date, 'N')){
                    $req->end_date =NULL;
                }
                resources::where('id', $req->resource_id)->update([
                    'name' => $req->name,
                    'job_function' => $req->job_function,
                    'rate' => $req->rate,
                    'hours' => $req->hours,
                    'starting_date' => $req->starting_date,
                    'end_date' => $req->end_date,
                ]);
                if ($req->hasFile('image')) {
                    $destPath = 'images/users';
                    $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
                    $img = $req->image;

                    $img->move(public_path($destPath), $imageName);
                    $this->updateFiles($req->resource_id, $imageName, 'image');
                }
                $response = Controller::returnResponse(200, "updated successfully", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    //delete row according to row id
    function Delete(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $rules = array(
                    "resource_id" => "required",
                );
                $validators = Validator::make($req->all(), $rules);
                if ($validators->fails()) {
                    $responseData = $validators->errors();
                    $response = Controller::returnResponse(101, "Validation Error", $responseData);
                    return (json_encode($response));
                }
                $contract_id = resources::where('id', $req->resource_id)->select('contract_id')->first()->contract_id;
                $team_id = hire_developer_final_proposal::where('id', $contract_id)->select('team_id')->first()->team_id;
                if ($team_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthorized", []);
                    return (json_encode($response));
                }
                resources::where('id', $req->resource_id)->delete();
                $response = Controller::returnResponse(200, "deleted successfully", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function internalAdd($data, $contract_id)
    {

        foreach ($data as $resource) {
            $array = array(
                "contract_id" => $contract_id,
                "job_function" => $resource['skill'],
                "rate" => $resource['hourlyRate'],
                "starting_date" => date("Y-m-d")
            );
            resources::create($array);
        }
    }
    function contractResources(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $team_id = hire_developer_final_proposal::where('id', $req->contract_id)->select('team_id')->first()->team_id;
                if ($team_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthorized", []);
                    return (json_encode($response));
                }
                $resources = resources::where('contract_id', $req->contract_id)->get();
                foreach ($resources as $resource) {
                    if ($resource->image != '') {
                        $image = asset('images/users/' . $resource->image);
                        $resource->image = $image;
                    } else {
                        $resource->image = asset('images/profile-pic.jpg');
                    }
                }
                $response = Controller::returnResponse(200, "successful", $resources);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function updateFiles($resource_id, $imageName, $filedName)
    {
        resources::where('id', $resource_id)->update(array($filedName => $imageName));
    }
    function getContractResourcesById($contract_id)
    {
        $resources = resources::where('contract_id', $contract_id)->get();
        return $resources;
    }
    function getClientActiveResources(Request $req, $projectId)
    {
    }
    function checkIfValidToSubmit($resources)
    {
        foreach ($resources as $resource) {
            $validityArray = array($resource['name'], $resource['job_function'], $resource['starting_date'], $resource['rate'], $resource['hours']);
            if (!in_array("", $validityArray)) {
                continue;
            } else {
                return 0;
            }
        }
        return 1;
    }
    function getHires(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $company_id = Project::where('id', $req->project_id)->select('company_id')->first()->company_id;
                if ($company_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthorized group", []);
                    return (json_encode($response));
                }
                $proposalsIds = hire_developer_proposals::where('project_id', $req->project_id)->select('*')->pluck('id')->toArray();
                $contracts = DB::table('hire_developer_final_proposals')
                    ->select('id')
                    ->whereIn('proposal_id', $proposalsIds)
                    ->where('status', 1)
                    ->get();
                $contractsIds = $contracts->pluck('id')->toArray();

                $resources = DB::table('resources')
                    ->select('*')
                    ->whereIn('contract_id', $contractsIds)
                    ->get();
                $response = Controller::returnResponse(200, "successful", $resources);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
