<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response([
            'products' => Product::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')->get();
                })
                ->get()
        ], 200);
    }

    // get single post
    public function show($id)
    {
        return response([
            'product' => Product::where('id', $id)->withCount('comments', 'likes')->get()
        ], 200);
    }

    // create a post
    public function store(Request $request)
    {
        //validate fields
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'products');

        $product = Product::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

        // for now skip for post image

        return response([
            'message' => 'Product created.',
            'post' => $product,
        ], 200);
    }

    // update a post
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response([
                'message' => 'Product not found.'
            ], 403);
        }

        if ($product->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }

        //validate fields
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $product->update([
            'body' =>  $attrs['body']
        ]);

        // for now skip for post image

        return response([
            'message' => 'Product updated.',
            'product' => $product
        ], 200);
    }

    //delete post
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response([
                'message' => 'Product not found.'
            ], 403);
        }

        if ($product->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }

        $product->comments()->delete();
        $product->likes()->delete();
        $product->delete();

        return response([
            'message' => 'Product deleted.'
        ], 200);
    }
}
