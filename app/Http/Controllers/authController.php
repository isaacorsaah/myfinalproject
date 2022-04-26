<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
class authController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();
        $authUser = $this->findOrCreateUser($user, $provider);
        Auth::login($authUser, true);
        return redirect('/');
    }
    public function findOrCreateUser($user, $provider)
    {
        $authUser = User::where('provider_id', $user->id)->first();
        if ($authUser) {
            return $authUser;
        }
        else{
            $data = User::create([
                'name'     => $user->name,
                'email'    => !empty($user->email)? $user->email : '' ,
                'provider' => $provider,
                'provider_id' => $user->id
            ]);
            return $data;
        }
    }
    public function googleredirect(Request $request)
    {
        return Socialite::driver('google')->redirect();
    }
    public function googlecallback(Request $request)
    {
        $userdata =Socialite::driver('google')->stateless()->user();
        
        $user= User::where('email',$userdata->email)->where('auth_type','google')->first();
        if($user){
            Auth::login($user);
            return redirect('/home');
        }else{
            $uuid = Str::uuid()->toString();

            $user = new User();
            $user->name = $userdata->name;
            $user->email=$userdata->email;
            $user->password=Hash::make($uuid.now());
            $user->auth_type = 'google';
            $user->save();
            
            Auth::login($user);
            return redirect('/home');

        }
        
    }
}
