<?php

namespace App\Services\admin;

use Illuminate\Support\Str;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
class CategoryManagementService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private \App\Services\CloudImageService $cis)
    {
    }

    public function createCategory(\App\DTOs\CreateCategoryDTO $dto)
    {
        DB::transaction(function () use ($dto) {
            $imageData = $this->cis->upload($dto->image);
            $category = ServiceCategory::create([
                'name' => $dto->name,
                'slug' => Str::slug($dto->name),
                'commission' => $dto->commission,
            ]);
            $category->image()->create([
                'image_url' => $imageData['url'],
                'public_id' => $imageData['public_id']
            ]);
        });
    }

    public function getAllCategories()
    {
        return ServiceCategory::with('image')->get();
    }
    public function getCategoryById(int $id)
    {
        return ServiceCategory::with('image')->findOrFail($id);
    }

    public function updateCategory(\App\DTOs\UpdateCategoryDTO $dto)
    {
        DB::transaction(function () use ($dto) {
            $category = ServiceCategory::findOrFail($dto->categoryId);
            $category->update([
                'name' => $dto->name ?? $category->name,
                'slug' => Str::slug($dto->name ?? $category->name),
                'commission' => $dto->commission ?? $category->commission,
            ]);
            if ($dto->image) {
                $oldImage = $category->image()->first();
                if ($oldImage) {
                    $imageData = $this->cis->update($dto->image, $oldImage->public_id);
                    $category->image()->create([
                        'image_url' => $imageData['url'],
                        'public_id' => $imageData['public_id']
                    ]);
                }
            }
        });
    }
    public function deleteCategory(int $id)
    {
        DB::transaction(function () use ($id) {
            $category = ServiceCategory::findOrFail($id);
            $image = $category->image()->first();
            if ($image) {
                $this->cis->delete($image->public_id);
            }
            $category->delete();
        });
    }

}
