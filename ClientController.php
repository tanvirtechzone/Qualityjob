<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Client;
use App\Models\Admin\Website;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $photos = Client::all();
        $website = Website::latest()->first();
        return view('backend.pages.client.index', compact('photos', 'website'));
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

        $client = new Client();
        $client->text = Str::ucfirst($request->input('text'));
        $client->link = $request->input('link');

        $image = $request->file('image');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/client/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            if ($success) {
                $client->image = $image_url;
                $client_insert = $client->save();
                if ($client_insert) {
                    return redirect()->back()->with('message','Client added Successfully');
                }else{
                    return redirect()->back()->with('error','Client dose not added!');
                }
            }else{
                return redirect()->back()->with('error','Client not store to folder!');
            }
        }else{
            return redirect()->back()->with('error','May be image selection problem!');
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
        $old_img = DB::table('clients')->where('id', $id)->first();
        $old_img_path = $old_img->image;
        if($old_img_path){
            unlink($old_img_path);
            $delete = DB::table('clients')->where('id', $id)->delete();
            if($delete){
                return redirect()->back()->with('message','Client deleted Successfully!');
            }else{
                return redirect()->back()->with('error','Have some errors!!');
            }
        }else{
            $delete = DB::table('clients')->where('id', $id)->delete();
            if($delete){
                return redirect()->back()->with('message','Client deleted Successfully!');
            }else{
                return redirect()->back()->with('error','Have some errors!!');
            }
        }
    }
}
