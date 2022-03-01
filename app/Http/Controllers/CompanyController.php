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
use App\Http\Controllers\ProjectController;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
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
        $ProjectControllerObj = new ProjectController;
        $projectsNo = $ProjectControllerObj->getNumberOfProjectForCompany($id);
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
        $info->projectsNo = $projectsNo;
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
    function updateCompanyBio(Request $req)
    {
        try {
            $rules = array(
                "id" => "required",
                "bio" => "required",
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            } else {
                $userData = Controller::checkUser($req);
                if ($userData['exist'] == 1) {
                    if ($userData['group_id'] == $req->id) {
                        if ($userData['privileges'] == 1) {

                            Company::where('group_id', $req->id)->update(['bio' => $req->bio]);
                            $response = Controller::returnResponse(200, "successful", []);
                            return (json_encode($response));
                        } else {
                            $response = Controller::returnResponse(422, "Unauthorized this function for admins only", []);
                            return (json_encode($response));
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "the user does not have company", []);
                    return (json_encode($response));
                }
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function updateBasicInfo(Request $req)
    {
        try {
            $rules = array(
                "id" => "required",
                "name" => "required",
                "country" => "required",
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            } else {
                $userData = Controller::checkUser($req);
                if ($userData['exist'] == 1) {
                    if ($userData['group_id'] == $req->id) {
                        if ($userData['privileges'] == 1) {
                            Group::where('id', $req->id)->update(['name' => $req->name]);
                            Company::where('group_id', $req->id)->update(['country' => $req->country]);
                            $response = Controller::returnResponse(200, "successful", []);
                            return (json_encode($response));
                        } else {
                            $response = Controller::returnResponse(422, "Unauthorized this function for admins only", []);
                            return (json_encode($response));
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "the user does not have company", []);
                    return (json_encode($response));
                }
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function updateLink(Request $req)
    {
        try {
            $rules = array(
                "id" => "required",
                "link" => "required",
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            } else {
                $userData = Controller::checkUser($req);
                if ($userData['exist'] == 1) {
                    if ($userData['group_id'] == $req->id) {
                        if ($userData['privileges'] == 1) {

                            Company::where('group_id', $req->id)->update(['link' => $req->link]);
                            $response = Controller::returnResponse(200, "successful", []);
                            return (json_encode($response));
                        } else {
                            $response = Controller::returnResponse(422, "Unauthorized this function for admins only", []);
                            return (json_encode($response));
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "the user does not have company", []);
                    return (json_encode($response));
                }
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function updateCompanyImage(Request $req)
    {
        try {
            $rules = array(
                "id" => "required|exists:groups,id",
                "image" => "required|mimes:png,jpg,jpeg|max:5000"
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            } else {
                $userData = Controller::checkUser($req);
                if ($userData['exist'] == 1) {
                    if ($userData['group_id'] == $req->id) {
                        if ($userData['privileges'] == 1) {
                            $company_image = Company::where('id', $req->id)->select('image')->first()->image;
                            $image_path = "images/companies/" . $company_image;
                             File::delete(public_path($image_path));
                            if ($req->hasFile('image')) {
                                $destPath = 'images/companies';
                                $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
                                $img = $req->image;
                                $img->move(public_path($destPath), $imageName);
                                $this->updateFiles($req->id, $imageName, 'image');
                                $response=Controller::returnResponse(200,'successful',[]);
                                return json_encode($response);
                            }
                        } else {
                            $response = Controller::returnResponse(422, "Unauthorized this function for admins only", []);
                            return (json_encode($response));
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "the user does not have company", []);
                    return (json_encode($response));
                }
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function updateFieldSector(Request $req)
    {
        try {
            $rules = array(
                "id" => "required",
                "field" => "required",
                "sector" => "required",
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            } else {
                $userData = Controller::checkUser($req);
                if ($userData['exist'] == 1) {
                    if ($userData['group_id'] == $req->id) {
                        if ($userData['privileges'] == 1) {
                            Company::where('group_id', $req->id)->update(['field' => $req->filed, 'sector' => $req->sector]);
                            $response = Controller::returnResponse(200, "successful", []);
                            return (json_encode($response));
                        } else {
                            $response = Controller::returnResponse(422, "Unauthorized this function for admins only", []);
                            return (json_encode($response));
                        }
                    } else {
                        $response = Controller::returnResponse(422, "Unauthorized you are trying to access another company data", []);
                        return (json_encode($response));
                    }
                } else {
                    $response = Controller::returnResponse(422, "the user does not have company", []);
                    return (json_encode($response));
                }
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
