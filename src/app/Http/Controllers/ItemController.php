<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $keyword = $request->get('keyword');
        //tab指定がない場合のデフォルトはおすすめタブ
        $activeTab = $request->get('tab', 'recommend');

        // おすすめタブ
        //検索結果は自分が出品した商品は除外して表示
        $items = Item::query()
            ->when(
                auth()->check(),
                fn($query) =>
                $query->where('seller_id', '<>', $userId)
            )
            ->when(
                $keyword,
                fn($query, $keyword) =>
                $query->where('item_name', 'LIKE', "%{$keyword}%")
            )
            ->latest()
            ->get();

        //ログインユーザーのみマイリスト表示
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

        //ログインユーザーのいいね数のみDBに保存
        $favoritesCount = $item->favoritedByUsers()->count();
        $guestFavorited = in_array($item->id, session('guest_favorites', []));

        $isFavorited = auth()->check()
            ? $item->favoritedByUsers()->where('user_id', auth()->id())->exists()
            : $guestFavorited;

        // 表示用のいいね数
        //ログイン中: DB に保存された正しい数だけ表示
        //ゲスト中: DBカウント + あればセッション分(+1)。
        $displayFavoritesCount = $favoritesCount + (!auth()->check() && $guestFavorited ? 1 : 0);

        return view('detail', compact('item', 'favoritesCount', 'isFavorited', 'comments', 'currentUser', 'displayFavoritesCount'));
    }
}
