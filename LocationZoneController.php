<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Role;
use App\Models\Admin\Website;
use App\Models\Admin\Country;
use App\Models\Admin\LocationZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class LocationZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::all();
        $zones = LocationZone::all();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.zone', compact('zones', 'countries', 'website'));
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
            'name' => 'required|unique:location_zones|max:100',
        ],[
            'name.required'=> 'Please give a unique zone name.'
        ]);

        $zone = new LocationZone();
        $zone->name = Str::ucfirst($request->input('name'));
        $zone->save();

        return redirect()->back()->with('message','Zone added Successfully');
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
            'name' => 'required|max:100',
        ],[
            'name.required'=> 'Please give a unique zone name.'
        ]);

        $zone = LocationZone::find($id);
        $zone->name = Str::ucfirst($request->input('name'));
        $zone->save();

        return redirect()->back()->with('message','Zone updated Successfully');
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
