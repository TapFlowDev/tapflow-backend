<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Exception;
use Illuminate\Http\Request;

class FeaturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('AdminTool.Features.index', ['features' => Feature::paginate(20)]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('AdminTool.Features.add');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $features = $request->feature;
        foreach ($features as $feature) {
            $featureArr = array(
                'name' => $feature,
                'unique_name' => $this->trimedfeature($feature),
            );
            Feature::create($featureArr);
        }
        return redirect('/AdminTool/features');
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
        $feature = Feature::where('id', '=', $id)->first();
        return view('AdminTool.Features.edit', ['feature' => $feature]);
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
        $featureData = Feature::find($id);
        $feature = $request->name;
        $trimedFeature = $this->trimedFeature($feature);
        $doesExist = Feature::select('unique_name')->where('unique_name', '=', $trimedFeature)->first();
        if (!$doesExist) {
            $features = array(
                'name' => $feature,
                'unique_name' => $trimedFeature,
            );
            $featureData->update($features);
            $featureData->save();
            $request->session()->flash('success', 'Feature Edited Successfully');
            return redirect('/AdminTool/features');
        }
        $request->session()->flash('fail', 'Feature Already Exists');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $feature = Feature::where('id', '=', $id)->first();
        if (!$feature) {
            return back();
        }
        if ($feature->deleted == 1) {
            $feature->deleted = 0;
        } else {
            $feature->deleted = 1;
        }
        $feature->save();
        return back();
    }

    private function trimedFeature($feature)
    {
        $trimed = str_replace(' ', '-', strtolower(trim($feature)));
        return $trimed;
    }
    function isFeatureExsits($feature)
    {
        $responseData['status'] = 200;
        $responseData['msg'] = 'success';
        $responseData['data'] = [];
        $trimed = $this->trimedFeature($feature);
        $doesExist = Feature::where('unique_name', '=', $trimed)->first();
        if ($doesExist) {
            $responseData['status'] = 101;
            $responseData['msg'] = 'already exists';
            $responseData['data'] = [];
            return $responseData;
        }
        $responseData['data'] = ['trimed' => $trimed];
        return $responseData;
    }
    function addByCSV()
    {
        return view('AdminTool.Features.addCSV');
    }
    function createByCSV(Request $request)
    {
        // dd($request);
        try {


            $features = array();
            if ($request->hasFile('CSV')) {
                $file = $request->file('CSV');
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                // dd($real_path);
                if ($type <> 'csv') {
                    // Alert::error('Wrong file extension', 'Only CSV is allowed')->persistent('close');
                    $request->session()->flash('fail', 'File must be csv');
                    return redirect()->back();
                }
                if (($open = fopen($real_path, "r")) !== FALSE) {
                    while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                        $feature = $data[0];
                        $trimedFeature = $this->trimedFeature($feature);
                        $doesExist = Feature::select('unique_name')->where('unique_name', '=', $trimedFeature)->first();
                        if (!$doesExist) {
                            $features = array(
                                'name' => $feature,
                                'unique_name' => $trimedFeature,
                            );
                            Feature::create($features);
                        }
                    }
                    fclose($open);
                }
                $request->session()->flash('success', 'features Added Successfully');
                return redirect('/AdminTool/features');
            }
            $request->session()->flash('fail', 'File must be csv');
            return redirect()->back();
        } catch (Exception $error) {
            $request->session()->flash('fail', $error->getMessage());
            return redirect()->back();
        }
    }
}