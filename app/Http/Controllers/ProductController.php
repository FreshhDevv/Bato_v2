<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $response = [
            'products' => Product::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'product_id')->get();
                })->get(),
        ];
        return response($response, 200);
    }

    // get single post
    public function show($id)
    {
        $response = [
            'product' => Product::where('id', $id)->withCount('comments', 'likes')->get(),
        ];
        return response($response, 200);
    }

    // create a product
    public function store(Request $request)
    {
        //validate fields
        $fields = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'products');

        $product = Product::create([
            'body' => $fields['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

        // for now skip for post image

        $response = [
            'message' => 'Product created.',
            'product' => $product,
        ];
        return response($response, 201);
    }

    // update a product
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
        $fields = $request->validate([
            'body' => 'required|string'
        ]);

        $product->update([
            'body' =>  $fields['body']
        ]);

        // for now skip for product image

        $response = [
            'message' => 'Product updated.',
            'product' => $product
        ];
        return response($response, 200);
    }

    //delete product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            $response = [
                'message' => 'Product not found.',
            ];
            return response($response, 403);
        }

        if ($product->user_id != auth()->user()->id) {
            $response = [
                'message' => 'Permission denied.'
            ];
            return response($response, 403);
        }

        $product->comments()->delete();
        $product->likes()->delete();
        $product->delete();

        $response = [
            'message' => 'Product deleted.'
        ];
        return response($response, 200);
    }
}
