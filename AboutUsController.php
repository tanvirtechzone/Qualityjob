<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Aboutus;
use Illuminate\Http\Request;
use App\Models\Admin\Website;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\News;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AboutUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $website = Website::latest()->first();
        $abouts = Aboutus::latest()->first();

        return view('backend.pages.company.about-us', compact('abouts', 'website'));
    }
    
    public function login_register_page_info()
    {
        $website = Website::latest()->first();
        $abouts = Aboutus::latest()->first();

        return view('backend.pages.company.login-register-page-info', compact('abouts', 'website'));
    }
    
    public function refer_info()
    {
        $website = Website::latest()->first();
        $abouts = Aboutus::latest()->first();

        return view('backend.pages.company.refer-info', compact('abouts', 'website'));
    }
    
    public function header_info()
    {
        $website = Website::latest()->first();
        $abouts = Aboutus::latest()->first();

        return view('backend.pages.company.headre-info', compact('abouts', 'website'));
    }
    
    public function system_color_setup()
    {
        $website = Website::latest()->first();
        $abouts = Aboutus::latest()->first();

        return view('backend.pages.company.system-color-setup', compact('abouts', 'website'));
    }
    
    public function counter_info()
    {
        $website = Website::latest()->first();
        $abouts = Aboutus::latest()->first();

        return view('backend.pages.company.counter-info', compact('abouts', 'website'));
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
    public function refer_info_update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'refer_details' => 'required'
        ]);
        
        $abouts = Aboutus::find($id);
        $abouts->refer_title = Str::ucfirst($request->refer_title);
        $abouts->refer_details = Str::ucfirst($request->refer_details);
        $abouts->save();
        return redirect()->back()->with('message','Data updated successfully');
    }
    
    public function login_register_page_info_update(Request $request, $id)
    {
        $abouts = Aboutus::find($id);
        $abouts->login_page_title = Str::ucfirst($request->login_page_title);
        $abouts->login_form_title = Str::ucfirst($request->login_form_title);
        $abouts->login_page_tcontent = Str::ucfirst($request->login_page_tcontent);
        $abouts->register_page_title = Str::ucfirst($request->register_page_title);
        $abouts->register_form_title = Str::ucfirst($request->register_form_title);
        $abouts->register_page_content = Str::ucfirst($request->register_page_content);
        $abouts->forget_page_title = Str::ucfirst($request->forget_page_title);
        $abouts->forget_page_tcontent = Str::ucfirst($request->forget_page_tcontent);
        $abouts->forget_page_form_title = Str::ucfirst($request->forget_page_form_title);
        $abouts->otp_check_page_title = Str::ucfirst($request->otp_check_page_title);
        $abouts->otp_check_page_tcontent = Str::ucfirst($request->otp_check_page_tcontent);
        $abouts->otp_check_page_form_title = Str::ucfirst($request->otp_check_page_form_title);
        $abouts->save();
        return redirect()->back()->with('message','Data updated successfully');
    }
    
    public function system_color_setup_update(Request $request, $id)
    {
        $abouts = Aboutus::find($id);
        $abouts->menubar_color = $request->menubar_color;
        $abouts->menubar_text_color = $request->menubar_text_color;
        $abouts->menubar_overlay_color = $request->menubar_overlay_color;
        $abouts->menubar_overlay_text_color = $request->menubar_overlay_text_color;
        $abouts->header_bg = $request->header_bg;
        $abouts->header_text_color = $request->header_text_color;
        $abouts->footer_bg = $request->footer_bg;
        $abouts->footer_text_color = $request->footer_text_color;
        $abouts->button_color = $request->button_color;
        $abouts->button_text_color = $request->button_text_color;
        $abouts->button_hover_color = $request->button_hover_color;
        $abouts->button_hover_text_color = $request->button_hover_text_color;
        $abouts->headline_bg_color = $request->headline_bg_color;
        $abouts->headline_text_color = $request->headline_text_color;
        $abouts->service_bg = $request->service_bg;
        $abouts->service_text_color = $request->service_text_color;
        $abouts->service_hover_bg = $request->service_hover_bg;
        $abouts->service_hover_text_color = $request->service_hover_text_color;
        $abouts->refer_area_bg = $request->refer_area_bg;
        $abouts->refer_area_text_color = $request->refer_area_text_color;
        $abouts->login_register_title_color = $request->login_register_title_color;
        $abouts->login_register_content_bg = $request->login_register_content_bg;
        $abouts->login_register_content_color = $request->login_register_content_color;
        $abouts->login_register_form_title_bg = $request->login_register_form_title_bg;
        $abouts->login_register_form_title_color = $request->login_register_form_title_color;
        $abouts->login_register_form_bg = $request->login_register_form_bg;
        $abouts->user_db_sidebar_bg = $request->user_db_sidebar_bg;
        $abouts->user_db_sidebar_text = $request->user_db_sidebar_text;
        $abouts->user_db_sidebar_menu_active = $request->user_db_sidebar_menu_active;
        $abouts->user_db_navbar_bg = $request->user_db_navbar_bg;
        $abouts->user_db_navbar_text = $request->user_db_navbar_text;
        $abouts->user_db_bg = $request->user_db_bg;
        $abouts->job_create_point = $request->job_create_point;
        $abouts->job_create_next = $request->job_create_next;
        $abouts->job_create_back = $request->job_create_back;
        $abouts->button_disable = $request->button_disable;
        $abouts->user_panel_logo_area = $request->user_panel_logo_area;
        $abouts->deposit_balance_bg = $request->deposit_balance_bg;
        $abouts->earning_balance_bg = $request->earning_balance_bg;
        $abouts->paid_area_ad_bg = $request->paid_area_ad_bg;
        $abouts->user_panel_main_area = $request->user_panel_main_area;
        $abouts->save();
        return redirect()->back()->with('message','Data updated successfully');
    }
    
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'details' => 'required'
        ]);

        $abouts = Aboutus::find($id);
        $abouts->details = Str::ucfirst($request->details);
        $image = $request->file('image');
        if ($image) {
            if(file_exists($abouts->image)){
                unlink($abouts->image);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->image = $image_url;
        }

        $abouts->save();
        return redirect()->back()->with('message','Data updated successfully');
    }
    
    public function counter_info_update(Request $request, $id)
    {
        $abouts = Aboutus::find($id);
        
        $abouts->total_job_title = Str::ucfirst($request->total_job_title);
        $image = $request->file('total_job_icon');
        if ($image) {
            if(file_exists($abouts->total_job_icon)){
                unlink($abouts->total_job_icon);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->total_job_icon = $image_url;
        }
        $abouts->total_job_status = $request->total_job_status;
        $abouts->total_job = $request->total_job;
        $abouts->total_job_manual_show = $request->total_job_manual_show;
        
        $abouts->total_user_title = Str::ucfirst($request->total_user_title);
        $image = $request->file('total_user_icon');
        if ($image) {
            if(file_exists($abouts->total_user_icon)){
                unlink($abouts->total_user_icon);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->total_user_icon = $image_url;
        }
        $abouts->total_user_status = $request->total_user_status;
        $abouts->total_user = $request->total_user;
        $abouts->total_user_manual_show = $request->total_user_manual_show;
        
        $abouts->totle_work_done_title = Str::ucfirst($request->totle_work_done_title);
        $image = $request->file('totle_work_done_icon');
        if ($image) {
            if(file_exists($abouts->totle_work_done_icon)){
                unlink($abouts->totle_work_done_icon);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->totle_work_done_icon = $image_url;
        }
        $abouts->totle_work_done_status = $request->totle_work_done_status;
        $abouts->totle_work_done = $request->totle_work_done;
        $abouts->totle_work_done_manual_show = $request->totle_work_done_manual_show;
        
        $abouts->total_withdraw_title = Str::ucfirst($request->total_withdraw_title);
        $image = $request->file('total_withdraw_icon');
        if ($image) {
            if(file_exists($abouts->total_withdraw_icon)){
                unlink($abouts->total_withdraw_icon);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->total_withdraw_icon = $image_url;
        }
        $abouts->total_withdraw_status = $request->total_withdraw_status;
        $abouts->total_withdraw = $request->total_withdraw;
        $abouts->paid_tast_manual_show = $request->paid_tast_manual_show;

        $abouts->save();
        return redirect()->back()->with('message','Data updated successfully');
    }
    
    public function header_info_update(Request $request, $id)
    {
        $abouts = Aboutus::find($id);
        
        $abouts->slider_title = Str::ucfirst($request->slider_title);
        $image = $request->file('slider_image_one');
        if ($image) {
            if(file_exists($abouts->slider_image_one)){
                unlink($abouts->slider_image_one);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->slider_image_one = $image_url;
        }
        $abouts->slider_image_one_status = $request->slider_image_one_status;
        
        $image = $request->file('slider_image_two');
        if ($image) {
            if(file_exists($abouts->slider_image_two)){
                unlink($abouts->slider_image_two);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->slider_image_two = $image_url;
        }
        $abouts->slider_image_two_status = $request->slider_image_two_status;
        
        $image = $request->file('slider_image_three');
        if ($image) {
            if(file_exists($abouts->slider_image_three)){
                unlink($abouts->slider_image_three);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->slider_image_three = $image_url;
        }
        $abouts->slider_image_three_status = $request->slider_image_three_status;
        
        $image = $request->file('slider_image_four');
        if ($image) {
            if(file_exists($abouts->slider_image_four)){
                unlink($abouts->slider_image_four);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->slider_image_four = $image_url;
        }
        $abouts->slider_image_four_status = $request->slider_image_four_status;
        
        $image = $request->file('slider_image_five');
        if ($image) {
            if(file_exists($abouts->slider_image_five)){
                unlink($abouts->slider_image_five);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->slider_image_five = $image_url;
        }
        $abouts->slider_image_five_status = $request->slider_image_five_status;
        
        $image = $request->file('slider_image_six');
        if ($image) {
            if(file_exists($abouts->slider_image_six)){
                unlink($abouts->slider_image_six);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->slider_image_six = $image_url;
        }
        $abouts->slider_image_six_status = $request->slider_image_six_status;
        
        $image = $request->file('slider_image_seven');
        if ($image) {
            if(file_exists($abouts->slider_image_seven)){
                unlink($abouts->slider_image_seven);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/about-us/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $abouts->slider_image_seven = $image_url;
        }
        $abouts->slider_image_seven_status = $request->slider_image_seven_status;

        $abouts->save();
        return redirect()->back()->with('message','Data updated successfully');
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
