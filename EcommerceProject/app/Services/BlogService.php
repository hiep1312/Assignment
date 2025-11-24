<?php

namespace App\Services;

use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CategoryController;
use App\Models\Blog;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Support\Arr;

class BlogService
{
    public function __construct(
        protected BlogRepositoryInterface $repository,
    ){}

    public function create(array $data): array
    {
        $createdBlog = $this->repository->create(array_merge(
            Arr::except($data, ['thumbnail', 'categories']),
            ['author_id' => authPayload('sub')]
        ));
        $createdBlog->imageable()->create([
            'image_id' => $data['thumbnail'],
            'is_main' => true
        ]);

        $relationsToLoad = ['thumbnail:' . (implode(',', BlogController::THUMBNAIL_PRIVATE_FIELDS))];

        if(!empty($data['categories']) && is_array($data['categories'])){
            $createdBlog->categories()->attach($data['categories']);
            array_push($relationsToLoad, 'categories:' . (implode(',', CategoryController::API_FIELDS)));
        }

        $createdBlog->load($relationsToLoad);
        return [$createdBlog];
    }

    public function update(array $data, string $slug): array
    {
        $isUpdated = $this->repository->update(
            idOrCriteria: fn($query) => $query->where('slug', $slug),
            attributes: Arr::except($data, ['thumbnail', 'categories']),
            updatedModel: $updatedBlog
        );

        $updatedBlog = $updatedBlog->first();
        if(!$isUpdated) return [false, null];

        if(!empty($data['thumbnail'])){
            $updatedBlog->imageable()->updateOrCreate([
                'imageable_id' => $updatedBlog->id,
                'imageable_type' => Blog::class,
                'is_main' => true
            ], [
                'image_id' => $data['thumbnail']
            ]);
        }

        if(!empty($data['categories']) && is_array($data['categories'])){
            $updatedBlog->categories()->sync($data['categories']);
        }

        $updatedBlog->load(['thumbnail:' . (implode(',', BlogController::THUMBNAIL_PRIVATE_FIELDS)), 'categories:' . (implode(',', CategoryController::API_FIELDS))]);
        return [(bool) $isUpdated, $updatedBlog];
    }
}
