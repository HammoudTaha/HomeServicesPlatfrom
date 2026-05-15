<?php

namespace App\Services\admin;

use Illuminate\Support\Str;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use App\Exceptions\FaildCategoryProcessException;
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
        if (ServiceCategory::where('slug', Str::slug($dto->name))->exists()) {
            throw new FaildCategoryProcessException("Category with the same name already exists.", 400);
        }
        return DB::transaction(function () use ($dto) {
            $category = ServiceCategory::create([
                'name' => $dto->name,
                'slug' => Str::slug($dto->name),
                'commission' => $dto->commission,
            ]);
            $imageData = $this->cis->upload($dto->image);
            $category->image()->create([
                'image_url' => $imageData['url'],
                'public_id' => $imageData['public_id']
            ]);
            $category->refresh();
            return $category;
        });
    }

    public function getAllCategories()
    {
        return ServiceCategory::with('image')->get();
    }
    public function getCategoryById(int $id)
    {
        $category = $this->isFoundCategory($id);
        return $category;
    }

    public function updateCategory(\App\DTOs\UpdateCategoryDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $category = $this->isFoundCategory($dto->categoryId);
            $category->update([
                'name' => $dto->name ?? $category->name,
                'slug' => Str::slug($dto->name ?? $category->name),
                'commission' => $dto->commission ?? $category->commission,
            ]);
            if ($dto->image) {
                $oldImage = $category->image()->first();
                if ($oldImage) {
                    $imageData = $this->cis->update($dto->image, $oldImage->public_id);
                    $category->image()->update([
                        'image_url' => $imageData['url'],
                        'public_id' => $imageData['public_id']
                    ]);
                } else {
                    $imageData = $this->cis->upload($dto->image);
                    $category->image()->create([
                        'image_url' => $imageData['url'],
                        'public_id' => $imageData['public_id']
                    ]);
                }
                return $category->load('image');
            }
            return $category;
        });
    }
    public function deleteCategory(int $id)
    {
        DB::transaction(function () use ($id) {
            $category = $this->isFoundCategory($id);
            $image = $category->image()->first();
            if ($image) {
                $this->cis->delete($image->public_id);
            }
            $category->delete($id);
        });
    }

    private function isFoundCategory(int $id): ServiceCategory
    {
        $category = ServiceCategory::where('id', $id)->with('image')->first();
        if (!$category) {
            throw new FaildCategoryProcessException("Category not found.", 404);
        }
        return $category;
    }

    public function searchCategories(string $query)
    {
        return ServiceCategory::where('name', 'LIKE', "%$query%")->with('image')->get();
    }

    public function activateCategory(int $id)
    {
        $category = $this->isFoundCategory($id);
        $category->update(['is_active' => true]);
    }

    public function deactivateCategory(int $id)
    {
        $category = $this->isFoundCategory($id);
        $category->update(['is_active' => false]);
    }

}
