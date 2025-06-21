<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Role;
use App\Models\Admin\Website;
use App\Models\SupportTicket;
use App\Models\SupportTicketData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'All';
        $datas = SupportTicket::orderBy('id', 'ASC')->get();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.support-ticket', compact('title', 'datas', 'website'));
    }
    public function pending()
    {
        $title = 'Pending';
        $datas = SupportTicket::where('status', 0)->where('answered', 0)->orderBy('id', 'ASC')->get();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.support-ticket', compact('title', 'datas', 'website'));
    }
    public function answered()
    {
        $title = 'Answered';
        $datas = SupportTicket::where('answered', 1)->where('status', 0)->orderBy('id', 'ASC')->get();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.support-ticket', compact('title', 'datas', 'website'));
    }
    public function closed()
    {
        $title = 'Closed';
        $datas = SupportTicket::where('status', 2)->where('answered', 1)->orderBy('id', 'ASC')->get();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.support-ticket', compact('title', 'datas', 'website'));
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
        
    }
    
    public function replay_store(Request $request)
    {
        $support_data = new SupportTicketData();
        $support_data->ticket_id = $request->ticket_id;
        $support_data->message = $request->input('message');
        
        $images = $request->file('file');
        if ($images) {
            foreach ($images as $key => $image) {
                $image_name = Str::random(20);
                $ext = strtolower($image->getClientOriginalExtension());
                $image_full_name = $image_name.'.'.$ext;
                $upload_path = 'backend/img/support-ticket/';
                $image_url = $upload_path.$image_full_name;
                $success = $image->move($upload_path, $image_full_name);
                $img_arr[$key] = $image_url;
            }
            $support_data->file = trim(implode('|', $img_arr), '|');
        }
        
        $support_data->is_replay = 1;
        $support_data->save();
        
        $data = SupportTicket::find($request->ticket_id);
        $data->answered = 1;
        $data->save();
        
        return redirect()->back()->with('message','Ticket successfully replayed');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Ticket Details';
        $data = SupportTicket::find($id);
        $ticket_datas = SupportTicketData::where('ticket_id', $data->id)->latest()->get();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.show-ticket', compact('title', 'data', 'ticket_datas', 'website'));
    }
    
    public function close($id)
    {
        $data = SupportTicket::find($id);
        $data->status = 2;
        $data->save();
        return redirect()->back()->with('message','Ticket successfully closed');
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = SupportTicket::find($id);
        $tkt_datas = SupportTicketData::where('ticket_id', $data->id)->get();
        foreach($tkt_datas as $tkt_data){
            if($tkt_data->file){
                $files = explode("|",$tkt_data->file);
                foreach($files as $file){
                    if(file_exists($file)){
                        unlink($file);
                    }
                }
            }
            $tkt_data->delete();
        }
        $data->delete();
        return redirect()->back()->with('message','Ticket successfully deleted');
    }
    
    public function destroy_data($id)
    {
        $data = SupportTicketData::find($id);
        if($data->file){
            $files = explode("|",$data->file);
            foreach($files as $file){
                if(file_exists($file)){
                    unlink($file);
                }
            }
        }
        $data->delete();
        return redirect()->back()->with('message','Ticket successfully deleted');
    }
}
