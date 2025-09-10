<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'コメントを入力してください',
            'body.string' => 'コメントは文字列で入力してください',
            'body.max' => 'コメントは255文字以下で入力してください',
        ];
    }
}
