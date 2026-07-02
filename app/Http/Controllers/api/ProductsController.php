<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
 public function index()
{
    $products = Categorie::with('products')
        ->where('name', '!=', 'default')
        ->select('name', 'id')
        ->orderBy('sort')
        ->paginate(50);

    return response()->json([
        'status' => true,
        'data' => $products->items(),
        'total' => $products->total(),
        'current_page' => $products->currentPage(),
        'last_page' => $products->lastPage(),
    ]);
}

    public function show($slug)
    {
        try {

            $product = Product::select([
                'id',
                'name',
                'slug',
                'image',
                'support_country',
                'delivery_system',
                'input_name',
                'cover_image',
                'is_auto',
                'sort',
                'short_description',
                'stock',
                'status',
                'created_at'
            ])
                ->with(['items'])
                ->where('slug', $slug)
                ->first();

            if ($product) {
                return response()->json([
                    'status' => true,
                    'data' => $product,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
