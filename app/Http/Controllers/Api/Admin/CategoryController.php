<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private function rules(?string $exceptId = null): array
    {
        $slugRule = 'required|string|max:80|unique:categories,slug';
        if ($exceptId !== null) {
            $slugRule .= ',' . $exceptId;
        }
        return [
            'name'       => 'required|string|max:80',
            'slug'       => $slugRule,
            'sort_order' => 'nullable|integer',
            'is_active'  => 'required|boolean',
        ];
    }

    private function findOrFail(string $id): ?Category
    {
        return Category::find((int) $id);
    }

    public function index(Request $request)
    {
        $v = $request->validate([
            'sort_key'  => 'nullable|string',
            'sort_type' => 'required_with:sort_key|in:asc,desc',
            'name'      => 'nullable|string',
            'slug'      => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'page'      => 'nullable|integer|min:1',
            'per_page'  => 'nullable|integer|min:1|max:100',
        ]);

        $query = Category::query()
            ->when(isset($v['name']) && $v['name'] !== '', fn ($q) => $q->where('name', 'like', '%' . $v['name'] . '%'))
            ->when(isset($v['slug']) && $v['slug'] !== '', fn ($q) => $q->where('slug', 'like', '%' . $v['slug'] . '%'))
            ->when(isset($v['is_active']), fn ($q) => $q->where('is_active', $v['is_active']))
            ->orderBy($v['sort_key'] ?? 'sort_order', $v['sort_type'] ?? 'asc');

        return response()->successJson($query->paginate($v['per_page'] ?? 20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        return response()->successJson(Category::create($validated));
    }

    public function show(string $id)
    {
        $model = $this->findOrFail($id);
        if (!$model) {
            return response()->errorJson('Object not found', 404);
        }
        return response()->successJson($model);
    }

    public function update(Request $request, string $id)
    {
        $model = $this->findOrFail($id);
        if (!$model) {
            return response()->errorJson('Object not found', 404);
        }
        $model->update($request->validate($this->rules($id)));
        return response()->successJson($model->fresh());
    }

    public function destroy(string $id)
    {
        $model = $this->findOrFail($id);
        if (!$model) {
            return response()->errorJson('Object not found', 404);
        }
        $model->delete();
        return response()->successJson(['message' => 'Category deleted']);
    }
}
