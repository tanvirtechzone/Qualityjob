<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketData;
use App\Models\Admin\MainWallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserSupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currenturl = url()->current();
        if( Auth::user()->is_ban == 1 && suspend_url_status($currenturl) == 1){
            return redirect()->route('user.account-suspended');
        }
        
        $datas = SupportTicket::where('user_id', Auth::user()->id)->latest()->get();
        return view('user.pages.support-ticket-list', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currenturl = url()->current();
        if( Auth::user()->is_ban == 1 && suspend_url_status($currenturl) == 1){
            return redirect()->route('user.account-suspended');
        }
        
        return view('user.pages.support-ticket');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'priority' => 'required',
            'message' => 'required',
        ]);
        
        // return $request;

        $data = new SupportTicket();
        $data->user_id = Auth::user()->id;
        $data->name = $request->input('name');
        $data->email = $request->input('email');
        $data->subject = $request->input('subject');
        $data->priority = $request->input('priority');

        // $image = $request->file('file');
        // if ($image) {
        //     $image_name = Str::random(20);
        //     $ext = strtolower($image->getClientOriginalExtension());
        //     $image_full_name = $image_name.'.'.$ext;
        //     $upload_path = 'backend/img/support-ticket/';
        //     $image_url = $upload_path.$image_full_name;
        //     $success = $image->move($upload_path, $image_full_name);
        //     $data->file = $image_url;
        // }
        $data->save();

        $support_data = new SupportTicketData();
        $support_data->ticket_id = $data->id;
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
        
        $support_data->save();
        
        return redirect()->back()->with('message','Ticket successfully submited');
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
        $data->answered = 0;
        $data->status = 0;
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
        return view('user.pages.show-support-ticket', compact('title', 'data', 'ticket_datas'));
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
