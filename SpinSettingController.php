<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\SpinSetting;
use Illuminate\Http\Request;
use App\Models\Admin\Website;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\News;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SpinSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $website = Website::latest()->first();
        $data = SpinSetting::latest()->first();

        return view('backend.pages.company.spin-setting', compact('data', 'website'));
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
        $data = SpinSetting::find($id);
        $data->part_one_bg = $request->part_one_bg;
        $data->part_one_mark = $request->part_one_mark;
        $data->part_two_bg = $request->part_two_bg;
        $data->part_two_mark = $request->part_two_mark;
        $data->part_three_bg = $request->part_three_bg;
        $data->part_three_mark = $request->part_three_mark;
        $data->part_four_bg = $request->part_four_bg;
        $data->part_four_mark = $request->part_four_mark;
        $data->part_five_bg = $request->part_five_bg;
        $data->part_five_mark = $request->part_five_mark;
        $data->part_six_bg = $request->part_six_bg;
        $data->part_six_mark = $request->part_six_mark;
        $data->part_seven_bg = $request->part_seven_bg;
        $data->part_seven_mark = $request->part_seven_mark;
        $data->daily_spin = $request->daily_spin;
        $data->status = $request->status;
        $data->save();
        return redirect()->back()->with('message','Data updated successfully');
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
