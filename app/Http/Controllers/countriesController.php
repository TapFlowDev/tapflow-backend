<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use app\Models\country;
use Exception;
use Illuminate\Support\Facades\DB;
class countriesController extends Controller
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
    
    function getCountries()
    {
        try {
            $countries = country::all();
            $response = Controller::returnResponse(200, 'successfully', $countries);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'something wrong', $error);
            return json_encode($response);
        }
    }
    function getCountryById($id){
        $country=country::where('id',$id)->select('name')->first();
        $response=Controller::returnResponse(200,'successful',['name'=>$country]);
        return (json_encode($response));
    }

}
