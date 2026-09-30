<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featured_products = Product::where('is_active', true)->take(6)->get();
        $categories = Category::take(8)->get();
        return view('home', compact('featured_products', 'categories'));
    }
}