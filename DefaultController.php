<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\DollarRate;
use App\Models\Admin\JobFee;
use App\Models\Admin\MainWallet;
use App\Models\Admin\Website;
use App\Models\Admin\WelcomeBonus;
use App\Models\Admin\WithdrawFee;
use Illuminate\Http\Request;

class DefaultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wc_bonus = WelcomeBonus::latest()->first();
        $dollar_rate = DollarRate::latest()->first();
        $jobFee = JobFee::latest()->first();
        $withdrawFee = WithdrawFee::latest()->first();
        $main_wallet = MainWallet::latest()->first();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.deafult-setup', compact('wc_bonus', 'website', 'main_wallet', 'dollar_rate', 'jobFee', 'withdrawFee'));
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

    public function withdraw_fee(Request $request, $id)
    {
        $withdrawFee = WithdrawFee::find($id);
        $withdrawFee->fee = $request->fee;
        $withdrawFee->minimum_withdraw = $request->minimum_withdraw;
        $withdrawFee->save();

        return redirect()->back()->with('message','Data updated Successfully');
        $jobFee = JobFee::find($id);
    }

    public function job_fee(Request $request, $id)
    {
        $jobFee = JobFee::find($id);
        $jobFee->fee = $request->fee;
        $jobFee->save();

        return redirect()->back()->with('message','Data updated Successfully');
    }

    public function main_wallet_update(Request $request, $id)
    {
        $request->validate([
            'main_balance' => 'required',
        ]);

        $data = MainWallet::find($id);
        $data->amount = $request->input('main_balance');
        $data->save();

        return redirect()->back()->with('message','Data updated Successfully');
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
