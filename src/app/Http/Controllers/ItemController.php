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

        // 未ログイン状態の場合マイリストは何も表示しない
        if ($activeTab === 'mylist' && !auth()->check()) {
            $myListItems = collect();
        }

        // おすすめ（検索条件があれば絞り込み）
        $items = Item::when($keyword, function ($query, $keyword) {
            return $query->where('item_name', 'LIKE', "%{$keyword}%");
        })->paginate(12)->withQueryString();

        // マイリスト（検索条件があれば絞り込み）
        // まだいいね機能実装前なのでお気に入り商品は空配列にしておく
        $myListItems = collect();
        if ($activeTab === 'mylist') {
            $myListItems = auth()->user()->favoriteItems()
                ->when($keyword, fn($query, $keyword) =>
                $query->where('item_name', 'LIKE', "%{$keyword}%"))->paginate(12)->withQueryString();
        }
        return view('index', [
            'activeTab' => $activeTab,
            'items'     => $items,
            'myListItems'    => $myListItems,
        ]);
    }
}
