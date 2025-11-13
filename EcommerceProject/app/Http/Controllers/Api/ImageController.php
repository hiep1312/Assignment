<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelationHelper;
use App\Http\Requests\Client\ImageRequest;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends BaseApiController
{
    use ApiQueryRelationHelper;

    const API_FIELDS = ['id', 'image_url', 'created_at'];
    const IMAGEABLE_FIELDS = ['id', 'image_id', 'is_main', 'position', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'products' => ProductController::API_FIELDS,
        ];
    }

    public function __construct(
        protected ImageRepositoryInterface $repository
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $images = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $query->with($this->getRequestedRelations($request));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->whereLike('image_url', '%'. trim($request->search) .'%');
                });
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Image list retrieved successfully.',
            additionalData: $images->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ImageRequest $request)
    {
        $validatedData = $request->validated();
        $createdImage = $this->repository->create([
            'image_url' => storeImage($validatedData['photo'], 'images', getFileName($validatedData['photo']))
        ]);

        return $this->response(
            success: true,
            message: 'Image created successfully.',
            code: 201,
            data: $createdImage->only(self::API_FIELDS)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $image = $this->repository->first(
            criteria: function($query) use ($request, $id){
                $query->with($this->getRequestedRelations($request))
                    ->where('id', $id);
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $image,
            message: $image
                ? 'Image retrieved successfully.'
                : 'Image not found.',
            code: $image ? 200 : 404,
            data: $image?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ImageRequest $request, string $id)
    {
        $validatedData = $request->validated();
        if($image = $this->repository->find($id)){
            Storage::disk('public')->putFileAs('images', $validatedData['photo'], basename($image->image_url));
            $image->touch();
        }

        return $this->response(
            success: (bool) $image,
            message: $image
                ? 'Image updated successfully.'
                : 'Image not found.',
            code: $image ? 200 : 404,
            data: $image?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $isDeleted = $this->repository->delete($id);

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Image deleted successfully.'
                : 'Image not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
