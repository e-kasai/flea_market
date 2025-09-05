<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;



class FavoriteController extends Controller
{
    public function setItemFavorite($item_id)
    {
        $item = Item::findOrFail($item_id);
        $item->favoritedByUsers()->syncWithoutDetaching([auth()->id()]);
        return back();
    }

    public function setItemUnfavorite($item_id)
    {
        $item = Item::findOrFail($item_id);
        $item->favoritedByUsers()->detach(auth()->id());
        return back();
    }
}
