<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Deposit;
use App\Models\Admin\DepositDocumentData;
use App\Models\Admin\DepositAccount;
use App\Models\Admin\DepositAccountDocument;
use App\Models\Admin\Website;
use App\Models\User;
use App\Models\Admin\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepositAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = DepositAccount::all();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.deposit-account', compact('datas', 'website'));
    }

    public function deposit_list()
    {
        $datas = Deposit::latest()->get();
        $website = Website::latest()->first();
        $title = 'Deposit List';
        return view('backend.pages.system-setting.deposit', compact('title', 'datas', 'website'));
    }

    public function pending_deposit()
    {
        $datas = Deposit::where('approval', 0)->latest()->get();
        $website = Website::latest()->first();
        $title = 'Pending Deposit List';
        return view('backend.pages.system-setting.deposit', compact('title', 'datas', 'website'));
    }

    public function deposit_approved(Request $request, $id)
    {
        $deposit = Deposit::find($id);
        $msg_user_id = $deposit->user_id;

        if($request->approval == 1){
            $user = User::find($deposit->user_id);
            $user->deposit_balance = $user->deposit_balance + $deposit->amount;

            $website = Website::latest()->first();
            if($website->referral_deposit_commission > 0){
                $deposit_commission = ($website->referral_deposit_commission * $deposit->amount) / 100;

                $refered_by = User::find($user->rfered_by);
                if($refered_by){
                    $refered_by->deposit_balance = $refered_by->deposit_balance + $deposit_commission;
                    $refered_by->save();
    
                    $user->deposit_commision_from_refer = $user->deposit_commision_from_refer + $deposit_commission;
                }
            }

            $user->save();
        
            $data = new UserMessage();
            $data->user_id = $msg_user_id;
            $data->message_title = 'Deposite';
            $data->message = 'Your last deposit approved.';
            $data->save();
        }elseif($request->approval == 2){
            $deposit->reason = $request->reason;
        
            $data = new UserMessage();
            $data->user_id = $msg_user_id;
            $data->message_title = 'Deposite';
            $data->message = 'Your last deposit rejected.';
            $data->save();
        }
        
        $deposit->approval = $request->approval;
        $deposit->save();

        return redirect()->back()->with('message','Successfully approved this deposit!');
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
        $request->validate([
            'name' => 'required|unique:deposit_accounts|max:100',
            'account_no' => 'required',
        ],[
            'name.required'=> 'Please give a unique account name.'
        ]);

        $data = new DepositAccount();
        $data->name = Str::ucfirst($request->input('name'));
        $data->account_no = $request->input('account_no');

        $image = $request->file('image');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/account/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $data->image = $image_url;
        }

        $data->guideline = $request->input('guideline');
        $data->status = $request->input('status');
        $data->save();

        return redirect()->back()->with('message','Data added Successfully');
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
        $request->validate([
            'name' => 'required',
            'account_no' => 'required',
        ],[
            'name.required'=> 'Please give a unique account name.'
        ]);

        $data = DepositAccount::find($id);
        $data->name = Str::ucfirst($request->input('name'));
        $data->account_no = $request->input('account_no');

        $image = $request->file('image');
        if ($image) {
            if(file_exists($data->image)){
                unlink($data->image);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/account/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $data->image = $image_url;
        }

        $data->guideline = $request->input('guideline');
        $data->status = $request->input('status');
        $data->save();

        return redirect()->back()->with('message','Data updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deposit = Deposit::find($id);
        $deposit->delete();

        return redirect()->back()->with('message','Successfully approved this deposit!');
    }
    
    public function deposit_delete($id)
    {
        $data = Deposit::find($id);
        
        $documents = DepositDocumentData::where('deposit_id', $id)->orderBy('id', 'ASC')->get();
        foreach($documents as $document){
            $document_data = DepositDocumentData::find($document->id);
            if($document_data->type == 'file'){
                if(file_exists($document_data->data)){
                    unlink($document_data->data);
                }
            }
            $document_data->delete();
        }
        
        $data->delete();

        return redirect()->back()->with('message','Successfully delete this data!');
    }
}
