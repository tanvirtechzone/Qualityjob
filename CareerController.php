<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Career;
use App\Models\Admin\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $website = Website::latest()->first();
        $career = Career::latest()->first();

        return view('backend.pages.company.career', compact('career', 'website'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'details' => 'required',
        ]);

        $career = Career::find($id);
        $career->details = Str::ucfirst($request->details);

        $image = $request->file('image');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/career/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            if ($success) {
                $old_img = DB::table('careers')->where('id', $id)->select('image')->first();
                if ($old_img->image) {
                    unlink($old_img->image);
                }
                $career->image = $image_url;
                $career_insert = $career->save();
                if ($career_insert) {
                    return redirect()->route('admin.career')->with('message', 'Career us updated successfully');
                } else {
                    return redirect()->route('admin.career')->with('error', 'Career us dose not updated!');
                }
            } else {
                return redirect()->route('admin.career')->with('error', 'Image not store to folder!');
            }
        }

        $career->save();

        return redirect()->route('admin.career')->with('message', 'Career us updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
