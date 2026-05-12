<?php

namespace App\DTOs;
use Illuminate\Http\UploadedFile;
class UpdateCategoryDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public int $categoryId, public ?string $name, public ?float $commission, public ?UploadedFile $image)
    {
    }

    public static function fromRequest(\App\Http\Requests\Admin\UpdateCategoryRequest $request): self
    {
        return new self(
            categoryId: $request->input('category_id'),
            name: $request->input('name'),
            commission: $request->input('commission'),
            image: $request->file('image'),
        );
    }
}
