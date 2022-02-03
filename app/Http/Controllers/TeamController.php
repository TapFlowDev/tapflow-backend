<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\Group;
use App\Http\Controllers\GroupsLinksController;
use App\Http\Controllers\GroupMembersController;
use App\Http\Controllers\GroupCategoriesController;
use App\Http\Controllers\NewCountriesController;
use App\Http\Controllers\AgencyTargetsController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use League\CommonMark\Context;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Exception;
use Facade\FlareClient\Flare;
use PhpParser\Node\Expr\New_;
use PHPUnit\TextUI\XmlConfiguration\Groups;

use function PHPSTORM_META\type;

class TeamController extends Controller
{
    //add row 
    function Insert($arr)
    {
        return Team::create($arr);
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }
    function updateFiles($id, $fileName, $columnName)
    {
        Team::where('group_id', $id)->update(array($columnName => $fileName));
    }
    function get_team($id)
    {
        try {
            $linksController = new GroupsLinksController;
            $GroupMembersController = new GroupMembersController;
            $GroupCategoriesController = new GroupCategoriesController;
            $NewCountriesController = new NewCountriesController;
            $agencyTargetsController = new AgencyTargetsController;
            $links = $linksController->get_group_links($id);
            $teamMembers = $GroupMembersController->getTeamMembersByGroupId($id);
            $cats = $GroupCategoriesController->getTeamCategories($id);
            $targets = $agencyTargetsController->getTargets($id);
            $info = $this->get_team_info($id);
            $country_id = $info->country;
            $Country = $NewCountriesController->getCountryFlag($country_id);
            $info->image = asset('images/companies/' . $info->image);
            $info->links = $links;
            $info->teamMembers = $teamMembers;
            $info->categories = $cats;
            $info->targets = $targets;
            $info->countryName = $Country->name;
            $info->countryCode = $Country->code;
            $info->countryFlag = $Country->flag;
            $response = Controller::returnResponse(200, "successful", $info);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    private function get_team_info($id)
    {
        $team = DB::table('groups')
            ->Join('teams', 'groups.id', '=', 'teams.group_id')
            ->where('groups.id', '=',  $id)
            ->select('groups.id', 'groups.name', 'groups.type', 'groups.verified', 'teams.bio', 'teams.image', 'teams.link', 'teams.country', 'teams.field',)
            ->first();

        return ($team);
    }
    function updateGeneralInfo(Request $req)
    {
        $rules = array(
            'group_id' => "required|exists:groups,id",
            'name' => "required|max:255",
            'country' => "required|max:255",
            'field' => "required|max:255",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } else {
            $group = Group::where("id", $req->group_id)->update(['name' => $req->name]);
            $team = Team::where("group_id", $req->group_id)->update(['country' => $req->country, 'field' => $req->field]);
            $response = Controller::returnResponse(200, 'successful', []);
            return json_encode($response);
        }
    }

    function updateTeamBio(Request $req)
    {
        $rules = array(
            "group_id" => "required|exists:groups,id",
            "bio" => "required"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } else {
            try {
                $team = Team::where('group_id', $req->group_id)->update(['bio' => $req->bio]);
                $response = Controller::returnResponse(200, 'successful', []);
                return json_encode($response);
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
                return json_encode($response);
            }
        }
    }

    function updateTeamImage(Request $req)
    {
        $rules = array(
            "group_id" => "required|exists:groups,id",
            "image" => "required"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } else {
            try {
                $id = $req->group_id;
                $team_image = Team::where('group_id', $id)->select('image')->first()->image;
                $image_path = "images/companies/" . $team_image;
                $a = File::delete(public_path($image_path));
                if ($req->hasFile('image')) {
                    $destPath = 'images/companies';
                    $ext = $req->file('image')->extension();
                    $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
                    // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;
                    $img = $req->image;
                    $img->move(public_path($destPath), $imageName);
                    $this->updateFiles($id, $imageName, 'image');
                }
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'successful', $error);
                return json_encode($response);
            }
        }
    }

    function updateLink(Request $req)
    {
        $rules = array(
            "group_id" => "required|exists:groups,id",
            "link" => "required|max:255"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } else {
            try {
                $team = Team::where('group_id', $req->group_id)->update(['link' => $req->link]);
                $response = Controller::returnResponse(200, 'successful', []);
                return json_encode($response);
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
                return json_encode($response);
            }
        }
    }
}
