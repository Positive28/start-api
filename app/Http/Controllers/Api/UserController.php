<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $lists = $this->service->get($params);
        if($lists)
            return response()->successJson($lists);
        else
            return response()->errorJson('Object not found', 404);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $params = $request->validated();
        $model = $this->service->create($params);
        return response()->successJson($model);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->service->show((int) $id);
        if($user)
            return response()->successJson($user);
        else
            return response()->errorJson('Object not found', 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
