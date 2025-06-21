<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\MainWallet;
use App\Models\User;
use App\Models\Admin\DepositAccount;
use App\Models\InvestmentPackage;
use App\Models\InvestmentPackageBook;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserInvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = InvestmentPackage::latest()->get();
        return view('user.pages.investment-package', compact('packages'));
    }
    
    public function investment_list()
    {
        $datas = InvestmentPackageBook::where('user_id', Auth::user()->id)->latest()->get();
        return view('user.pages.investment-list', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($package_id)
    {
        $pay_accounts = DepositAccount::where('status', 1)->get();
        $package = InvestmentPackage::find($package_id);
        return view('user.pages.investment-create', compact('package', 'pay_accounts'));
    }
    
    public function invest_from_earning($package_id)
    {
        $package = InvestmentPackage::find($package_id);
        if(Auth::user()->earning_balance < $package->invest_amount){
            return redirect()->back()->with('error','Insufficicent Balance');
        }
        
        $user = User::find(Auth::user()->id);
        $user->earning_balance = $user->earning_balance - $package->invest_amount;
        $user->save();
        
        $data = new InvestmentPackageBook();
        $data->package_id = $package_id;
        $data->user_id = Auth::user()->id;
        $data->invest_amount = $package->invest_amount;
        $data->profit_per = $package->profit_per;
        $data->duration = $package->duration;
        $data->final_amount = $package->invest_amount + (($package->invest_amount * $package->profit_per)/100);
        $data->payment_type = 1;
        $data->status = 1;
        $data->save();

        return redirect()->back()->with('message','Investment successful');
    }
    
    public function invest_from_deposit($package_id)
    {
        $package = InvestmentPackage::find($package_id);
        if(Auth::user()->deposit_balance < $package->invest_amount){
            return redirect()->back()->with('error','Insufficicent Balance');
        }
        
        $data = new InvestmentPackageBook();
        $data->package_id = $package_id;
        $data->user_id = Auth::user()->id;
        $data->invest_amount = $package->invest_amount;
        $data->profit_per = $package->profit_per;
        $data->duration = $package->duration;
        $data->final_amount = $package->invest_amount + (($package->invest_amount * $package->profit_per)/100);
        $data->payment_type = 2;
        $data->status = 1;
        $data->save();
        
        $user = User::find(Auth::user()->id);
        $user->deposit_balance = $user->deposit_balance - $package->invest_amount;
        $user->save();

        return redirect()->back()->with('message','Investment successful');
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
            'package_id' => 'required',
            'deposit_account' => 'required',
            'phone' => 'required',
            'receipt' => 'required',
        ]);
        
        $package = InvestmentPackage::find($request->input('package_id'));

        $data = new InvestmentPackageBook();
        $data->package_id = $request->input('package_id');
        $data->user_id = Auth::user()->id;
        $data->invest_amount = $package->invest_amount;
        $data->profit_per = $package->profit_per;
        $data->duration = $package->duration;
        $data->final_amount = $package->invest_amount + (($package->invest_amount * $package->profit_per)/100);
        $data->account_id = $request->input('deposit_account');
        $data->phone = $request->input('phone');
        $data->transaction_id = $request->input('transaction_id');
        $image = $request->file('receipt');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/deposit/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
        }
        $data->receipt = $image_url;
        $data->status = 0;
        $data->save();

        return redirect()->back()->with('message','Investment successful');
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
