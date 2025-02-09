<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // عرض جميع المنتجات
/*    public function index()
    {
        return Product::all();
    }*/
    public function index()
    {
        return response()->json(Product::all());
    }


    // إضافة منتج جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        return Product::create($request->all());
    }

    // عرض منتج واحد
    public function show(Product $product)
    {
        return $product;
    }

    // تحديث منتج
    public function update(Request $request, Product $product)
    {
        $product->update($request->all());
        return $product;
    }

    // حذف منتج
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }


    public function productsByLocation()
    {
        $products = Product::with('locations')->get();

        return response()->json($products);
    }
}


