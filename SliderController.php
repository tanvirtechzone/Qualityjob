<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Slider;
use App\Models\Admin\Website;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Slider::all();
        $website = Website::latest()->first();
        return view('backend.pages.website.slider', compact('sliders', 'website'));
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
            'image' => 'required',
        ]);

        $data = array();
        $slider = new Slider();
        $slider->text = Str::ucfirst($request->input('text'));
        $slider->link = $request->input('link');

        $image = $request->file('image');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/slider/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            if ($success) {
                $slider->image = $image_url;
                $slider_insert = $slider->save();
                if ($slider_insert) {
                    return redirect()->route('admin.slider')->with('message','Banner added Successfully');
                }else{
                    return redirect()->route('admin.slider')->with('error','Banner dose not added!');
                }
            }else{
                return redirect()->route('admin.slider')->with('error','Image not store to folder!');
            }
        }else{
            return redirect()->route('admin.slider')->with('error','May be image selection problem!');
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
        $old_img = DB::table('sliders')->where('id', $id)->first();
        $old_img_path = $old_img->image;
        if($old_img_path){
            unlink($old_img_path);
            $delete = DB::table('sliders')->where('id', $id)->delete();
            if($delete){
                return redirect()->route('admin.slider')->with('message','Banner deleted Successfully!');
            }else{
                return redirect()->route('admin.slider')->with('error','Have some errors!!');
            }
        }else{
            $delete = DB::table('sliders')->where('id', $id)->delete();
            if($delete){
                return redirect()->route('admin.slider')->with('message','Banner deleted Successfully!');
            }else{
                return redirect()->route('admin.slider')->with('error','Have some errors!!');
            }
        }
    }
}
