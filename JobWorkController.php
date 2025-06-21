<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Website;
use App\Models\Job;
use App\Models\JobWork;
use App\Models\User;
use Illuminate\Http\Request;

class JobWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = JobWork::latest()->get();
        $website = Website::latest()->first();
        $title = 'Worked Job List';

        return view('backend.pages.job-manage.job-work', compact('title', 'website', 'datas'));
    }

    public function job_work_approve($id)
    {
        $job_work = JobWork::find($id);

        $job = Job::find($job_work->job_id);

        $user = User::find($job_work->user_id);
        $user->earning_balance = $user->earning_balance + $job->each_worker_earn;

        $website = Website::latest()->first();
        if($website->referral_earning_commission > 0){
            $earning_commission = ($website->referral_earning_commission * $job->each_worker_earn) / 100;

          
        }

        $user->save();

        $job->worker_confirmed = $job->worker_confirmed + 1;

        $job_work->status = 1;
        $job_work->save();

        $job->save();

        return redirect()->back()->with('message','Successfully approved this job!');
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
