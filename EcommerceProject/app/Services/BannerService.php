<?php

namespace App\Services;

use App\Http\Controllers\Api\BannerController;
use App\Models\Banner;
use App\Models\Imageable;
use App\Repositories\Contracts\BannerRepositoryInterface;
use Illuminate\Support\Arr;

class BannerService
{
    protected array $imageFields = ['image_id', 'position'];

    public function __construct(
        protected BannerRepositoryInterface $repository,
    ){}

    public function create(array $data): array
    {
        $createdBanner = $this->repository->create(Arr::except($data, $this->imageFields));
        $createdBanner->imageable()->create(Arr::only($data, $this->imageFields));
        $bannerImage = $this->loadBannerImage($createdBanner);

        return [$createdBanner, $bannerImage];
    }

    public function update(array $data, string $id): array
    {
        $isUpdated = $this->repository->update(
            idOrCriteria: function($query) use ($id) {
                $query->leftJoin('imageables', 'imageables.imageable_id', '=', 'banners.id')
                    ->where('imageables.imageable_type', Banner::class);
                $query->leftjoin('images', 'images.id', '=', 'imageables.image_id');

                $query->where('banners.id', $id);
            },
            attributes: $data,
            forceFill: true,
            updatedModel: $updatedBanner
        );
        $updatedBanner = $updatedBanner->first();

        $bannerImage = null;
        if($updatedBanner){
            $bannerImage = [
                'id' => $updatedBanner->image_id,
                ...$updatedBanner->only(['image_url', 'position', 'created_at', 'imageable_id', 'imageable_type'])
            ];

            $updatedBanner->forceFill([
                'id' => $updatedBanner->imageable_id,
                'created_at' => null
            ]);
        }

        return [(bool) $isUpdated, $updatedBanner, $bannerImage];
    }

    protected function loadBannerImage(Banner $banner): Imageable
    {
        $banner->load('image:' . (implode(',', BannerController::IMAGE_PRIVATE_FIELDS)));

        return $banner->image;
    }
}
