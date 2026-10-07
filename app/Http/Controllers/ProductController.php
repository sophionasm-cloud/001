<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
               if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->min_price) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('selling_price', '<=', $request->max_price);
        }
        if ($request->sort) {
            switch ($request->sort) {
                case 'price_asc': $query->orderBy('selling_price', 'asc'); break;
                case 'price_desc': $query->orderBy('selling_price', 'desc'); break;
                default: $query->orderBy('created_at', 'desc');
            }
        }
        $products = $query->paginate(12);
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) return abort(404);
        $reviews = $product->reviews()->with('user')->latest()->get();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)->take(4)->get();
        return view('products.show', compact('product', 'reviews', 'relatedProducts'));
    }
}
