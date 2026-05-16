<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\ProviderResource;
use App\Http\Resources\User\ProviderDetailsResource;
class ProvidersController extends Controller
{
    public function __construct(private \App\Services\Auth\ProviderAuthService $pas)
    {
    }

    public function getCategoryProviders(int $id)
    {
        $category = $this->pas->getProvidersByCategory($id);
        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => ProviderResource::collection($category)
        ], 200);
    }
    public function getProviderDetails(int $id)
    {
        $provider = $this->pas->getProviderDetails($id);
        return response()->json([
            'message' => 'Provider details retrieved successfully',
            'data' => new ProviderDetailsResource($provider)
        ], 200);
    }
}
