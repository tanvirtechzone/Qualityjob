<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Advertisement;
use App\Models\Admin\MainWallet;
use App\Models\Admin\Website;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = date('Y-m-d');
        $datas = Advertisement::where('approval', 1)->where('exp_date', '>=', $today)->get();
        $website = Website::latest()->first();
        $title = 'Ads List';
        return view('backend.pages.system-setting.advertisement', compact('title', 'datas', 'website'));
    }

    public function pending_advertisement()
    {
        $today = date('Y-m-d');
        $datas = Advertisement::where('approval', 0)->where('exp_date', '>=', $today)->get();
        $website = Website::latest()->first();
        $title = 'Pending Ads List';
        return view('backend.pages.system-setting.advertisement', compact('title', 'datas', 'website'));
    }

    public function expired_advertisement()
    {
        $today = date('Y-m-d');
        $datas = Advertisement::where('exp_date', '<', $today)->latest()->get();
        $website = Website::latest()->first();
        $title = 'Expired Ads List';
        return view('backend.pages.system-setting.advertisement', compact('title', 'datas', 'website'));
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
            'image' => 'required',
        ]);

        $data = new Advertisement();
        $data->user_id = Auth::user()->id;
        $data->title = Str::ucfirst($request->input('title'));
        $data->link = $request->input('link');

        $image = $request->file('image');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/ads/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $data->image = $image_url;
        }

        $data->approval = $request->input('approval');
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
        $data = Advertisement::find($id);
        $data->approval = $request->input('approval');

        if($request->approval == 2){
            $user = User::find($data->user_id);
            $user->deposit_balance = $user->deposit_balance + $data->cost;
            $user->save();
        }

        $data->reason = $request->input('reason');
        $data->save();

        return redirect()->back()->with('message','Data added Successfully');
    }

    public function exp_dade_update(Request $request, $id)
    {
        $data = Advertisement::find($id);

        $today = date('Y-m-d');
        $exp_date = $request->input('exp_date');
        $days = (strtotime($exp_date)- strtotime($today))/24/3600;
        $ad = Advertisement::find($id);
        $check_user = User::find($ad->user_id);
        if($check_user->deposit_balance < $days){
            return redirect()->back()->with('error','User have no sufficient balance for ad.');
        }else{
            $user_balance = User::find($check_user->id);
            $user_balance->deposit_balance = $user_balance->deposit_balance - $days;
            $user_balance->save();

            $main_wallet = MainWallet::latest()->first();
            $main_wallet->amount = $main_wallet->amount + $days;
            $main_wallet->save();
        }

        $data->exp_date = $request->input('exp_date');
        $data->save();

        return redirect()->back()->with('message','Data added Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Advertisement::find($id);
        if(file_exists($data->image)){
            unlink($data->image);
        }
        $data->delete();

        return redirect()->back()->with('message','Data deleted Successfully');
    }
}
