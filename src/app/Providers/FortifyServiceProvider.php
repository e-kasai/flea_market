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
        //同じIPや同じメールからの連続ログイン施行を制限
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        // Fortify が使うLoginRequestを自作のFR(LoginUserRequest)に差し替える
        $this->app->bind(LoginRequest::class, LoginUserRequest::class);

        Fortify::loginView(function () {
            return view('auth.login');
        });
    }
}
