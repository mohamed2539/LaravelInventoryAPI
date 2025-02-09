<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * إنشاء طلب جديد
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // التأكد من توفر المنتج في المخزون
        $product = Product::find($request->product_id);
        if ($product->stock < $request->quantity) {
            return response()->json(['error' => 'الكمية غير متوفرة في المخزون'], 400);
        }

        // إنشاء الطلب
        $order = Order::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
        ]);

        // تقليل المخزون
        $product->decrement('stock', $request->quantity);

        return response()->json([
            'message' => 'تم إنشاء الطلب بنجاح!',
            'order' => $order
        ], 201);
    }



    public function index(Request $request)
    {
        // لو المستخدم Admin يشوف كل الطلبات
        if ($request->user()->role === 'admin') {
            $orders = Order::with('product')->get();
        } else {
            // لو مستخدم عادي يشوف طلباته بس
            $orders = Order::where('user_id', $request->user()->id)->with('product')->get();
        }

        return response()->json($orders);
    }


    public function show($id, Request $request)
    {
        $order = Order::with('product')->find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        // تحقق إن المستخدم يملك الطلب أو Admin
        if ($request->user()->role !== 'admin' && $order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مسموح لك بمشاهدة هذا الطلب'], 403);
        }

        return response()->json($order);
    }


    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        // تحقق إن المستخدم هو صاحب الطلب أو Admin
        if ($request->user()->role !== 'admin' && $order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مسموح لك بتعديل هذا الطلب'], 403);
        }

        // تحقق إن الطلب لم يتم تأكيده بعد
        if ($order->status !== 'Pending') {
            return response()->json(['message' => 'لا يمكنك تعديل هذا الطلب بعد الموافقة عليه'], 400);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $order->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'تم تعديل الطلب بنجاح', 'order' => $order]);
    }



    public function destroy($id, Request $request)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        // تحقق إن المستخدم هو صاحب الطلب أو Admin
        if ($request->user()->role !== 'admin' && $order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مسموح لك بحذف هذا الطلب'], 403);
        }

        // تحقق إن الطلب لم يتم تأكيده بعد
        if ($order->status !== 'Pending') {
            return response()->json(['message' => 'لا يمكنك حذف هذا الطلب بعد الموافقة عليه'], 400);
        }

        $order->delete();

        return response()->json(['message' => 'تم حذف الطلب بنجاح']);
    }


    public function approve($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        $order->update(['status' => 'approved']);

        return response()->json(['message' => 'تمت الموافقة على الطلب', 'order' => $order]);
    }


    public function reject($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        $order->update(['status' => 'Rejected']);

        return response()->json(['message' => 'تم رفض الطلب', 'order' => $order]);
    }


}
