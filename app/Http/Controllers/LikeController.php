<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function likeOrUnlike($id)
    {
        $product = Product::find($id);

        if(!$product)
        {
            $response = [
                'message' => 'Post not found.'
            ];
            return response($response, 403);
        }

        $like = $product->likes()->where('user_id', auth()->user()->id)->first();

        // if not liked then like
        if(!$like)
        {
            Like::create([
                'product_id' => $id,
                'user_id' => auth()->user()->id
            ]);

            $response = [
                'message' => 'Liked'
            ];
            return response($response, 200);
        }
        // else dislike it
        $like->delete();

        $response = [
            'message' => 'Disliked'
        ];
        return response($response, 200);
    }
}
