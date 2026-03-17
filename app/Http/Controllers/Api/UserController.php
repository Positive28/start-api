<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Foydalanuvchilar CRUD (JWT kerak)"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Foydalanuvchilar ro'yxati (paginate)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="sort_key", in="query", required=false, @OA\Schema(type="string", example="id")),
     *     @OA\Parameter(name="sort_type", in="query", required=false, @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(name="fname", in="query", required=false, description="Ism bo'yicha qidirish", @OA\Schema(type="string")),
     *     @OA\Parameter(name="lname", in="query", required=false, description="Familiya bo'yicha qidirish", @OA\Schema(type="string")),
     *     @OA\Parameter(name="phone", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="role", in="query", required=false, @OA\Schema(type="string", enum={"user","admin"})),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Paginated list"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'sort_key'  => 'nullable|string',
            'sort_type' => 'required_with:sort_key|in:asc,desc',
            'fname'     => 'nullable|string',
            'lname'     => 'nullable|string',
            'phone'     => 'nullable|string',
            'email'     => 'nullable|string',
            'role'      => 'nullable|string|in:user,admin',
            'page'      => 'nullable|integer|min:1',
            'per_page'  => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $validated['per_page'] ?? 20;
        $query   = User::with(['region', 'city']);

        if (!empty($validated['fname'])) {
            $query->where('fname', 'like', '%' . $validated['fname'] . '%');
        }
        if (!empty($validated['lname'])) {
            $query->where('lname', 'like', '%' . $validated['lname'] . '%');
        }
        if (!empty($validated['phone'])) {
            $query->where('phone', 'like', '%' . $validated['phone'] . '%');
        }
        if (!empty($validated['email'])) {
            $query->where('email', 'like', '%' . $validated['email'] . '%');
        }
        if (!empty($validated['role'])) {
            $query->where('role', $validated['role']);
        }

        $sortKey   = $validated['sort_key'] ?? 'id';
        $sortType  = $validated['sort_type'] ?? 'desc';
        $query->orderBy($sortKey, $sortType);

        $lists = $query->paginate($perPage);

        if ($lists->isEmpty()) {
            return response()->errorJson('Object not found', 404);
        }

        return response()->successJson($lists);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Yangi foydalanuvchi yaratish",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone","password","fname","lname"},
     *             @OA\Property(property="fname", type="string", example="Ism"),
     *             @OA\Property(property="lname", type="string", example="Familiya"),
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="password", type="string", example="parol123"),
     *             @OA\Property(property="email", type="string", nullable=true),
     *             @OA\Property(property="telegram", type="string", nullable=true),
     *             @OA\Property(property="region_id", type="integer", nullable=true),
     *             @OA\Property(property="city_id", type="integer", nullable=true),
     *             @OA\Property(property="role", type="string", enum={"user","admin"}, nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone'     => 'required|string|max:20|unique:users,phone',
            'password'  => 'required|string|min:6',
            'fname'     => 'required|string|max:255',
            'lname'     => 'required|string|max:255',
            'email'     => 'nullable|email|unique:users,email',
            'telegram'  => 'nullable|string|max:80',
            'region_id' => 'nullable|integer|exists:regions,id',
            'city_id'   => 'nullable|integer|exists:cities,id',
            'role'      => 'nullable|in:user,admin',
        ]);

        $validated['role'] = $validated['role'] ?? User::ROLE_USER;
        $user = User::create($validated);

        return response()->successJson($user);
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Bitta foydalanuvchi ma'lumotlari",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User object"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(string $id)
    {
        $user = User::with(['region', 'city'])->find((int) $id);

        if (!$user) {
            return response()->errorJson('Object not found', 404);
        }

        return response()->successJson($user);
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Foydalanuvchini yangilash",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"fname","lname","phone","region_id","city_id","role"},
     *             @OA\Property(property="fname", type="string"),
     *             @OA\Property(property="lname", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="email", type="string", nullable=true),
     *             @OA\Property(property="password", type="string", nullable=true),
     *             @OA\Property(property="telegram", type="string", nullable=true),
     *             @OA\Property(property="region_id", type="integer"),
     *             @OA\Property(property="city_id", type="integer"),
     *             @OA\Property(property="role", type="string", enum={"user","admin"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, string $id)
    {
        $userId = (int) $id;
        $user   = User::find($userId);

        if (!$user) {
            return response()->errorJson('Object not found', 404);
        }

        $validated = $request->validate([
            'fname'     => 'required|string|max:255',
            'lname'     => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:users,phone,' . $userId,
            'email'     => 'nullable|email|unique:users,email,' . $userId,
            'password'  => 'nullable|string|min:6',
            'telegram'  => 'nullable|string|max:80',
            'region_id' => 'required|integer|exists:regions,id',
            'city_id'   => 'required|integer|exists:cities,id',
            'role'      => 'required|in:user,admin',
            'avatar'    => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        unset($validated['avatar']);

        $user->update($validated);

        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($request->file('avatar'))->toMediaCollection('avatar');
        }

        return response()->successJson($user->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Foydalanuvchini o'chirish",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User deleted"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(string $id)
    {
        $user = User::find((int) $id);

        if (!$user) {
            return response()->errorJson('Object not found', 404);
        }

        $user->delete();

        return response()->successJson(['message' => 'User deleted']);
    }
}
