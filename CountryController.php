<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Website;
use App\Models\Admin\Continent;
use App\Models\Admin\Country;
use App\Models\Admin\ContinentCountry;
use Illuminate\Support\Str;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $continents = Continent::orderBy('id', 'ASC')->get();
        $countries = Country::orderBy('id', 'ASC')->get();
        $website = Website::latest()->first();
        return view('backend.pages.system-setting.country', compact('countries', 'continents', 'website'));
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
            'continent_id' => 'required',
            'name' => 'required|unique:countries|max:100',
        ],[
            'name.required'=> 'Please give a unique country name.'
        ]);

        $country = new Country();
        // $country->continent_id = $request->input('continent_id');
        $country->name = Str::ucfirst($request->input('name'));
        $country->save();
        
        $check_country = Country::where('name', $request->input('name'))->first();
        foreach($request->input('continent_id') as $continent_id){
            $check_exist = ContinentCountry::where('continent_id', $continent_id)->where('name', $check_country->id)->first();
            if(!$check_exist){
                $cont_country = new ContinentCountry();
                $cont_country->continent_id = $continent_id;
                $cont_country->country_id = $check_country->id;
                $cont_country->save();
            }
        }

        return redirect()->back()->with('message','Country added Successfully');
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
            'continent_id' => 'required',
            'name' => 'required|max:100',
        ],[
            'name.required'=> 'Please give a unique country name.'
        ]);

        $country = Country::find($id);
        // $country->continent_id = $request->input('continent_id');
        $country->name = Str::ucfirst($request->input('name'));
        $country->save();
        
        ContinentCountry::where('country_id', $id)->delete();
        foreach($request->input('continent_id') as $continent_id){
            $check_exist = ContinentCountry::where('continent_id', $continent_id)->where('country_id', $id)->first();
            if(!$check_exist){
                $cont_country = new ContinentCountry();
                $cont_country->continent_id = $continent_id;
                $cont_country->country_id = $id;
                $cont_country->save();
            }
        }

        return redirect()->back()->with('message','Country added Successfully');
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
