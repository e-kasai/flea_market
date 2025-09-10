<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// フォームリクエスト
use App\Http\Requests\RegisterUserRequest;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    // validationは自作FRを使い、登録ロジックは Fortify の CreateNewUserを再利用
    public function store(RegisterUserRequest $request, CreateNewUser $creator): RedirectResponse
    {
        $input = $request->validated();
        $user = $creator->create($input);

        auth()->login($user);
        $request->session()->regenerate();
        // return redirect()->route('profile.show');
        return redirect()->route('verification.notice');
    }
}
