<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use \App\Models\Category;
use Illuminate\Support\Facades\DB;

class ExhibitController extends Controller
{
    public function showExhibitForm()
    {
        $item = new Item();
        $categories = Category::all();
        return view('exhibit', compact('categories', 'item'));
    }

    //出品処理
    public function storeExhibitItem(ExhibitionRequest $request)
    {
        //transactionで出品データが１部だけ変更されてしまうのを防ぐ(All or nothing)
        DB::transaction(function () use ($request) {

            $item = new Item();
            // fillableにseller_idをいれてないのでリレーションで取得
            $item->seller_id = auth()->id();

            $validated = $request->validated();

            $item->fill([
                'item_name'     => $validated['item_name'],
                'brand_name'    => $validated['brand_name'],
                'description'   => $validated['description'],
                'price'         => $validated['price'],
                'condition'     => $validated['condition'],
            ]);


            //商品画像アップロード
            if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
                // 画像を保存
                $item->image_path = $request->file('image_path')->store('items', 'public');
            }
            $item->save();

            // 複数カテゴリのIDを配列で受け取る 例: [1, 3, 5]
            $categoryIds = $request->input('category_ids', []);
            // 中間テーブルへ保存（古いのを削除 → 新しいのをまとめて追加）
            $item->categories()->sync($categoryIds);
        });

        // return redirect()->back()->with('message', '出品しました。');
        return redirect()->route('items.index')->with('message', '出品しました。');
    }
}
