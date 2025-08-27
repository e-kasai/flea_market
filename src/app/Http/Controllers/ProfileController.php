<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //プロフィール画面の表示
    public function showProfilePage()
    {
        return view('profile');
    }
}
