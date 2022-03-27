<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        // dd($request->verify);
        Group::where('id', $id)->update(['verified' => (int)$request->verify]);
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
        //
    }
    public function agencyExportCsv(Request $request)
    {
        $fileName = "agencies-".date("Y-m-d H:i:s").".csv";
        $groupObj = new TeamsController;
        $tasks = $groupObj->getAllTeams();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Agency Name', 'Admin Name', 'Admin Email', 'Is Verfied');

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['Agency Name']  = $task->name;
                $row['Admin Name']    = $task->admin_name;
                $row['Admin Email']    = $task->admin_email;
                if($task->verified==1){
                    $row['Is Verfied']  = "Verfied";
                }else{
                    $row['Is Verfied']  = "Not Verfied";
                }

                fputcsv($file, array($row['Agency Name'], $row['Admin Name'], $row['Admin Email'], $row['Is Verfied']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
