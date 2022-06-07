<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($id)
    {
        $product = Product::find($id);

        if(!$product)
        {
            $response = [
                'message' => 'Product not found.'
            ];
            return response($response, 403);
        }

        $response = [
            'comments' => $product->comments()->with('user:id,name,image')->get(),
        ];
        return response($response, 200);
    }

    // create a comment
    public function store(Request $request, $id)
    {
        $product = Product::find($id);

        if(!$product)
        {
            $response = [
                'message' => 'Product not found.'
            ];
            return response($response, 403);
        }

        //validate fields
        $fields = $request->validate([
            'comment' => 'required|string'
        ]);

        Comment::create([
            'comment' => $fields['comment'],
            'product_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        $response = [
            'message' => 'Comment created.'
        ];
        return response($response, 201);
    }

    // update a comment
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            $response = [
                'message' => 'Comment not found.',
            ];
            return response($response, 403);
        }

        if($comment->user_id != auth()->user()->id)
        {
            $response = [
                'message' => 'Permission denied.',
            ];
            return response($response, 403);
        }

        //validate fields
        $fields = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $fields['comment']
        ]);

        $response = [
            'message' => 'Comment updated.'
        ];
        return response($response, 200);
    }

    // delete a comment
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            $response = [
                'message' => 'Comment not found.'
            ];
            return response($response, 403);
        }

        if($comment->user_id != auth()->user()->id)
        {
            $response = [
                'message' => 'Permission denied.',
            ];
            return response($response, 403);
        }

        $comment->delete();

        $response = [
            'message' => 'Comment deleted.'
        ];
        return response($response, 200);
    }
}
