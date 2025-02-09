<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductLocation;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductLocationController extends Controller
{


    public function index()
    {
        $locations = ProductLocation::all();
        return response()->json($locations);
    }


    /**
     * إضافة موقع جديد لمنتج معين
     */


    public function store(Request $request)
    {
        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // إضافة الموقع الجديد
        $productLocation = ProductLocation::create([
            'product_id' => $request->product_id,
            'location' => $request->location,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'تمت إضافة موقع المنتج بنجاح!',
            'product_location' => $productLocation
        ], 201);
    }


    /**
     * تحديث موقع منتج معين
     */
    public function update(Request $request, $id)
    {
        // البحث عن موقع المنتج
        $productLocation = ProductLocation::find($id);

        if (!$productLocation) {
            return response()->json(['error' => 'موقع المنتج غير موجود!'], 404);
        }

        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'location' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // تحديث البيانات المطلوبة فقط
        $productLocation->update($request->only(['location', 'price']));

        return response()->json([
            'message' => 'تم تحديث موقع المنتج بنجاح!',
            'product_location' => $productLocation
        ]);
    }



    /**
     * حذف موقع منتج معين
     */
    public function destroy($id)
    {
        // البحث عن موقع المنتج
        $productLocation = ProductLocation::find($id);

        if (!$productLocation) {
            return response()->json(['error' => 'موقع المنتج غير موجود!'], 404);
        }

        // حذف الموقع
        $productLocation->delete();

        return response()->json(['message' => 'تم حذف موقع المنتج بنجاح!']);
    }


    public function findProductInLocation($product_id, $location)
    {
        $productLocation = ProductLocation::where('product_id', $product_id)
            ->where('location', $location)
            ->first();

        if (!$productLocation) {
            return response()->json(['message' => 'المنتج غير متوفر في هذا الموقع'], 404);
        }

        return response()->json($productLocation);
    }






}
