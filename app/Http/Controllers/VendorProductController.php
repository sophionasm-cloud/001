<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class VendorProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Vendor');
    }

    public function index()
    {
        $vendor = auth()->user()->vendor;
        $products = $vendor->products()->paginate(10);
        return view('vendor.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('vendor.products.create', compact('categories'));
    }

    // ✅ UPDATE THIS METHOD
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = auth()->user()->vendor->products()->create($validated);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->update(['image' => $imagePath]);
        }

        return redirect('vendor/products/' . $product->id)->with('success', 'Product created');
    }

    public function show(Product $product)
    {
        if ($product->vendor_id !== auth()->user()->vendor->id) {
            return abort(403);
        }
        return view('vendor.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if ($product->vendor_id !== auth()->user()->vendor->id) {
            return abort(403);
        }
        $categories = Category::all();
        return view('vendor.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->vendor_id !== auth()->user()->vendor->id) {
            return abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product->update($validated);

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(storage_path('app/public/' . $product->image))) {
                unlink(storage_path('app/public/' . $product->image));
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->update(['image' => $imagePath]);
        }

        return redirect('vendor/products/' . $product->id)->with('success', 'Product updated');
    }

    public function destroy(Product $product)
    {
        if ($product->vendor_id !== auth()->user()->vendor->id) {
            return abort(403);
        }
        $product->delete();
        return redirect('vendor/products')->with('success', 'Product deleted');
    }
}