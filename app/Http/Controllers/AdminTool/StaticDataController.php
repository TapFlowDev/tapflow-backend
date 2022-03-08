<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\static_data;
class StaticDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = $this->getData();
    
        return view('AdminTool.staticData.index', ['data' => $data]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('AdminTool.staticData.add', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
      
        $content=static_data::create($request->except('_token'));
        if ($request->hasFile('imageContent')) {
          
            $destPath = 'images/content';
            $ext = $request->file('imageContent')->extension();
            $orgName = $request->file('imageContent')->getClientOriginalName();
            $imageName = "imageContent-" .$content->id.'-'.$orgName ;
            $img = $request->imageContent;
            $img->move(public_path($destPath), $imageName);
            static_data::where('id', $content->id)->update(array('image' => $imageName));
        }
        return redirect('/AdminTool/staticData');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data=$this->getDataById($id);
        return view('AdminTool.staticData.edit', ["data"=>$data]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        
        if ($request->hasFile('imageContent')) {
          
            $destPath = 'images/content';
            $ext = $request->file('imageContent')->extension();
            $orgName = $request->file('imageContent')->getClientOriginalName();
            $imageName = "imageContent-" .$id.'-'.$orgName ;
            $img = $request->imageContent;
            $img->move(public_path($destPath), $imageName);
            static_data::where('id', $id)->update(array('image' => $imageName));
        }
        $data=static_data::where('id',$id)->update(['link'=>$request->link,'text'=>$request->text]);
        return redirect('/AdminTool/staticData');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
   function getData()
   {
        $all_data=static_data::select('*')->get();
        return $all_data;
   }
   function getDataById($id)
   {
       $data=static_data::where('id',$id)->select('id','image','link','text')->first();
       return $data;
   }
   function hideContent($id)
   {
    $data=static_data::where('id',$id)->update(['hidden'=>1]);
    return redirect('/AdminTool/staticData');
   }
   function showContent($id)
   {
    $data=static_data::where('id',$id)->update(['hidden'=>0]);
    return redirect('/AdminTool/staticData');
   }
}