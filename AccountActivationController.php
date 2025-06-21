<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Deposit;
use App\Models\Admin\DepositAccount;
use App\Models\DepositHeadline;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserDepositCOntroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function active()
    {
        $pay_accounts = DepositAccount::where('status', 1)->get();
        $headlines = DepositHeadline::all();
        return view('user.pages.activation', compact('pay_accounts', 'headlines'));
    }

   
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $request->validate([
            'deposit_account' => 'required',
            'amount' => 'required',
            'phone' => 'required',
            'receipt' => 'required',
        ]);

        $category = new Deposit();
        $category->account_id = $request->input('deposit_account');
        $category->amount = $request->input('amount');
        $category->phone = $request->input('phone');
        $category->transaction_id = $request->input('transaction_id');
        $image = $request->file('receipt');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/deposit/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
        }
        $category->receipt = $image_url;
        $category->user_id = Auth::user()->id;
        $category->save();

        return redirect()->back()->with('message','Deposit successful');
    }
    
    public function earning_to_deposit()
    {
        $user = User::find(Auth::user()->id);
        $user->earning_balance = $user->earning_balance - 1;
        $user->deposit_balance = $user->deposit_balance + 1;
        $user->save();

        return redirect()->back()->with('message','Deposit successful');
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
