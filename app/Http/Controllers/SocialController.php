<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
class SocialController extends Controller
{
 // توجيه المستخدم إلى مزود OAuth
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    
    public function callback($provider)
{
    try {
        $socialUser = Socialite::driver($provider)->user();
        
        $user = User::where([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),

        ])->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'token' => Crypt::encryptString($socialUser->token),
                'password' => Hash::make(Str::random(16)),
            ]);

        }
                Auth::login($user);

        return "hh";

    } catch (\Exception $e) {
        return redirect()->route('login')->withErrors([
            'social' => 'فشل تسجيل الدخول عبر ' . $provider
        ]);
    }
}
}
