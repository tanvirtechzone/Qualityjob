<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\MainWallet;
use App\Models\Admin\WithdrawFee;
use App\Models\User;
use App\Models\Withdraw;
use App\Models\WithdrawHeadline;
use App\Models\WithdrawMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $withdraws = Withdraw::where('user_id', Auth::user()->id)->latest()->get();
        $headlines = WithdrawHeadline::all();
        return view('user.pages.withdraw', compact('withdraws', 'headlines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $withdraw_fee = WithdrawFee::latest()->first();
        $methods = WithdrawMethod::where('status', 1)->get();
        $headlines = WithdrawHeadline::all();
        return view('user.pages.new-withdraw', compact('withdraw_fee', 'methods', 'headlines'));
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
            'amount' => 'required',
        ]);

        $withdraw_fee = WithdrawFee::latest()->first();
        if($withdraw_fee->minimum_withdraw > $request->input('amount') || Auth::user()->earning_balance < $request->input('amount')){
            return redirect()->back()->with('error','You have no sufficient balance for withdraw.');
        }else{
            $user_balance = User::find(Auth::user()->id);
            $user_balance->earning_balance = $user_balance->earning_balance - $request->input('amount');
            $user_balance->save();

            $main_wallet = MainWallet::latest()->first();
            $main_wallet->amount = $main_wallet->amount + $request->input('amount');
            $main_wallet->save();
        }

        $withdraw = new Withdraw();
        $withdraw->amount = $request->input('amount');
        $withdraw->charge = $request->input('withdraw_charge');
        $withdraw->account_type = $request->input('account_type');
        $withdraw->account_no = $request->input('account_no');
        $withdraw->admin_fee = $request->input('admin_fee');
        $withdraw->user_id = Auth::user()->id;
        $withdraw->save();

        return redirect()->route('user.withdraw')->with('message','Deposit successful');
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
