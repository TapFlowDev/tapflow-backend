<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Models\notifications_setting;
use Illuminate\Http\Request;

class NotificationSettings extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('AdminTool.NotificationsSettings.index', ['notifications' => notifications_setting::paginate(20)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('AdminTool.NotificationsSettings.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'numeric|gt:0|lt:4',
            'email_subject' => "required",
            'email_text' => "required",
            'email_template' => "required",
            // 'notification_title',
            // 'notification_text',
        ]);
        $email_template = $request->email_template;
        $email_template = str_replace(' ', '-', strtolower(trim($email_template)));
        $request->email_template = $email_template;
        $isEmailExits = notifications_setting::select('email_template')->where('email_template','=', $email_template)->get()->first();
        if($isEmailExits){
            return 'Email already exists';
        }
        // return $request->email_template;
        notifications_setting::create($request->except('_token', 'email_template') +['email_template'=> $email_template]);
        return redirect('AdminTool/notificationSettings');
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
