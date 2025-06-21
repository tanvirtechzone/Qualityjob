<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Role;
use App\Models\Admin\Website;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        $website = Website::latest()->first();
        return view('backend.pages.usermanage.role', compact('roles', 'website'));
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
            'name' => 'required|unique:roles|max:100',
        ],[
            'name.required'=> 'Please give a unique role name.'
        ]);

        $role = new Role();

        $role->name = Str::ucfirst($request->input('name'));
        $role->slug = Str::slug($request->input('name'));

        $role->save();

        return redirect()->route('admin.role')->with('message','Role added Successfully');
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
            'name' => 'required|max:100',
        ],[
            'name.required'=> 'Please give a unique role name.'
        ]);

        $data = array();

        $data['name'] = Str::ucfirst($request->input('name'));
        $data['slug'] = Str::slug($request->input('name'));

        $update = DB::table('roles')->where('id', $id)->update($data);

        if($update){
            return redirect()->route('admin.role')->with('message','Role updated Successfully!');
        }else{
            return redirect()->route('admin.role')->with('error','Role dose not updated Successfully!');
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
