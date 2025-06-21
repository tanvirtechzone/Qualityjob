<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Website;
use App\Models\GoogleAd;
use Illuminate\Http\Request;

class GoogleAdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $website = Website::latest()->first();
        $google_ads = GoogleAd::orderBy('id', 'ASC')->get();

        return view('backend.pages.company.google-ad', compact('website', 'google_ads'));
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
        $validatedData = $request->validate([
            'position' => 'required',
            'code' => 'required',
        ]);

        $data = new GoogleAd();
        $data->position = $request->input('position');
        $data->code = $request->input('code');
        $data->save();

        return redirect()->back()->with('message','Ad added Successfully');
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
        $validatedData = $request->validate([
            'position' => 'required',
            'code' => 'required',
        ]);

        $data = GoogleAd::find($id);
        $data->position = $request->input('position');
        $data->code = $request->input('code');
        $data->save();

        return redirect()->back()->with('message','Ad updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = GoogleAd::find($id);
        $data->delete();

        return redirect()->back()->with('message','Ad deleted Successfully');
    }
}
