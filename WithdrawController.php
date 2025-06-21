<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\MainWallet;
use App\Models\Admin\Website;
use App\Models\User;
use App\Models\Admin\UserMessage;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = Withdraw::latest()->get();
        $website = Website::latest()->first();
        $title = 'Withdraw Request';
        return view('backend.pages.system-setting.withdraw', compact('title', 'datas', 'website'));
    }

    public function pending_withdraw_request()
    {
        $datas = Withdraw::where('approval', 0)->latest()->get();
        $website = Website::latest()->first();
        $title = 'Pending Withdraw Request';
        return view('backend.pages.system-setting.withdraw', compact('title', 'datas', 'website'));
    }

    public function withdraw_request_approved(Request $request, $id)
    {
        $withdraw = Withdraw::find($id);
        $msg_user_id = $withdraw->user_id;

        if($request->approval == 1){
            $payable = $withdraw->amount - $withdraw->charge;

            $main_wallet = MainWallet::latest()->first();
            $main_wallet->amount = $main_wallet->amount - $payable;
            $main_wallet->save();

        }elseif($request->approval == 2){
            $user = User::find($withdraw->user_id);
            $user->earning_balance = $user->earning_balance + $withdraw->amount;
            $user->save();

            $main_wallet = MainWallet::latest()->first();
            $main_wallet->amount = $main_wallet->amount - $withdraw->amount;
            $main_wallet->save();
        }

        $withdraw->approval = $request->approval;
        $withdraw->reason = $request->reason;
        $withdraw->save();
        
        $data = new UserMessage();
        $data->user_id = $msg_user_id;
        $data->message_title = 'Withdraw';
        $data->message = 'Your withdraw request approved.';
        $data->save();

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
        $withdraw = Withdraw::find($id);
        $withdraw->delete();

        return redirect()->back()->with('message','Successfully deleted this withdraw request!');
    }
}
