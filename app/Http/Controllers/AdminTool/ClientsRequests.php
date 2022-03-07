<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Models\Clients_requests;
use App\Models\Form_options;
use Illuminate\Http\Request;

class ClientsRequests extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('AdminTool.ClientsRequests.index', ['users' => Clients_requests::orderBy('status', 'desc')->paginate(20)]);
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
        $info = Clients_requests::find($id);
        $answers = unserialize($info->answers);
        $userAnswers = array();
        $count = 0;
        foreach($answers as $keyAnswer => $valAnswer){
            $question = Form_options::find($keyAnswer);
            if(isset($question)){
                $userAnswers[$count]['question'] = $question->label;
                $userAnswers[$count]['answer'] = $valAnswer;
            }else{
                $count++;
                continue;
            }
            $count++;
        }
        $info->answers = $userAnswers;
        return $info;
        // return view('AdminTool.ClientsRequests.show',  ['info' => $info]);
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
        //
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
}
