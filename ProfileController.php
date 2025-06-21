<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Role;
use App\Models\User;
use App\Models\Admin\Website;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id  = Auth::user()->id;
        $profile_info = DB::table('users')
                ->join('roles', 'users.role_id', 'roles.id')
                ->select('users.*', 'roles.name as role_name')
                ->where('users.id', $id)
                ->first();

        $website = Website::latest()->first();

        return view('backend.pages.profile.profile', compact('profile_info', 'website'));
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
        $validatedData = $request->validate([
            'name' => 'required|min:3|max:50',
            'email' => 'required',
        ]);

        $data = array();

        $data['name'] = Str::ucfirst($request->input('name'));
        $data['username'] = $request->input('username');
        $data['email'] = $request->input('email');
        $data['phone'] = $request->input('phone');
        $data['present_address'] = $request->input('present_address');
        $data['permanent_address'] = $request->input('permanent_address');

        $image = $request->file('image');

        // Update----------------
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/user/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            if ($success) {
                $data['image'] = $image_url;
                $old_img = DB::table('users')->where('id', $id)->first();
                $old_img_path = $old_img->image;
                if($old_img_path){
                    $old_img_dlt = unlink($old_img_path);
                    if ($old_img_dlt) {
                        $update_user = DB::table('users')->where('id', $id)->update($data);
                        if ($update_user) {
                            return redirect()->route('admin.profile')->with('message','Profile upadated Successfully!');
                        }else{
                            return redirect()->route('admin.profile')->with('error','Profile dose not updated!');
                        }
                    }else{
                        return redirect()->route('admin.profile')->with('error','Old image dose not deleted!');
                    }
                }else{
                    $update_user = DB::table('users')->where('id', $id)->update($data);
                    if ($update_user) {
                        return redirect()->route('admin.profile')->with('message','Profile upadated Successfully!');
                    }else{
                        return redirect()->route('admin.profile')->with('error','Profile dose not updated!');
                    }
                }
            }else{
                return redirect()->route('admin.profile')->with('error','Image not store to folder!');
            }
        }else{
            $update_user = DB::table('users')->where('id', $id)->update($data);
            if ($update_user) {
                return redirect()->route('admin.profile')->with('message','Profile upadated Successfully!');
            }else{
                return redirect()->route('admin.profile')->with('error','Profile dose not updated!');
            }
        }
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
