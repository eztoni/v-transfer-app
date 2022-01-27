<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EditUserController extends Controller
{
    public function showUser(User $user){

        return view('edit-user',['user' => $user]);
    }
}
