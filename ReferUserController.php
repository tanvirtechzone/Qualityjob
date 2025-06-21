<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferUserController extends Controller
{
    public function refer_user($code)
    {
        return view('auth.refer-register', compact('code'));
    }
}
