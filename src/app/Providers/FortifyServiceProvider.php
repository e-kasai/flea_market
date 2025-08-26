<?php

namespace App\Providers;

use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Http\Requests\LoginRequest;
use App\Http\Requests\LoginUserRequest;


class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        $this->app->bind(LoginRequest::class, LoginUserRequest::class);

        // login を開いたとき、auth/login.blade.php を表示する(fortifyがrouteを担当)
        Fortify::loginView(function () {
            return view('auth.login');
        });
    }
}
