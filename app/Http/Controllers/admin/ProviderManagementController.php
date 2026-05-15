<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\admin\ProviderManagementService;
use App\Traits\ApiResponse;
use App\Http\Resources\ProviderResource;
use App\Models\Provider;

class ProviderManagementController extends Controller
{

    public function __construct(private readonly ProviderManagementService $pms)
    {
    }

    public function createProvider(\App\Http\Requests\Admin\CreateProviderRequest $request)
    {
        $dto = \App\DTOs\CreateProviderDTO::fromRequest($request);
        $result = $this->pms->createProvider($dto);
        return ApiResponse::success(message: 'Provider created successfully', data: ProviderResource::make($result));
    }

    public function deleteProvider(int $id)
    {
        $this->pms->deleteProvider($id);
        return ApiResponse::success(message: 'Provider deleted successfully');
    }

    public function getAllProviders(Request $request)
    {
        $result = $this->pms->getAllProviders();
        return ApiResponse::success(message: 'Providers retrieved successfully', data: ProviderResource::collection($result));
    }

    public function getProviderById(int $id)
    {
        $result = $this->pms->getProviderById($id);
        return ApiResponse::success(message: 'Provider retrieved successfully', data: ProviderResource::make($result));
    }

    public function activateProvider(int $id)
    {
        $this->pms->activateProvider($id);
        return ApiResponse::success(message: 'Provider activated successfully');
    }

    public function deactivateProvider(int $id)
    {
        $this->pms->deactivateProvider($id);
        return ApiResponse::success(message: 'Provider deactivated successfully');
    }

    public function searchProviders(Request $request)
    {
        $query = $request->input('query', '');
        $result = $this->pms->searchProviders($query);
        return ApiResponse::success(message: 'Search completed successfully', data: ProviderResource::collection($result));
    }



}
