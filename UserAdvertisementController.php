<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Advertisement;
use App\Models\Admin\MainWallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserAdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.pages.ads');
    }

    public function advertisement_list()
    {
        $datas = Advertisement::where('user_id', Auth::user()->id)->latest()->get();
        return view('user.pages.ads-list', compact('datas'));
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
            'title' => 'required',
            'image' => 'required',
            'days' => 'required',
        ]);

        $days = $request->days;
        if(Auth::user()->deposit_balance < $days){
            return redirect()->back()->with('error','You have no sufficient balance for ad.');
        }else{
            $user_balance = User::find(Auth::user()->id);
            $user_balance->deposit_balance = $user_balance->deposit_balance - $days;
            $user_balance->save();

            $main_wallet = MainWallet::latest()->first();
            $main_wallet->amount = $main_wallet->amount + $days;
            $main_wallet->save();
        }

        $today = date("Y-m-d");
        $exp_date = date( "Y-m-d", strtotime( "$today +$days day" ) );

        $category = new Advertisement();
        $category->title = $request->input('title');
        $category->link = $request->input('link');

        $image = $request->file('image');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/ads/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
        }
        $category->image = $image_url;
        $category->exp_date = $exp_date;
        $category->duration = $days;
        $category->cost = $days;
        $category->user_id = Auth::user()->id;
        $category->save();

        return redirect()->back()->with('message','Ad posted successfully');
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
