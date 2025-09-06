<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

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
        })->get();


        // マイリストはログイン状態の場合のみ表示
        if ($activeTab === 'mylist' && auth()->check()) {
            $myListItems = auth()->user()->favoriteItems()
                ->when($keyword, fn($query, $keyword) =>
                $query->where('item_name', 'LIKE', "%{$keyword}%"))->get();
        } else {
            $myListItems = collect();
        }
        return view('index', [
            'activeTab' => $activeTab,
            'items'     => $items,
            'myListItems'    => $myListItems,
        ]);
    }

    //商品詳細表示
    public function showItemDetail(Item $item)
    {
        $currentUser = Auth::user();
        $item->load(['comments.user.profile', 'categories']);

        $comments = $item->comments()->oldest()->get();

        $favoritesCount = $item->favoritedByUsers()->count();
        $isFavorited = auth()->check()
            ? $item->favoritedByUsers()->where('user_id', auth()->id())->exists()
            : false;

        return view('detail', compact('item', 'favoritesCount', 'isFavorited', 'comments', 'currentUser'));
    }
}
