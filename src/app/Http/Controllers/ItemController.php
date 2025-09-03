<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//モデルクラスのインポート
use App\Models\Item;


class ItemController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        //tab指定がない場合のデフォルトはおすすめタブ
        $activeTab = $request->get('tab', 'recommend');

        // おすすめ（検索条件があれば絞り込み）
        $items = Item::when($keyword, function ($query, $keyword) {
            return $query->where('item_name', 'LIKE', "%{$keyword}%");
        })->paginate(12)->withQueryString();


        // マイリストはログイン状態の場合のみ表示
        if ($activeTab === 'mylist' && auth()->check()) {
            $myListItems = auth()->user()->favoriteItems()
                ->when($keyword, fn($query, $keyword) =>
                $query->where('item_name', 'LIKE', "%{$keyword}%"))->paginate(12)->withQueryString();
        } else {
            $myListItems = collect();
        }

        return view('index', [
            'activeTab' => $activeTab,
            'items'     => $items,
            'myListItems'    => $myListItems,
        ]);
    }
}
