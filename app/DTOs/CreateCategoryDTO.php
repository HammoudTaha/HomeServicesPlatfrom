<?php

namespace App\DTOs;
use Illuminate\Http\UploadedFile;
class CreateCategoryDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(public string $name, public float $commission, public UploadedFile $image)
    {
    }
    public static function fromRequest(\App\Http\Requests\Admin\CreateCategoryRequest $request): self
    {
        return new self(
            name: $request->input('name'),
            commission: $request->input('commission'),
            image: $request->file('image'),
        );
    }
}
