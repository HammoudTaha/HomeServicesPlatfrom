<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\User\CategoryResource;
use App\Http\Resources\User\ProviderResource;
class CategoriesController extends Controller
{
    public function __construct(private \App\Services\admin\CategoryService $categoryService)
    {
    }

    public function getAllCategories()
    {
        $categories = $this->categoryService->getActiveCategories();
        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => CategoryResource::collection($categories)
        ], 200);
    }


}
