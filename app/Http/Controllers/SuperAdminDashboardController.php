<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminDashboardController extends Controller
{
    public function show(Request  $request){

        $users = User::all()->except(Auth::id());

        return view('super-admin-dashboard',['users' => $users]);
    }
}
