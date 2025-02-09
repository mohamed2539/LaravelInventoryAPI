<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد
     */


        public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // ✅ إضافة البريد الإلكتروني
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,user'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $user = User::create([
            'username' => $request->username,
            'email' => $request->email, // ✅ إضافة البريد الإلكتروني
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user
        ], 201);
    }

        /**
         * تسجيل الدخول
         */
    public function login(Request $request)
    {
        // 1️⃣ التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2️⃣ تجهيز البيانات
        $credentials = $request->only('username', 'password');

        // 3️⃣ محاولة تسجيل الدخول
        if (!$token = JWTAuth::attempt($credentials)) { // ✨ استخدام JWTAuth
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 4️⃣ إرسال التوكن مع المستخدم
        return response()->json([
            'message' => 'Login successful!',
            'user' => auth()->user(),
            'token' => $token // ✨ التوكن الصحيح
        ]);
    }

        /**
         * استرجاع بيانات المستخدم المسجل
         */
        public function profile()
    {
        return response()->json(Auth::user());
    }


    public function me()
    {
        return response()->json(auth()->user());
    }

        /**
         * تسجيل الخروج
         */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken()); // ✨ إلغاء التوكن

        return response()->json(['message' => 'Successfully logged out']);
    }
}
