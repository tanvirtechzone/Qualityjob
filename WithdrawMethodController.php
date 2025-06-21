<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Website;
use App\Models\WithdrawMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WithdrawMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = WithdrawMethod::all();
        $website = Website::latest()->first();
        $title = "Withdraw Method";
        return view('backend.pages.system-setting.withdraw-method', compact('title', 'datas', 'website'));
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
            'name' => 'required|unique:withdraw_methods|max:100',
        ],[
            'name.required'=> 'Please give a unique method name.'
        ]);

        $method = new WithdrawMethod();
        $method->name = Str::ucfirst($request->input('name'));
        $method->save();

        return redirect()->back()->with('message','Method added Successfully');
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
        ]);

        $method = WithdrawMethod::find($id);
        $method->name = Str::ucfirst($request->input('name'));
        $method->status = $request->input('status');
        $method->save();

        return redirect()->back()->with('message','Method Update Successfully');
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
