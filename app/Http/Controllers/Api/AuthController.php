<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Start API",
 *     version="1.0.0",
 *     description="AgroBozor uchun backend API"
 * )
 *
 * @OA\Server(
 *     url="http://daladan-api.loc/api/v1",
 *     description="Local dev server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Auth"},
     *     summary="Yangi foydalanuvchini ro'yxatdan o'tkazish",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone","password","name"},
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="password", type="string", example="parol123"),
     *             @OA\Property(property="name", type="string", example="Ism Familiya"),
     *             @OA\Property(property="email", type="string", nullable=true, example="user@example.com"),
     *             @OA\Property(property="telegram", type="string", nullable=true, example="@username")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Muvaffaqiyatli ro'yxatdan o'tdi, token qaytdi"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validatsiya xatosi"
     *     )
     * )
     */
    
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone'    => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:users,email',
            'telegram' => 'nullable|string|max:80',
        ]);

        $validated['role'] = User::ROLE_USER;
        User::create($validated);
        $token = auth('api')->attempt([
            'phone'    => $request->phone,
            'password' => $request->password,
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => auth('api')->user(),
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Telefon raqam va parol bilan login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone","password"},
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="password", type="string", example="parol123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Muvaffaqiyatli login, JWT token qaytadi"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Telefon raqam yoki parol noto'g'ri"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validatsiya xatosi"
     *     )
     * )
     */
    
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->errorJson('Telefon raqam yoki parol noto\'g\'ri.', 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }

     /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Auth"},
     *     summary="Tizimdan chiqish",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Muvaffaqiyatli logout"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *     path="/auth/get-info",
     *     tags={"Auth"},
     *     summary="Hozirgi foydalanuvchi ma'lumotlari",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Foydalanuvchi ma'lumotlari qaytadi"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized (token yo'q yoki noto'g'ri)"
     *     )
     * )
     */

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * @OA\Post(
     *     path="/refresh",
     *     tags={"Auth"},
     *     summary="Tokenni yangilash",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Yangi token qaytadi"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }
}
