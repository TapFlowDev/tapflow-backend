<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Category;
use App\Http\Controllers\GroupCategoriesController;
use App\Http\Controllers\NewCountriesController;
use App\Http\Controllers\GroupsLinksController;
use App\Http\Controllers\GroupMembersController;
use Illuminate\Support\Facades\DB;
use Exception;

class CompanyController extends Controller
{
    //add row 
    function Insert($arr)
    {
        return Company::create($arr);
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }

    function updateFiles($groupId, $imageName, $filedName)
    {
        Company::where('group_id', $groupId)->update(array($filedName => $imageName));
    }
    function getCompany($id)
    {
        // try {
        $linksController = new GroupsLinksController;
        $GroupMembersController = new GroupMembersController;
        $GroupCategoriesController = new GroupCategoriesController;
        $NewCountriesController = new NewCountriesController;
        $links = $linksController->get_group_links($id);
        $teamMembers = $GroupMembersController->getCompanyMembersByGroupId($id);
        $cats = $GroupCategoriesController->getTeamCategories($id);
        $info = $this->get_company_info($id);
        $country_id = $info->country;
        $Country = $NewCountriesController->getCountryFlag($country_id);
        if ($info->image == '') {
            $info->image = asset('images/profile-pic.jpg');
        } else {
            $info->image = asset('images/companies/' . $info->image);
        }
        $info->links = $links;
        $info->teamMembers = $teamMembers;
        $info->categories = $cats;
        if (isset($info->field)) {
            // $info->field = Category::find($info->field)->name;
            $info->field = Category::select('id', 'name')->where('id', '=', $info->field)->get()->first();
        } else {
            $info->field = '';
        }
        if (isset($info->sector)) {
            $info->sector = Category::select('id', 'name')->where('id', '=', $info->sector)->get()->first();
        } else {
            $info->sector = '';
        }
        $info->countryName = $Country->name;
        $info->countryCode = $Country->code;
        $info->countryFlag = $Country->flag;
        $response = Controller::returnResponse(200, "successful", $info);
        return (json_encode($response));
        // } catch (Exception $error) {
        //     $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
        //     return json_encode($response);
        // }
    }
    private function get_company_info($id)
    {
        $team = DB::table('groups')
            ->Join('companies', 'groups.id', '=', 'companies.group_id')
            ->where('groups.id', '=',  $id)
            ->select('groups.id', 'groups.name', 'groups.type', 'groups.verified', 'companies.bio', 'companies.image', 'companies.link', 'companies.country', 'companies.field', 'companies.employees_number', 'companies.sector')
            ->first();

        return ($team);
    }
}
