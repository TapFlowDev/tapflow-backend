<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Countries;
use App\Models\Freelancer;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectAgencyMatchController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }
    // function getMatchByProjectId($projectId){
    //     $matches = DB::table('project_agency_matches')
    //     ->join('groups', 'project_agency_matches.group_id', '=', 'groups.id')
    //     ->select('groups.*')
    //     ->where('project_agency_matches.project_id', '=', $projectId)
    //     ->get();
    //     return $matches;
    // }
    function getMatchCountByProjectId($projectId)
    {
        $matchesCount = DB::table('project_agency_matches')
            ->where('project_id', '=', $projectId)
            ->count();
        return $matchesCount;
    }
    function getProjectAgencyMatches(Request $req, $id, $offset = 0, $limit = 4)
    {
        try {
            $userData = $this->checkUser($req);
            $page = ($offset - 1) * $limit;
            $agencyIds = DB::table('project_agency_matches')
                ->select('group_id')
                ->where('project_id', '=', $id)
                ->offset($page)->limit($limit)->get()->pluck('group_id')->toArray();
            if (count($agencyIds) > 0) {
                $agencies = $this->getAgencyData($agencyIds);
            } else {
                $agencies = [];
            }
            $agencyCount = DB::table('project_agency_matches')
                ->select('group_id')
                ->where('project_id', '=', $id)
                ->count();
        } catch (Exception $error) {
            $responseData = $error->getMessage();
            $response = Controller::returnResponse(500, "Error", $responseData);
            return (json_encode($response));
        }
    }
    function getAgencyData($agencyIds)
    {
        $freelancerObj = new FreeLancerController;
        $groupMemsObj = new GroupMembersController;

        $agencies = [];
        foreach ($agencyIds as $key => $id) {
            $agencyAdmin = $groupMemsObj->getTeamAdminByGroupId($id);
            $teamInfo = Team::where('group_id', '=', $id)->first();
            $freelancer = Freelancer::where('user_id', '=', $teamInfo->user_id)->first();
            $country = Countries::find($agencyAdmin->country);
            $hourly_rate = Category::find((int)$teamInfo->hourly_rate);
            $lead_time = Category::find((int)$teamInfo->lead_time);
            $countryName = ($country ? $country->name : " ");
            $budget = ($hourly_rate ? $hourly_rate->name : "$$teamInfo->minPerHour - $$teamInfo->maxPerHour");
            $startProject = ($lead_time ? $lead_time->name : " ");
            $image = $freelancer->image;
            if ($image) {
                $image = asset('images/users/' . $image);
            } else {
                $image = asset('images/profile-pic.jpg');
            }
            $agencyArr = [
                'adminName' => $agencyAdmin->first_name . " " . $agencyAdmin->last_name,
                'country' => $countryName,
                'hourlyRate' => $budget,
                'leadTime' => $startProject,
                'image' => $image,
            ];
            $agencies[] = $agencyArr;
        }
        return $agencies;
    }
    function  AskToApply(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $RoomObj = new RoomController();
                $projectObj = new ProjectController;
                $companyAdmin = $projectObj->getCompanyProjectAdmin($req->project_id);
                $data = array('name' => null, 'agencyAdmin' => $req->agency_admin, 'companyAdmin' => $companyAdmin);
                $room = $RoomObj->createRoom($data);
                if ($room['code'] != 200) {
                    $response = Controller::returnResponse(500, "something wrong chat", $room['msg']);
                    return (json_encode($response));
                }
                $response = Controller::returnResponse(200, "successful", ['room_id' => $room['msg']]);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wrong chat", $error->getMessage());
            return (json_encode($response));
        }
    }
}
