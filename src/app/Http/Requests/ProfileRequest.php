<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:20'],
            'avatar_path' => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    //全角を半角に変換し空白を除去
    protected function prepareForValidation(): void
    {
        $postal = preg_replace('/\s+/u', '', (string) $this->postal_code); // 空白除去
        $postal = mb_convert_kana($postal, 'n'); // 全角数字を半角
        $postal = str_replace('ー', '-', $postal); // 全角ハイフンを半角に
        $this->merge(['postal_code' => $postal]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'お名前を入力してください',
            'name.string' => 'お名前を文字列で入力してください',
            'name.max' => 'お名前は20文字以下で入力してください',
            'avatar_path.image' => '画像形式でアップロードしてください',
            'avatar_path.max' => '画像サイズは5MB以下のみ対応です',
            'avatar_path.mimes' => 'プロフィール画像はjpg,jpeg,pngのみ対応です',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号は数字3桁-数字4桁で入力してください',
            'address.required' => '住所を入力してください',
            'address.string' => '住所を文字列で入力してください',
            'address.max' => '住所は255文字以下で入力してください',
            'building.string' => '建物名は文字列入力してください',
            'building.max' => '建物名は255文字以下で入力してください'
        ];
    }
}
