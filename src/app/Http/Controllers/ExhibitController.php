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
        DB::transaction(function () use ($request) {

            $item = new Item();
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
                $item->image_path = $request->file('image_path')->store('items', 'public');
            }
            $item->save();

            $categoryIds = $request->input('category_ids', []);
            $item->categories()->sync($categoryIds);
        });

        return redirect()->route('items.index')->with('message', '出品しました。');
    }
}
