<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\BannerRequest;
use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Services\BannerService;
use Illuminate\Http\Request;

class BannerController extends BaseApiController
{
    const API_FIELDS = ['id', 'title', 'link_url', 'status', 'created_at'];
    const IMAGE_PRIVATE_FIELDS = ['images.id', 'images.image_url', 'imageables.position', 'images.created_at', 'imageables.imageable_id', 'imageables.imageable_type'];

    public function __construct(
        protected BannerRepositoryInterface $repository,
        protected BannerService $service
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $banners = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $query->with('image:' . (implode(',', self::IMAGE_PRIVATE_FIELDS)));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->where(function($subQuery) use ($request){
                        $subQuery->whereLike('title', '%'. trim($request->search) .'%')
                            ->orWhereLike('link_url', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->status),
                    fn($innerQuery) => $innerQuery->where('status', $request->status)
                )->when(
                    isset($request->position_range),
                    function($innerQuery) use ($request){
                        $positionRange = is_array($request->position_range) ? $request->position_range : preg_split('/\s*-\s*/', $request->position_range, 2);
                        $minPosition = is_numeric($positionRange[0]) ? (int) $positionRange[0] : 0;
                        $maxPosition = is_numeric($positionRange[1] ?? null) ? (int) $positionRange[1] : PHP_INT_MAX;

                        $innerQuery->whereHas('image', fn($subQuery) => $subQuery->whereBetween('position', [$minPosition, $maxPosition]));
                    }
                );

                $this->repository->orderByImagePosition($query, 'asc');
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Banner list retrieved successfully.',
            additionalData: $banners->withQueryString()->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BannerRequest $request)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        [$createdBanner, $createdImage] = $this->service->create($validatedData);

        return $this->response(
            success: (bool) $createdBanner,
            message: 'Banner created successfully.',
            code: 201,
            data: array_merge(
                $createdBanner->only(self::API_FIELDS),
                ['image' => $createdImage->toArray()]
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $banner = $this->repository->first(
            criteria: function($query) use ($id){
                $query->with('image:' . (implode(',', self::IMAGE_PRIVATE_FIELDS)))
                    ->where('id', $id);
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $banner,
            message: $banner
                ? 'Banner retrieved successfully.'
                : 'Banner not found.',
            code: $banner ? 200 : 404,
            data: $banner?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BannerRequest $request, string $id)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        [$isUpdated, $updatedBanner, $updatedImage] = $this->service->update($validatedData, $id);

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Banner updated successfully.'
                : 'Banner not found.',
            code: $isUpdated ? 200 : 404,
            data: array_merge(
                $updatedBanner?->only(self::API_FIELDS) ?? [],
                $updatedBanner ? ['image' => $updatedImage] : []
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $isDeleted = $this->repository->delete(
            idOrCriteria: fn($query) => $query->where('id', $id)
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Banner deleted successfully.'
                : 'Banner not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
