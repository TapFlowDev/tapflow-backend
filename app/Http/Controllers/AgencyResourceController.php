<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agency_resource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgencyResourceController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        try {
            $userData = $this->checkUser($req);
            $condition = ($userData['privileges'] == 1 && $userData['group_id'] != '');
            if (!$condition) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $rules = array(
                "name" => "required|max:255",
                "seniority" => "required|exists:categories,id",
                "country" => "required|exists:countries,id",
                "rate" => "required|numeric",
                "skills" => "required",
                "cv" => "mimes:doc,pdf,docx",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
                return json_encode($response);
            }
            $agencyResourcesSkillObj = new AgencyResourcesSkillController;
            $agencyResourceArr = [
                'team_id' => $userData['group_id'],
                'name' => $req->name,
                'seniority' => $req->seniority,
                'country' => $req->country,
                'hourly_rate' => $req->rate,
            ];
            $agencyResource = Agency_resource::create($agencyResourceArr);
            if ($req->hasFile('cv')) {
                $destPath = 'images/cvs';
                $imageName = time() . "-" . $req->file('cv')->getUserOriginalName();
                $img = $req->cv;
                $img->move(public_path($destPath), $imageName);
                $this->updateFiles($agencyResource->id, $imageName, 'cv');
            }
            $agencyResourcesSkillObj->Insert($agencyResource->id, $req->skills);
            $responseData = ['agencyResourceId'=>$agencyResource->id]; 
            $response = Controller::returnResponse(200, 'Success', $responseData);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
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
    function updateFiles($id, $imageName, $filedName)
    {
        Agency_resource::where('id', $id)->update(array($filedName => $imageName));
    }
}
