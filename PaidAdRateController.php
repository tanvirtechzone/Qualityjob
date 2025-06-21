<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Website;
use App\Models\Admin\PaidAdRate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaidAdRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = PaidAdRate::all();
        $website = Website::latest()->first();
        $title = "Ads Rate";
        return view('backend.pages.system-setting.ads-rate', compact('title', 'datas', 'website'));
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
            'duration' => 'required|unique:paid_ad_rates',
        ]);

        $date = new PaidAdRate();
        $date->duration = $request->input('duration');
        $date->rate = $request->input('rate');
        $date->save();

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
            'rate' => 'required|max:100',
        ]);

        $date = PaidAdRate::find($id);
        $date->duration = $request->input('duration');
        $date->rate = $request->input('rate');
        $date->status = $request->input('status');
        $date->save();

        return redirect()->back()->with('message','Data Update Successfully');
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
