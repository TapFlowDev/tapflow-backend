<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Exception;
use Illuminate\Http\Request;

class FeatureController extends Controller
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
    function search($feature)
    {
        try {
            $featureTrimed = $this->trimedFeature($feature);
            $features = Feature::select('id', 'name')->where('unique_name', 'LIKE', $featureTrimed . '%')->get();
            $response = Controller::returnResponse(200, 'data found', $features);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    private function trimedFeature($feature)
    {
        $trimed = str_replace(' ', '-', strtolower(trim($feature)));
        return $trimed;
    }
}
