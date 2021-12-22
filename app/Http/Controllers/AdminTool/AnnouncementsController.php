<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;


class AnnouncementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('AdminTool.Announcements.index', ['announcements'=>$this->getDataInfo(Announcement::paginate(10))]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('AdminTool.Announcements.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $announcement = Announcement::create($request->except(['_token', 'logo']));

        $announcementId = $announcement->id;
        if ($request->hasFile('logo')) {
            $destPath = 'images/announcements';
            $ext = $request->file('logo')->extension();
            $imageName = "announcement-" . $announcementId . "." . $ext;
            $img = $request->logo;
            $img->move(public_path($destPath), $imageName);
            Announcement::where('id', $announcementId)->update(array('logo' => $imageName));
        }
        return redirect('/AdminTool/announcements');
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
        return view('AdminTool.Announcements.edit', ['announcement'=>Announcement::find($id)]);
        
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
        $announcementId = $id;
        $announcement = Announcement::findOrfail($id);
        $announcement->update($request->except(['_token', 'logo']));
        if ($request->hasFile('logo')) {
            $destPath = 'images/announcements';
            $ext = $request->file('logo')->extension();
            $imageName = "announcement-" . $announcementId . "." . $ext;
            $img = $request->logo;
            $img->move(public_path($destPath), $imageName);
            Announcement::where('id', $announcementId)->update(array('logo' => $imageName));
        }
        return redirect('/AdminTool/announcements');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Announcement::destroy($id);
        return redirect('/AdminTool/announcements');
    }
    private function getDataInfo($announcements){
        foreach($announcements as $key => &$announcement){
            $content = strip_tags($announcement->content);
            $content = html_entity_decode($content);
            $announcement->stripedContent = $content;
            
        }
        
        return $announcements;
    }
}