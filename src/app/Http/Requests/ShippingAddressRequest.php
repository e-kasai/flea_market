<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingAddressRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    //全角を半角に変換し空白を除去
    protected function prepareForValidation(): void
    {
        $postal = preg_replace('/\s+/u', '', (string) $this->postal_code);
        $postal = mb_convert_kana($postal, 'n');
        $postal = str_replace('ー', '-', $postal);
        $this->merge(['postal_code' => $postal]);
    }

    public function messages(): array
    {
        return [
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
