<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Category;
use App\Models\Admin\JobFee;
use App\Models\Admin\LocationZone;
use App\Models\Admin\LocationZoneCountry;
use App\Models\Admin\SubCategory;
use App\Models\Admin\Website;
use App\Models\Admin\UserMessage;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = Job::where('status', 1)->whereColumn('worker_need', '>', 'worker_confirmed')->latest()->get();
        $website = Website::latest()->first();
        $title = 'Approval Job List';

        return view('backend.pages.job-manage.index', compact('title', 'website', 'datas'));
    }

    public function pending_job()
    {
        $datas = Job::where('status', 0)->latest()->get();
        $website = Website::latest()->first();
        $title = 'Pending Job List';

        return view('backend.pages.job-manage.index', compact('title', 'website', 'datas'));
    }

    public function rejected_job()
    {
        $datas = Job::where('status', 2)->latest()->get();
        $website = Website::latest()->first();
        $title = 'Rejected Job List';

        return view('backend.pages.job-manage.index', compact('title', 'website', 'datas'));
    }

    public function complete_job()
    {
        $datas = Job::where('status', 1)->whereColumn('worker_need', '<=', 'worker_confirmed')->latest()->get();
        $website = Website::latest()->first();
        $title = 'Complete Job List';

        return view('backend.pages.job-manage.index', compact('title', 'website', 'datas'));
    }

    public function job_approve($id)
    {
        $job = Job::find($id);
        $job->status = 1;
        
        $data = new UserMessage();
        $data->user_id = $job->user_id;
        $data->message_title = 'Job Approval';
        $data->message = 'Your job, id: '.$job->code.' approved.';
        $data->save();
        
        $job->save();

        return redirect()->back()->with('message','Successfully approved this job!');
    }

    public function reject_job(Request $request, $id)
    {
        $job = Job::find($id);
        $job->status = 2;
        $job->reason = $request->reason;

        $user = User::find($job->user_id);
        $user->deposit_balance = $user->deposit_balance + $job->budget;
        $user->save();
        
        $data = new UserMessage();
        $data->user_id = $job->user_id;
        $data->message_title = 'Job Reject';
        $data->message = 'Your job, id: '.$job->code.' reject for '.$request->reason;
        $data->save();

        $job->save();

        return redirect()->back()->with('message','Successfully reject this job!');
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
        $location_zone = LocationZone::latest()->get();
        $categorys = Category::latest()->get();
        $job = Job::find($id);
        $job_countrys = LocationZoneCountry::latest()->get();
        $job_sub_cats = SubCategory::where('category_id', $job->category_id)->get();
        $job_fee = JobFee::latest()->first();
        $website = Website::latest()->first();
        $title = 'Update job';

        return view('backend.pages.job-manage.job-edit', compact('website', 'location_zone', 'job_countrys', 'job', 'job_fee', 'categorys', 'job_sub_cats', 'title'));
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
            'title' => 'required',
            'location_zone_country' => 'required',
            'category_id' => 'required',
            'sub_category' => 'required',
            'specific_task' => 'required',
            'required_proof' => 'required',
            'worker_need' => 'required',
            'each_worker_earn' => 'required',
            'required_screenshots' => 'required',
            'estimited_day' => 'required',
            'budget' => 'required',
        ]);

        $job = Job::find($id);
        $job->title = $request->title;
        $job->location_zone_country = $request->location_zone_country;
        $job->category_id = $request->category_id;
        $job->sub_category = $request->sub_category;
        $job->specific_task = trim(implode("|",$request->specific_task),"|");
        $job->required_proof = $request->required_proof;

        $image = $request->file('thumbnail');
        if ($image) {
            if(file_exists($job->thumbnail_image)){
                unlink($job->thumbnail_image);
            }
            $image_name = Str::random(20);
            $ext = strtolower($image->getClientOriginalExtension());
            $image_full_name = $image_name.'.'.$ext;
            $upload_path = 'backend/img/job/';
            $image_url = $upload_path.$image_full_name;
            $success = $image->move($upload_path, $image_full_name);
            $job->thumbnail_image = $image_url;
        }

        $job->worker_need = $request->worker_need;
        $job->each_worker_earn = $request->each_worker_earn;
        $job->required_screenshots = $request->required_screenshots;
        $job->estimited_day = $request->estimited_day;
        $job->budget = $request->budget;
        $job->save();

        return redirect()->back()->with('message','Job updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = Job::find($id);
        $job->delete();

        return redirect()->back()->with('message','Successfully deleted this job!');
    }
}
