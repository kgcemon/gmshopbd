<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewControllers extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user','product']);

        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%');
            })->orWhereHas('product', function ($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%');
            });
        }

        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'review' => 'nullable|string',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $review = Review::findOrFail($id);
        $review->update($request->only('review','rating'));

        return redirect()->back()->with('success','Review updated successfully!');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success','Review deleted successfully!');
    }
}
