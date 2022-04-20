<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Post;
use App\Http\Controllers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
class authController extends Controller
{
    public function githubredirect(Request $request)
    {
        return Socialite::driver('github')->redirect();
    }
    public function githubcallback(Request $request)
    {
        $userdata = Socialite::driver('github')->user();
        
        $uuid = Str::uuid()->toString();

        $user = new User();
        $user->name = $userdata->name;
        $user->email=$userdata->email;
        $user->password=Hash::make($uuid.now());
        $user->auth_type = 'github';
        $user->save();
        Auth::login($user);
        return redirect('/');

    }}
