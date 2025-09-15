<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;


class FavoriteController extends Controller
{
    public function setItemFavorite(Request $request, Item $item)
    {
        if (auth()->check()) {
            $item->favoritedByUsers()->syncWithoutDetaching([auth()->id()]);
        } else {
            $key = 'guest_favorites';
            $ids = collect($request->session()->get($key, []))
                ->push($item->id)
                ->unique()
                ->values()
                ->all();
            $request->session()->put($key, $ids);
        }
        return back();
    }

    public function setItemUnfavorite(Request $request, Item $item)
    {
        if (auth()->check()) {
            $item->favoritedByUsers()->detach(auth()->id());
        } else {
            $key = 'guest_favorites';
            $ids = collect($request->session()->get($key, []))
                ->reject(fn($id) => (int)$id === (int)$item->id)
                ->values()
                ->all();
            $request->session()->put($key, $ids);
        }
        return back();
    }
}
