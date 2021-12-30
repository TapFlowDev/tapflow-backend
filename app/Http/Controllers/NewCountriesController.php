<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Countries;
use Exception;

class NewCountriesController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $data=$req->all();
        
        foreach($data as $country)
        {
            $flag="https://cdn.jsdelivr.net/npm/react-flagkit@1.0.2/img/SVG/".$country['code'] .".svg";
            $county=Countries::create(['name' =>$country['name'] ,'code'=>$country['code'],'flag'=>$flag]);
        }
     return('success');
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
            $countries = Countries::all();
            $response = Controller::returnResponse(200, 'successfully', $countries);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'something wrong', $error);
            return json_encode($response);
        }
    }
    function getCountryById($id){
        $country=Countries::where('id',$id)->first();
        $response=Controller::returnResponse(200,'successful',['name'=>$country]);
        return (json_encode($response));

    } function getCountryFlag($id){
        $country=Countries::where('id',$id)->select('flag')->first();
       return $country;
    }
}
