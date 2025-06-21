<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Website;
use App\Models\BoostCategory;
use App\Models\BoostSubCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class BoostSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorys = BoostCategory::orderBy('id', 'ASC')->get();
        $sub_categorys = BoostSubCategory::orderBy('id', 'ASC')->get();
        $website = Website::latest()->first();
        return view('backend.pages.boost-manage.sub-category', compact('categorys', 'sub_categorys', 'website'));
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
            'name' => 'required',
            'category_id' => 'required',
        ]);

        $category = new BoostSubCategory();
        $category->category_id = $request->input('category_id');
        $category->name = Str::ucfirst($request->input('name'));
        $category->slug = Str::slug($request->input('name'));
        $category->cost = $request->input('cost');
        $category->notice = $request->input('notice');

        $image = $request->file('image');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/category/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $category->image = $image_url;
        }

        $category->save();

        return redirect()->back()->with('message','Category added Successfully');
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
            'name' => 'required',
            'category_id' => 'required',
        ]);

        $category = BoostSubCategory::find($id);
        $category->category_id = $request->input('category_id');
        $category->name = Str::ucfirst($request->input('name'));
        $category->slug = Str::slug($request->input('name'));
        $category->cost = $request->input('cost');
        $category->notice = $request->input('notice');

        $image = $request->file('image');
        if ($image) {
            if(file_exists($category->image)){
                unlink($category->image);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/category/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $category->image = $image_url;
        }

        $category->save();

        return redirect()->back()->with('message','Category added Successfully');
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
