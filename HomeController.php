<?php

namespace App\Http\Controllers;

use App\Models\Admin\Aboutus;
use App\Models\Admin\Career;
use App\Models\Admin\Category;
use App\Models\Admin\Client;
use App\Models\Admin\ContactUsText;
use App\Models\Admin\HeavyEquipment;
use App\Models\Admin\PhotoGallery;
use App\Models\Admin\ProjectOverview;
use App\Models\Admin\Service;
use App\Models\Admin\Slider;
use App\Models\Admin\Website;
use App\Models\Admin\Project;
use App\Models\Admin\WelcomeBonus;
use Illuminate\Support\Facades\Validator;
use App\Models\Job;
use App\Models\JobWork;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (Auth::check()) {
            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
                return redirect()->route('admin.dashboard');
            }elseif (auth()->user()->role_id == 3) {
                return redirect()->route('user.dashboard');
            }
        }

        $request->session()->put('login_erro', '');
        $request->session()->put('mail_sent', '');
        
        $slider = Slider::all();
        $website = Website::latest()->first();
        $aboutus = Aboutus::latest()->first();
        $services = Service::orderBy('id', 'DESC')->latest()->limit('6')->get();
        $p_categorys = Category::orderBy('id', 'DESC')->latest()->get();
        $clients = Client::orderBy('id', 'DESC')->latest()->get();
        $jobs = Job::where('status', 1)->where('pause', 0)->where('worker_need', '!=', 'worker_confirmed')->orderBy('created_at', 'DESC')->limit(6)->get();

        return view('frontend.pages.home', compact('slider', 'website', 'aboutus', 'clients','services', 'p_categorys', 'jobs'));
    }
    
    public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_img()]);
    }

    public function about_us()
    {
        $slider = Slider::latest()->first();
        $website = Website::latest()->first();
        $aboutus = Aboutus::latest()->first();
        $p_categorys = Category::orderBy('id', 'DESC')->latest()->get();

        return view('frontend.pages.about-us', compact('slider', 'website', 'aboutus','p_categorys'));
    }

    public function service()
    {
        $slider = Slider::latest()->first();
        $website = Website::latest()->first();
        $services = Service::orderBy('id', 'DESC')->paginate(18);
        $p_categorys = Category::orderBy('id', 'DESC')->latest()->get();

        return view('frontend.pages.service', compact('slider', 'website', 'services','p_categorys'));
    }

    public function service_details($slug)
    {
        $slider = Slider::latest()->first();
        $website = Website::latest()->first();
        $service = Service::where('slug', $slug)->first();
        $services = Service::orderBy('id', 'DESC')->latest()->limit('5')->get();
        $p_categorys = Category::orderBy('id', 'DESC')->latest()->get();

        return view('frontend.pages.service-details', compact('slider', 'website', 'service', 'services','p_categorys'));
    }

    public function policy_details($slug)
    {
        $website = Website::latest()->first();
        $policy = Policy::where('slug', $slug)->first();

        return view('frontend.pages.policy-details', compact('website', 'policy'));
    }

    public function photo_gallery()
    {
        $slider = Slider::latest()->first();
        $website = Website::latest()->first();
        $photos = PhotoGallery::orderBy('id', 'DESC')->get();
        $p_categorys = Category::orderBy('id', 'DESC')->latest()->get();

        return view('frontend.pages.photo-gallery', compact('slider', 'website', 'photos','p_categorys'));
    }

    public function contact_us()
    {
        $slider = Slider::latest()->first();
        $website = Website::latest()->first();
        $contact_info = ContactUsText::latest()->first();
        $p_categorys = Category::orderBy('id', 'DESC')->latest()->get();

        return view('frontend.pages.contact-us', compact('slider', 'website', 'contact_info','p_categorys'));
    }

    public function career()
    {
        $slider = Slider::latest()->first();
        $website = Website::latest()->first();
        $career = Career::latest()->first();
        $p_categorys = Category::orderBy('id', 'DESC')->latest()->get();

        return view('frontend.pages.career', compact('slider', 'website', 'career','p_categorys'));
    }

    public function job_details($code)
    {
        $job = Job::where('code', $code)->first();
        // if($job->user_id == Auth::user()->id){
        //     return redirect()->back()->with('error','You can not work this job! Because this job posted by you!');
        // }

        $check_work = JobWork::where('user_id', Auth::user()->id)->where('job_id', $job->id)->count();
        if($check_work > 0){
            return redirect()->back()->with('error','Already worked this job!');
        }

        $check_work_done = JobWork::where('job_id', $job->id)->where('status', '!=', 2)->count();
        if($check_work_done >= $job->worker_need){
            return redirect()->back()->with('error','Work limit over. Please try another jobs!');
        }

        $title = 'Job details';

        return view('user.pages.job-details', compact('title', 'job'));
    }

    public function user_register(Request $request)
    {
        // $messages = [
        //     'g-recaptcha-response.required' => 'You must check the Captcha.',
        //     'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        // ];
  
        // $validator = Validator::make($request->all(), [
        //     'g-recaptcha-response' => 'required|captcha'
        // ], $messages);
  
        // if ($validator->fails()) {
        //     return redirect()
        //                 ->back()
        //                 ->withErrors($validator)
        //                 ->withInput();
        // }
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'country' => 'required',
        ]);

        $welcome_bonus = WelcomeBonus::latest()->first();

        $last_ac = User::select('id')->latest()->first();
        if (isset($last_ac)) {
            $code = sprintf('%04d', $last_ac->id + 1000001);
        } else {
            $code = sprintf('%04d', 1000001);
        }

        $user = new User();
        $user->role_id = 3;
        $user->code = $code;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->country = $request->country;
        $user->password = Hash::make($request->password);
        $user->save();

        $check_user = User::where('code', $code)->where('email', $request->email)->first();
        if($check_user){
            $s_user = User::find($check_user->id);
            $s_user->earning_balance = $s_user->earning_balance + $welcome_bonus->amount;
            $s_user->save();
        }

        return redirect()->route('login');
    }

    public function refer_user_register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'country' => 'required',
        ]);

        $welcome_bonus = WelcomeBonus::latest()->first();

        $last_ac = User::select('id')->latest()->first();
        if (isset($last_ac)) {
            $code = sprintf('%04d', $last_ac->id + 1000001);
        } else {
            $code = sprintf('%04d', 1000001);
        }

        $ck_refered_user = User::where('code', $request->user_code)->first();

        $user = new User();
        $user->role_id = 3;
        $user->code = $code;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->country = $request->country;
        $user->password = Hash::make($request->password);
        if($ck_refered_user){
            $user->rfered_by = $ck_refered_user->id;
        }
        // return $user;
        $user->save();

        $refered_user = User::find($ck_refered_user->id);
        $refered_user->total_refer = $refered_user->total_refer + 1;
        $refered_user->save();

        $check_user = User::where('code', $code)->where('email', $request->email)->first();
        if($check_user){
            $s_user = User::find($check_user->id);
            $s_user->earning_balance = $s_user->earning_balance + $welcome_bonus->amount;
            $s_user->save();
        }

        return redirect()->route('login');
    }

    public function foreget_password()
    {
        $website = Website::latest()->first();
        $msg = '';
        return view('auth.forget-password', compact('website','msg'));
    }

    public function recover_password(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);
        $ck_user = User::where('email', $request->email)->first();
        if($ck_user){
            
            $new_password = rand(100000,999999);
            
            $up_user = User::find($ck_user->id);
            $up_user->password = Hash::make($new_password);
            $up_user->save();
            
            $data = array(
                'name' => $ck_user->name,
                'email' => $ck_user->email,
                'phone' => $ck_user->phone,
                'subject' => 'Password Recover',
                'new_password' => $new_password
            );

            Mail::send('email', $data, function ($mail) use ($data) {
                $mail->from('sakibhasantuha12@gmail.com', website_info()->title)
                    ->to($data['email'], website_info()->title)
                    ->subject($data['subject']);
            });
            
            $request->session()->put('mail_sent', 'Verification mail sent. Check your mail.');
            
            return redirect()->route('login');
        }else{
            $request->session()->put('mail_sent', '');
            $website = Website::latest()->first();
            $msg = 'Email not valid! Please enter valid email';
            return view('auth.forget-password', compact('website','msg'));
        }
    }

    public function admin_login()
    {
        $website = Website::latest()->first();
        return view('auth.admin-login', compact('website'));
    }

    public function user_logout()
    {
        $user = Auth::user();
        $user->activity = 0;
        $user->save();
        Auth::logout();

        return redirect()->route('home');
    }

    public function reload_captcha()
    {
        $original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
        $original_string = implode("", $original_string);
        $data = substr(str_shuffle($original_string), 0, 6);
        return response()->json(['captcha'=>$data]);
    }
}
