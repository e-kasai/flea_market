<?php

namespace App\Http\Controllers;

use App\Models\Item;


class FavoriteController extends Controller
{
    public function setItemFavorite(Item $item)
    {
        $item->favoritedByUsers()->syncWithoutDetaching([auth()->id()]);
        return back();
    }

    public function setItemUnfavorite(Item $item)
    {
        $item->favoritedByUsers()->detach(auth()->id());
        return back();
    }
}
