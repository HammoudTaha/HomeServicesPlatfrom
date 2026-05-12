<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryManagementController extends Controller
{
    public function __construct(private readonly \App\Services\admin\CategoryManagementService $cms)
    {
    }

    public function createCategory(\App\Http\Requests\Admin\CreateCategoryRequest $request)
    {
        $dto = \App\DTOs\CreateCategoryDTO::fromRequest($request);
        $this->cms->createCategory($dto);
        return \App\Traits\APIResponse::success(message: 'Category created successfully', );
    }

    public function getAllCategories()
    {
        $result = $this->cms->getAllCategories();
        return \App\Traits\APIResponse::success(message: 'Categories retrieved successfully', data: $result);
    }
    public function getCategoryById(int $id)
    {
        $result = $this->cms->getCategoryById($id);
        return \App\Traits\APIResponse::success(message: 'Category retrieved successfully', data: $result);
    }
    public function updateCategory(\App\Http\Requests\Admin\UpdateCategoryRequest $request)
    {
        $dto = \App\DTOs\UpdateCategoryDTO::fromRequest($request);
        $this->cms->updateCategory($dto);
        return \App\Traits\APIResponse::success(message: 'Category updated successfully', );
    }
    public function deleteCategory(int $id)
    {
        $this->cms->deleteCategory($id);
        return \App\Traits\APIResponse::success(message: 'Category deleted successfully');
    }
}
