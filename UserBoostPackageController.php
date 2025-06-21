<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\MainWallet;
use App\Models\Admin\Website;
use App\Models\User;
use App\Models\UserBoostPackage;
use Illuminate\Http\Request;

class UserBoostPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = UserBoostPackage::latest()->get();
        $website = Website::latest()->first();
        $title = 'Boost Package List';

        return view('backend.pages.boost-manage.index', compact('title', 'website', 'datas'));
    }

    public function process($id)
    {
        $boost_package = UserBoostPackage::find($id);
        $boost_package->status = 1;
        $boost_package->save();

        return redirect()->back()->with('message','Boost Package process successfully');
    }

    public function inprocess($id)
    {
        $boost_package = UserBoostPackage::find($id);
        $boost_package->status = 2;
        $boost_package->save();

        return redirect()->back()->with('message','Boost Package inprocess successfully');
    }

    public function complete($id)
    {
        $boost_package = UserBoostPackage::find($id);
        $boost_package->status = 4;
        $boost_package->save();

        return redirect()->back()->with('message','Boost Package approved successfully');
    }

    public function reject(Request $request, $id)
    {
        $boost_package = UserBoostPackage::find($id);

        $user = User::find($boost_package->user_id);
        $user->deposit_balance = $user->deposit_balance + $boost_package->cost;
        $user->save();

        $check_main_wallet = MainWallet::latest()->first();
        $main_wallet = MainWallet::find($check_main_wallet->id);
        $main_wallet->amount = $main_wallet->amount - $boost_package->cost;
        $main_wallet->save();

        $boost_package->status = 3;
        $boost_package->reason = $request->reason;
        $boost_package->save();

        return redirect()->back()->with('message','Boost Package Rejected successfully');
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
        $boost_package = UserBoostPackage::find($id);
        $boost_package->delete();

        return redirect()->back()->with('message','Boost Package deleted successfully');
    }
}
