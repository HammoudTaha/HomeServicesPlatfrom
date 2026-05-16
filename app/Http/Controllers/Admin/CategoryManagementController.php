<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
class CategoryManagementController extends Controller
{
    public function __construct(private readonly \App\Services\admin\CategoryService $cms)
    {
    }

    public function createCategory(\App\Http\Requests\Admin\CreateCategoryRequest $request)
    {
        $dto = \App\DTOs\CreateCategoryDTO::fromRequest($request);
        $result = $this->cms->createCategory($dto);
        return ApiResponse::success(message: 'Category created successfully', data: CategoryResource::make($result));
    }

    public function getAllCategories()
    {
        $result = $this->cms->getAllCategories();
        return ApiResponse::success(message: 'Categories retrieved successfully', data: CategoryResource::collection($result));
    }
    public function getCategoryById(int $id)
    {
        $result = $this->cms->getCategoryById($id);
        return ApiResponse::success(message: 'Category retrieved successfully', data: CategoryResource::make($result));
    }
    public function updateCategory(\App\Http\Requests\Admin\UpdateCategoryRequest $request)
    {
        $dto = \App\DTOs\UpdateCategoryDTO::fromRequest($request);
        $result = $this->cms->updateCategory($dto);
        return ApiResponse::success(message: 'Category updated successfully', data: CategoryResource::make($result));
    }
    public function deleteCategory(int $id)
    {
        $this->cms->deleteCategory($id);
        return ApiResponse::success(message: 'Category deleted successfully');
    }

    public function searchCategories(Request $request)
    {
        $query = $request->query('query', '');
        $result = $this->cms->searchCategories($query);
        return ApiResponse::success(message: 'Categories retrieved successfully', data: CategoryResource::collection($result));
    }

    public function activateCategory(int $id)
    {
        $this->cms->activateCategory($id);
        return ApiResponse::success(message: 'Category activated successfully');
    }
    public function deactivateCategory(int $id)
    {
        $this->cms->deactivateCategory($id);
        return ApiResponse::success(message: 'Category deactivated successfully');
    }
}
