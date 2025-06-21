<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AcceptTaskHeadline;
use App\Models\Admin\Website;
use App\Models\CompleteTaskHeadline;
use App\Models\Job;
use App\Models\JobWork;
use App\Models\JobHide;
use App\Models\User;
use App\Models\Admin\UserMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserJobWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = JobWork::where('user_id', Auth::user()->id)->latest()->paginate(25);
        $headlines = CompleteTaskHeadline::all();
        $title = 'Complete Worked Job List';

        $l_date = Carbon::now()->subDays(7);
        $date = Carbon::parse($l_date)->format('Y-m-d 23:59:59');
        $complete_works = JobWork::where('created_at', '<=', $date)->get();
        if($complete_works->count() > 0){
            foreach($complete_works as $work){
                $job_work = JobWork::find($work->id);
                $job_work->delete();
            }
        }

        return view('user.pages.worked-job-list', compact('title', 'datas', 'headlines'));
    }

    public function complete()
    {
        $datas = JobWork::where('job_woner', Auth::user()->id)->latest()->paginate(25);
        $headlines = AcceptTaskHeadline::all();
        $title = 'Worked Job List';

        return view('user.pages.accept-worked-job-list', compact('title', 'datas', 'headlines'));
    }

    public function all_satisfied()
    {
        $works = JobWork::where('status', 0)->where('job_woner', Auth::user()->id)->latest()->get();
        foreach($works as $work){
            $job_work = JobWork::find($work->id);
            if($job_work){
                $job = Job::find($job_work->job_id);
                if($job){
                    $user = User::find($job_work->user_id);
                    $user->earning_balance = $user->earning_balance + $job->each_worker_earn;
            
                    $website = Website::latest()->first();
                    if($website->referral_earning_commission > 0){
                        $earning_commission = ($website->referral_earning_commission * $job->each_worker_earn) / 100;
            
                        $refered_by = User::find($user->rfered_by);
                        if($refered_by){
                            $refered_by->earning_balance = $refered_by->earning_balance + $earning_commission;
                            $refered_by->save();
                
                            $user->earning_commision_from_refer = $user->earning_commision_from_refer + $earning_commission;
                        }
                    }
            
                    $user->save();
            
                    $job->worker_confirmed = $job->worker_confirmed + 1;
            
                    $job_work->status = 1;
                    $job_work->save();
            
                    $job->save();
                }
            }else{
                $job_work->delete();
            }
        }
        
        $datas = JobWork::where('user_id', Auth::user()->id)->latest()->paginate(25);
        $headlines = AcceptTaskHeadline::all();
        $title = 'Satisfied Worked List';

        return view('user.pages.accept-worked-job-list', compact('title', 'datas', 'headlines'));
    }

    public function job_work_approve($id)
    {
        $job_work = JobWork::find($id);
        
        $msg_user_id = $job_work->user_id;

        $job = Job::find($job_work->job_id);
        $msg_title = $job->title;
        
        $job_code = $job->code;

        $user = User::find($job_work->user_id);
        $user->earning_balance = $user->earning_balance + $job->each_worker_earn;

        $website = Website::latest()->first();
        if($website->referral_earning_commission > 0){
            $earning_commission = ($website->referral_earning_commission * $job->each_worker_earn) / 100;

            $refered_by = User::find($user->rfered_by);
            if($refered_by){
                $refered_by->earning_balance = $refered_by->earning_balance + $earning_commission;
                $refered_by->save();
    
                $user->earning_commision_from_refer = $user->earning_commision_from_refer + $earning_commission;
            }
        }

        $user->save();

        $job->worker_confirmed = $job->worker_confirmed + 1;

        $job_work->status = 1;
        $job_work->save();

        $job->save();
        
        // $data = new UserMessage();
        // $data->user_id = $msg_user_id;
        // $data->message_title = 'Work Approval';
        // $data->message = 'Work approved for the job of '.$msg_title;
        // $data->save();

        return redirect()->back()->with('message','Successfully approved this job!');
    }

    public function job_work_rate(Request $request, $id){
        $job_work = JobWork::find($id);
        
        $msg_user_id = $job_work->user_id;
        $job = Job::find($job_work->job_id);
        $msg_title = $job->title;
        
        $job_work->is_rated = 1;
        $job_work->rating = $request->rate;
        $job_work->save();
        
        $data = new UserMessage();
        $data->user_id = $msg_user_id;
        $data->message_title = 'Work Rating';
        $data->message = 'Work rated for the job of '.$msg_title;
        $data->save();
        
        return redirect()->back()->with('message','Successfully rated this job!');
    }
    
    public function job_work_reject(Request $request, $id)
    {
        $job_work = JobWork::find($id);
        $msg_user_id = $job_work->user_id;
        
        $job_work->status = 2;
        $job_work->reason = $request->reason;

        $job = Job::find($job_work->job_id);
        $msg_title = $job->title;
        
        $reject_done = $job->reject_done + 1;
        $job->reject_done = $reject_done;
        $job->save();

        $job_work->save();
        
        $data = new UserMessage();
        $data->user_id = $msg_user_id;
        $data->message_title = 'Work Reject';
        $data->message = 'Work rejected for the job of '.$msg_title.'. Reason: '.$request->report_reason;
        $data->save();

        return redirect()->back()->with('message','Successfully reject this job!');
    }

    public function job_work_report(Request $request, $id)
    {
        $job_work = JobWork::find($id);
        $msg_user_id = $job_work->user_id;

        $job = Job::find($job_work->job_id);
        $msg_title = $job->title;
        
        $job_work->status = 3;
        $job_work->report_reason = $request->reason;
        $job_work->save();
        
        $data = new UserMessage();
        $data->user_id = $msg_user_id;
        $data->message_title = 'Work Report';
        $data->message = 'Work reported for the job of '.$msg_title.'. Reason: '.$request->report_reason;
        $data->save();

        return redirect()->back()->with('message','Successfully report this job!');
    }

    public function job_work_report_to_job_woner(Request $request, $id)
    {
        $job_work = JobWork::find($id);
        $msg_user_id = $job_work->job_woner;

        $job = Job::find($job_work->job_id);
        $msg_title = $job->title;
        
        $job_work->job_woner_report = 1;
        $job_work->job_woner_report_reason = $request->reason;
        $job_work->save();
        
        $data = new UserMessage();
        $data->user_id = $msg_user_id;
        $data->message_title = 'Work Report';
        $data->message = 'Reported for the job of '.$msg_title.'. Reason: '.$request->report_reason;
        $data->save();

        return redirect()->back()->with('message','Successfully report this job!');
    }

    public function job_work_resume(Request $request, $id)
    {
        $job_work = JobWork::find($id);
        $msg_user_id = $job_work->user_id;

        $job = Job::find($job_work->job_id);
        $msg_title = $job->title;
        
        $job_work->status = 4;
        $job_work->reason = $request->reason;
        $job_work->save();
        
        $data = new UserMessage();
        $data->user_id = $msg_user_id;
        $data->message_title = 'Work Resume';
        $data->message = 'Work resumed for the job of '.$msg_title.'. Reason: '.$request->reason;
        $data->save();

        return redirect()->back()->with('message','Successfully resumed this job!');
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
    
    public function job_hide($job_id)
    {
        $job_hide = new JobHide();
        $job_hide->job_id = $job_id;
        $job_hide->user_id = Auth::user()->id;
        $job_hide->save();
        
        return redirect()->route('user.find-job')->with('message','Successfully this job hide!');
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
            'job_id' => 'required',
            'work_proof' => 'required',
        ]);

        $job = Job::find($request->job_id);
        $msg_title = $job->title;
        
        $job_woner = $job->user_id;
        if($job->user_id == Auth::user()->id){
            return redirect()->back()->with('error','You can not work this job. This job posted by you!');
        }

        $work = new JobWork();
        $work->job_id = $request->job_id;
        $work->work_proof = $request->work_proof;

        $images = $request->file('screenshot_proof');
        if ($images) {
            foreach($images as $image){
                $image_name = Str::random(20);
                $ext = strtolower($image->getClientOriginalExtension());
                $image_full_name = $image_name.'.'.$ext;
                $upload_path = 'backend/img/work-proof/';
                $image_url = $upload_path.$image_full_name;
                $success = $image->move($upload_path, $image_full_name);
                $uls[] = $image_url;
            }
            $screenshot = trim(implode("|",$uls),"|");

            $work->screenshot_proof = $screenshot;
        }

        $work->job_woner = $job_woner;
        $work->user_id = Auth::user()->id;
        $work->save();
        
        
        // $data = new UserMessage();
        // $data->user_id = $job_woner;
        // $data->message = 'Work done for '.$msg_title;
        // $data->save();

        return redirect()->route('user.job-work-confirm');
    }
    
    public function resubmit_job_work(Request $request)
    {
        $request->validate([
            'job_id' => 'required',
            'work_proof' => 'required',
        ]);

        $job = Job::find($request->job_id);
        $msg_title = $job->title;
        $earning = $job->each_worker_earn;
        
        $job_woner = $job->user_id;
        if($job->user_id == Auth::user()->id){
            return redirect()->back()->with('error','You can not work this job. This job posted by you!');
        }

        $work = JobWork::find($request->work_id);
        $work->job_title = $msg_title;
        $work->earning = $earning;
        $work->job_id = $request->job_id;
        $work->work_proof = $request->work_proof;

        $images = $request->file('screenshot_proof');
        if ($images) {
            $s_shots = explode("|",$work->screenshot_proof);
            if ($s_shots){
                foreach ($s_shots as $key=>$s_shot){
                    if(file_exists($s_shot)){
                        unlink($s_shot);
                    }
                }
            }
            
            foreach($images as $image){
                $image_name = Str::random(20);
                $ext = strtolower($image->getClientOriginalExtension());
                $image_full_name = $image_name.'.'.$ext;
                $upload_path = 'backend/img/work-proof/';
                $image_url = $upload_path.$image_full_name;
                $success = $image->move($upload_path, $image_full_name);
                $uls[] = $image_url;
            }
            $screenshot = trim(implode("|",$uls),"|");

            $work->screenshot_proof = $screenshot;
        }

        $work->job_woner = $job_woner;
        $work->status = 0;
        $work->user_id = Auth::user()->id;
        $work->save();

        return redirect()->back()->with('message','Successfully resubmit your work!');
    }

    public function job_work_confirm()
    {
        return view('user.pages.work-done');
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
        $job = JobWork::find($id);
        $job->delete();

        return redirect()->back()->with('message','Successfully deleted this job!');
    }
}
