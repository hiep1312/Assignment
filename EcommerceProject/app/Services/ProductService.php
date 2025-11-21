<?php

namespace App\Services;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ImageController;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Arr;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $repository,
    ){}

    public function create(array $data): array
    {
        $createdProduct = $this->repository->create(Arr::except($data, ['main_image', 'images', 'categories']));
        $imageIds = array_merge(
            isset($data['main_image']) ? [$data['main_image']] : [],
            $data['images'] ?? []
        );
        $relationsToLoad = [];

        if(!empty($imageIds)){
            $createdProduct->images()->attach(Arr::mapWithKeys($imageIds, [$this, 'buildImagePivotData']));
            array_push($relationsToLoad, 'images');
        }

        if(!empty($data['categories']) && is_array($data['categories'])){
            $createdProduct->categories()->attach($data['categories']);
            array_push($relationsToLoad, 'categories');
        }

        !empty($relationsToLoad) && $createdProduct->load($relationsToLoad);
        return [$createdProduct];
    }

    public function update(array $data, string $slug): array
    {
        $isUpdated = $this->repository->update(
            idOrCriteria: fn($query) => $query->where('slug', $slug),
            attributes: Arr::except($data, ['main_image', 'images', 'categories']),
            updatedModel: $updatedProduct
        );

        $updatedProduct = $updatedProduct->first();
        if(!$isUpdated) return [false, null];

        $imageIds = array_merge(
            isset($data['main_image']) ? [$data['main_image']] : [],
            $data['images'] ?? []
        );
        $relationsToLoad = [];

        if(!empty($imageIds)){
            $updatedProduct->images()->sync(Arr::mapWithKeys($imageIds, [$this, 'buildImagePivotData']));
            array_push($relationsToLoad, 'images:' . (implode(',', ImageController::API_FIELDS)));
        }

        if(!empty($data['categories']) && is_array($data['categories'])){
            $updatedProduct->categories()->sync($data['categories']);
            array_push($relationsToLoad, 'categories:' . (implode(',', CategoryController::API_FIELDS)));
        }

        !empty($relationsToLoad) && $updatedProduct->load($relationsToLoad);
        return [(bool) $isUpdated, $updatedProduct];
    }

    public function buildImagePivotData($imageId, $position): array
    {
        return [
            $imageId => [
                'is_main' => $position === 0,
                'position' => $position
            ]
        ];
    }
}
