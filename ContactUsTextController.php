<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\ContactUsText;
use App\Models\Admin\Website;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactUsTextController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $website = Website::latest()->first();
        $contact_info = ContactUsText::latest()->first();

        return view('backend.pages.company.contact-us-info', compact('contact_info', 'website'));
    }

    public function contact_msg()
    {
        $website = Website::latest()->first();
        $contact_msgs = ContactMessage::orderBy('id', 'DESC')->get();

        return view('backend.pages.company.contact-msg', compact('contact_msgs', 'website'));
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

        $contact_info = ContactUsText::find($id);
        $contact_info->details = Str::ucfirst($request->details);
        $contact_info->save();

        return redirect()->route('admin.contact_info')->with('message', 'Contact information updated successfully');
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
