<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            // validationは画像パスではなく実際の画像ファイルが対象
            'image_path' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
            //複数のカテゴリを扱いやすくするためarrayで形式を固定
            'category_ids'   => ['required', 'array', 'min:1'],
            //各要素に対してのルール（整数、重複禁止、存在カテゴリ）
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
            'condition' => ['required', 'integer'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            // item_name
            'item_name.required' => '商品名は必須です。',
            'item_name.string'   => '商品名は文字列で入力してください。',
            'item_name.max'      => '商品名は255文字以下で入力してください。',

            // brand_name
            'brand_name.string'  => 'ブランド名は文字列で入力してください。',
            'brand_name.max'     => 'ブランド名は255文字以下で入力してください。',

            // description
            'description.required' => '商品説明は必須です。',
            'description.string'   => '商品説明は文字列で入力してください。',
            'description.max'      => '商品説明は255文字以下で入力してください。',

            // image_path（※ max:2048 は約2MB）
            'image_path.required' => '商品画像を選択してください。',
            'image_path.image'    => '画像ファイルを選択してください。',
            'image_path.max'      => '画像サイズは2MB以下にしてください。',
            'image_path.mimes'    => '画像の拡張子は jpg / jpeg / png のみ許可されています。',

            // category_ids
            'category_ids.required'   => 'カテゴリを1つ以上選択してください。',
            'category_ids.array'        => 'カテゴリを1つ以上選択してください。',
            'category_ids.min'        => 'カテゴリを1つ以上選択してください。',
            'category_ids.*.integer'  => 'カテゴリを選択肢から選んでください。',
            'category_ids.*.distinct' => '同じカテゴリは重複選択できません。',
            'category_ids.*.exists'   => '表示されているカテゴリから選んでください。',

            // condition
            'condition.required' => '商品の状態を選択してください。',
            'condition.integer' => '商品の状態を選択してください。',

            // price
            'price.required' => '価格を入力してください。',
            'price.integer'  => '価格は整数で入力してください。',
            'price.min'      => '価格は0円以上で入力してください。',
        ];
    }
}
