<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\admin\ProviderManagementService;
use App\Traits\APIResponse;
class ProviderManagementController extends Controller
{
    public function __construct(private readonly ProviderManagementService $pms)
    {
    }

    public function createProvider(\App\Http\Requests\Admin\CreateProviderRequest $request)
    {
        $dto = \App\DTOs\CreateProviderDTO::fromRequest($request);
        $result = $this->pms->createProvider($dto);
        return APIResponse::success(message: 'Provider created successfully', data: $result);
    }

    public function updateProvider(\App\Http\Requests\Admin\UpdateProviderRequest $request)
    {
        $dto = \App\DTOs\UpdateProviderDTO::fromRequest($request);
        $this->pms->updateProvider($dto);
        return APIResponse::success(message: 'Provider updated successfully');
    }

    public function deleteProvider(int $id)
    {
        $this->pms->deleteProvider($id);
        return APIResponse::success(message: 'Provider deleted successfully');
    }

    public function getAllProviders(Request $request)
    {
        $result = $this->pms->getAllProviders();
        return APIResponse::success(message: 'Providers retrieved successfully', data: $result);
    }

    public function getProviderById(int $id)
    {
        $result = $this->pms->getProviderById($id);
        return APIResponse::success(message: 'Provider retrieved successfully', data: $result);
    }

    public function activateProvider(int $id)
    {
        $this->pms->activateProvider($id);
        return APIResponse::success(message: 'Provider activated successfully');
    }

    public function deactivateProvider(int $id)
    {
        $this->pms->deactivateProvider($id);
        return APIResponse::success(message: 'Provider deactivated successfully');
    }

    public function searchProviders(Request $request)
    {
        $query = $request->input('query');
        $result = $this->pms->searchProviders($query);
        return APIResponse::success(message: 'Search completed successfully', data: $result);
    }



}
