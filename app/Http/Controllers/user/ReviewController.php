<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function reviewByProduct($slug)
    {
        $product = Product::where('slug', $slug)->first();
        $reviews = Review::where('product_id', $product->id)->orderBy('created_at', 'desc')->paginate(20);
        return view('user.review', compact('reviews'));
    }

    public function show($slug){
        $product = Product::where('slug', $slug)->first();
        return view('user.add-review', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'review'     => 'required|string|min:3',
            'product_id' => 'required|string|exists:products,slug',
            'rating'     => 'required|integer|min:1|max:5',
        ]);

        try {
            $user = auth()->user();

            $product = Product::where('slug', $validated['product_id'])->firstOrFail();

            $review = new Review();
            $review->review     = $validated['review'];
            $review->rating     = $validated['rating'];
            $review->product_id = $product->id;
            $review->user_id    = $user->id;
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your review has been submitted successfully.',
            ], 201);

        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $exception->getMessage(),
            ], 500);
        }
    }


}
