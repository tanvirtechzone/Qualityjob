<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Country;
use App\Models\User;
use App\Models\Admin\UserDailySpin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.pages.profile');
    }
    
    public function user_profile($id)
    {
        $user = User::find($id);
        return view('user.pages.user-profile', compact('user'));
    }
    
    public function add_spin_mark_to_earning(Request $request){
        $user = User::find(Auth::user()->id);
        $user->earning_balance = $user->earning_balance + $request->mark;
        $user->save();
        
        $uspin = new UserDailySpin();
        $uspin->user_id = Auth::user()->id;
        $uspin->spin_amount = $request->mark;
        $uspin->save();
        
        return 'Updated';
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
    public function edit()
    {
        $countries = Country::orderBy('name', 'ASC')->get();
        return view('user.pages.profile-manage', compact('countries'));
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
        ]);
        $user = User::find($id);
        if(Auth::user()->code == NULL){
            $last_ac = User::select('id')->latest()->first();
            if (isset($last_ac)) {
                $code = sprintf('%04d', $last_ac->id + 1000001);
            } else {
                $code = sprintf('%04d', 1000001);
            }
            $user->code = $code;
        }

        $user->name = $request->name;
        $user->phone = $request->phone;

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
            $success = $image->move($upload_path, $image_full_name);
            $user->image = $image_url;
        }

        if($request->password != ''){
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return redirect()->back()->with('message','Data updated Successfully!');
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
