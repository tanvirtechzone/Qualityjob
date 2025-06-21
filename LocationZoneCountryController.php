<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Country;
use App\Models\Admin\LocationZone;
use App\Models\Admin\LocationZoneCountry;
use App\Models\Admin\Website;
use Illuminate\Http\Request;

class LocationZoneCountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $countries = Country::all();
        $zone_countries = LocationZoneCountry::all();
        $zone = LocationZone::find($id);
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.zone-country', compact('zone', 'zone_countries', 'countries', 'website'));
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
        $check_country = LocationZoneCountry::where('zone_id', $request->zone_id)->where('country_id', $request->country_id)->first();
        if($check_country){
            return redirect()->back()->with('error','Country exist in this zone!');
        }else{
            $zone_country = new LocationZoneCountry();
            $zone_country->zone_id = $request->zone_id;
            $zone_country->country_id = $request->country_id;
            $zone_country->save();

            return redirect()->back()->with('message','Country added Successfully');
        }
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
        $zone_country = LocationZoneCountry::find($id);
        $zone_country->delete();

        return redirect()->back()->with('message','Country deleted Successfully');
    }
}
