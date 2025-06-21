<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Website;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $website = Website::latest()->first();
        $id  = Auth::user()->id;
        $profile_info = DB::table('users')
                ->join('roles', 'users.role_id', 'roles.id')
                ->select('users.*', 'roles.name as role_name')
                ->where('users.id', $id)
                ->first();
        return view('backend.pages.profile.usersetting', compact('profile_info', 'website'));
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
            'old_password' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required'
        ]);

        $hashedPassword = Auth::user()->password;
        if(Hash::check($request->old_password,$hashedPassword)){
            if (!Hash::check($request->password,$hashedPassword))
            {
                $user = User::find(Auth::id());
                $user->password = Hash::make($request->password);
                $pass_update = $user->save();
                // Toastr::success('Password Successfully Changed','Success');
                // Auth::logout();
                // return redirect()->back();
                if($pass_update){
                    return redirect()->route('admin.setting')->with('message','Password Upadated Successfully!');
                }else{
                    return redirect()->route('admin.setting')->with('error','Password Not Updated');
                }
            } else {
                return redirect()->route('admin.setting')->with('error','New password cannot be the same as old password.');
            }
        }else{
            return redirect()->route('admin.setting')->with('error','Old Password Not Matched');
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
