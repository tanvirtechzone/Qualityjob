<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Client;
use App\Models\Admin\Country;
use App\Models\Admin\HeavyEquipment;
use App\Models\Admin\LocationZoneCountry;
use App\Models\Admin\Project;
use App\Models\Admin\Service;
use App\Models\Admin\SubCategory;
use Illuminate\Http\Request;
use App\Models\Admin\Website;
use App\Models\BoostSubCategory;
use App\Models\Job;
use App\Models\JobWork;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class DashboardController extends Controller
{
    public function index()
    {
        // Approve work more than 36 hours
        // $works = JobWork::where('created_at', '<',Carbon::parse('-36 hours'))->where('status', 0)->get();
        // if($works->count() > 0){
        //     foreach($works as $work){
        //         $job_work = JobWork::find($work->id);

        //         $job = Job::find($job_work->job_id);
        //         if($job){
        //             $user = User::find($job_work->user_id);
        //             $user->earning_balance = $user->earning_balance + $job->each_worker_earn;
                    
        //             $website = Website::latest()->first();
        //             if($website->referral_earning_commission > 0){
        //                 $earning_commission = ($website->referral_earning_commission * $job->each_worker_earn) / 100;

        //                 $refered_by = User::find($user->rfered_by);
        //                 if($refered_by){
        //                     $refered_by->earning_balance = $refered_by->earning_balance + $earning_commission;
        //                     $refered_by->save();
        //                 }

        //                 $user->earning_commision_from_refer = $user->earning_commision_from_refer + $earning_commission;
        //             }

        //             $user->save();

        //             $job->worker_confirmed = $job->worker_confirmed + 1;

        //             $job->save();
        //         }


        //         $job_work->status = 1;
        //         $job_work->save();

        //     }
        // }
        
        // delete data more than 30 days
        $l_date = Carbon::now()->subDays(7);
        $date = Carbon::parse($l_date)->format('Y-m-d 23:59:59');
        $complete_works = JobWork::where('created_at', '<=', $date)->get();
        if($complete_works->count() > 0){
            foreach($complete_works as $work){
                $job_work = JobWork::find($work->id);
                $job_work->delete();
            }
        }

        $website = Website::latest()->first();
        $servise = Service::all();
        $client = Client::all();
        return view('backend.pages.dashboard.index', compact('website','servise','client'));
    }

    public function get_country(Request $request)
    {
        $location = $request->location_zone;

        $countrys = LocationZoneCountry::where('zone_id', $location)->get();
        $html = '';
        $html .= '<option value="">Select One</option>';
        if($countrys){
            foreach($countrys as $country){
                $ck_country = Country::find($country->country_id);
                if($ck_country){
                    $html .= '<option value="'.$ck_country->id.'">'.$ck_country->name.'</option>';
                }
            }
        }

        return $html;
    }

    public function get_sub_category(Request $request)
    {
        $category_id = $request->category_id;

        $sub_cats = SubCategory::where('category_id', $category_id)->get();
        $html = '';
        $html .= '<option value="">Select One</option>';
        if($sub_cats){
            foreach($sub_cats as $category){
                $html .= '<option value="'.$category->id.'">'.$category->name.'</option>';
            }
        }

        return $html;
    }

    public function get_sub_category_price(Request $request)
    {
        $id = $request->sub_category;

        $sub_cats = SubCategory::find($id);
        return $sub_cats->minimum_cost;
    }

    public function get_new_task_complete_area(Request $request)
    {
        $need_to_completed_step = $request->need_to_completed_step;
        $html = '
            <div class="form-group" id="another_area_'.$need_to_completed_step.'">
                <label for="link" class="form-label">Step '.$need_to_completed_step.' [ Max 100 character ] <button type="button" class="btn btn-sm btn-danger" onclick="deleteCompleteArea('.$need_to_completed_step.')">X</button></label>
                <textarea class="form-control" name="specific_task[]" id="" cols="30" rows="1"></textarea>
            </div>
        ';

        return $html;
    }
}
