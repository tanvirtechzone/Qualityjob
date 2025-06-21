<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\MainWallet;
use App\Models\Admin\Website;
use App\Models\BoostCategory;
use App\Models\BoostPackageHeadline;
use App\Models\User;
use App\Models\UserBoostPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoostPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = UserBoostPackage::where('user_id', Auth::user()->id)->latest()->paginate(25);
        $headlines = BoostPackageHeadline::all();
        $title = 'Order History';

        return view('user.pages.boost-package.index', compact('title', 'datas', 'headlines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorys = BoostCategory::orderBy('id', 'ASC')->get();
        $headlines = BoostPackageHeadline::all();
        $title = 'Add new Boost';

        return view('user.pages.boost-package.create', compact('categorys', 'title', 'headlines'));
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
            'category_id' => 'required',
            'sub_category' => 'required',
            'work_need' => 'required',
            'cost' => 'required',
        ]);

        $website = Website::latest()->first();

        $cost = $request->cost;
        if(Auth::user()->deposit_balance < $cost){
            return redirect()->back()->with('error','You have no sufficient balance for job.');
        }else{
            $user_balance = User::find(Auth::user()->id);
            $user_balance->deposit_balance = $user_balance->deposit_balance - $cost;
            $user_balance->save();

            $check_main_wallet = MainWallet::latest()->first();
            $main_wallet = MainWallet::find($check_main_wallet->id);
            $main_wallet->amount = $main_wallet->amount + $cost;
            $main_wallet->save();
        }

        $last_ac = UserBoostPackage::select('id')->latest()->first();
        if (isset($last_ac)) {
            $code = sprintf('%04d', $last_ac->id + 1000001);
        } else {
            $code = sprintf('%04d', 1000001);
        }

        $boost_package = new UserBoostPackage();
        $boost_package->code = $code;
        $boost_package->description = $request->description;
        $boost_package->link = $request->link;
        $boost_package->category_id = $request->category_id;
        $boost_package->sub_category = $request->sub_category;
        $boost_package->base_cost = $request->base_cost;
        $boost_package->unit_cost = $request->unit_cost;
        $boost_package->work_need = $request->work_need;
        $boost_package->cost = $request->cost;
        $boost_package->user_id = Auth::user()->id;
        $boost_package->save();

        return redirect()->back()->with('message','Boost Package added successfully');
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
        $boost_package = UserBoostPackage::find($id);
        $headlines = BoostPackageHeadline::all();
        $title = 'Update Boost Package';

        return view('user.pages.boost-package.edit', compact('boost_package', 'title', 'headlines'));
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
            'description' => 'required',
        ]);

        $boost_package = UserBoostPackage::find($id);
        $boost_package->description = $request->description;
        $boost_package->save();

        return redirect()->back()->with('message','Boost Package updated successfully');
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
