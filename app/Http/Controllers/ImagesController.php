<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImagesController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        if ($req->hasFile('image')) {
            $destPath = 'images';
            // $ext = $req->file('image')->getClientOriginalExtension();
            // $imageName = "user-image-" . $userId . "." . $ext;
            // $imageName = now() . "-" . $req->file('image')->getClientOriginalName();
            $imageName = "zdfsdgv" . mt_rand(100000,999999) . "-" . $req->file('image')->getClientOriginalName();
            // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;
            
            $img = $req->image;
            
            $img->move(public_path($destPath), $imageName);
            // $img->storeAs($destPath, $imageName);
            dd('done');
            
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
}
