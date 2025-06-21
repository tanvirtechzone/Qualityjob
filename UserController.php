<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use Illuminate\Http\Request;
use App\Models\Admin\Website;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $website = Website::latest()->first();
        $users = User::where('role_id', 3)->latest()->paginate(15);
        $roles = Role::all()->where('id', '!=', '3');

        return view('backend.pages.usermanage.user', compact('users', 'roles', 'website'));
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
            'name' => 'required|min:3|max:50',
            'username' => 'required|min:3|max:50',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'role_id' => 'required',
            'password' => 'required',
        ]);

        $user = new User();
        $user->name = Str::ucfirst($request->input('name'));
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->role_id = $request->input('role_id');
        $user->status = '0';
        $user->password = Hash::make($request->input('password'));
        $user->phone = $request->input('phone');

        $image = $request->file('image');
        if ($image) {
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/user/';
            $image_url = $upload_path.$image_full_name;
            $image->move($upload_path, $image_full_name);
            $user->image = $image_url;
        }
        
        $user->save();
        return redirect()->back()->with('message','User added Successfully');
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
        $website = Website::latest()->first();
        $user = DB::table('users')->where('id', $id)->first();
        $roles = Role::all()->where('id', '!=', '3');
        return view('backend.pages.usermanage.useredit', compact('user', 'website', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user_balance(Request $request, $id){
        $user = User::find($id);
        $user->deposit_balance = $request->deposit_balance;
        $user->earning_balance = $request->earning_balance;
        $user->save();

        return redirect()->back()->with('message','Data Updated Successfully!');
    }
    public function user_activity(Request $request, $id){
        $user = User::find($id);
        $user->status = $request->status;
        $user->reason = $request->reason;
        $user->save();

        return redirect()->back()->with('message','Data Updated Successfully!');
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:3|max:50',
            'email' => 'required',
        ]);
        
        $user = User::find($id);
        $user->name = Str::ucfirst($request->input('name'));
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->role_id = $request->input('role_id');
        $user->phone = $request->input('phone');

        $image = $request->file('image');
        if ($image) {
            if(file_exists($user->image)){
                unlink($user->image);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/user/';
            $image_url = $upload_path.$image_full_name;
            $image->move($upload_path, $image_full_name);
            $user->image = $image_url;
        }
        
        $user->save();
        return redirect()->back()->with('message','User update Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(file_exists($user->image)){
            unlink($user->image);
        }
        $user->delete();
        return redirect()->back()->with('message','User deleted Successfully!');
    }
}
