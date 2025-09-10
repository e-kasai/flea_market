<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user && ! $user->hasVerifiedEmail()) {
            // 未認証なら認証ページへ
            return redirect()->route('verification.notice');
        }

        // 認証済みは intended（もともと行きたかった場所）か商品一覧へ
        return redirect()->intended(route('items.index'));
    }
}
