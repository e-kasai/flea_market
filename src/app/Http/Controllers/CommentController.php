<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;

class CommentController extends Controller
{

    public function storeComment(CommentRequest $request, Item $item)
    {
        $item->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->validated()['body'],
        ]);

        return redirect()->route('details.show', $item)
            ->with('message', 'コメントを投稿しました。');
    }
}
