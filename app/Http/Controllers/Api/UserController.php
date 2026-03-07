<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Barcha user CRUD logikasi (filter, sort, validatsiya) shu controllerda.
 * Eski yondashuv saqlanib qolgan (ishlatilmaydi, faqat arxiv):
 *   - App\Services\UserService, App\Services\BaseService
 *   - App\Repositories\UserRepository, App\Repositories\BaseRepository
 *   - App\Http\Requests\User\IndexRequest, StoreRequest, UpdateRequest
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'sort_key'  => 'nullable|string',
            'sort_type' => 'required_with:sort_key|in:asc,desc',
            'name'      => 'nullable|string',
            'email'     => 'nullable|string',
            'role'      => 'nullable|string|in:user,admin',
            'page'      => 'nullable|integer|min:1',
            'per_page'  => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $validated['per_page'] ?? 20;
        $query   = User::query();

        if (!empty($validated['name'])) {
            $query->where('name', 'like', '%' . $validated['name'] . '%');
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:255',
            'role'     => 'required|in:user,admin',
        ]);

        $user = User::create($validated);

        return response()->successJson($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find((int) $id);

        if (!$user) {
            return response()->errorJson('Object not found', 404);
        }

        return response()->successJson($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $userId = (int) $id;
        $user   = User::find($userId);

        if (!$user) {
            return response()->errorJson('Object not found', 404);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6',
            'phone'    => 'nullable|string|max:255',
            'role'     => 'required|in:user,admin',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->successJson($user->fresh());
    }

    /**
     * Remove the specified resource from storage.
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
