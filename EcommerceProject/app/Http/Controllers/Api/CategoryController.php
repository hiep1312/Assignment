<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\CategoryRequest;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'name', 'slug', 'created_by', 'created_at'];
    const CATEGORYABLE_FIELDS = ['id', 'image_id', 'is_main', 'position', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'creator' => UserController::API_FIELDS,
            'products' => ProductController::API_FIELDS,
            'blogs' => BlogController::API_FIELDS
        ];
    }

    public function __construct(
        protected CategoryRepositoryInterface $repository
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $query->with($this->getRequestedRelations($request));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->where(function($subQuery) use ($request){
                        $subQuery->whereLike('name', '%'. trim($request->search) .'%')
                            ->orWhereLike('slug', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->created_by),
                    fn($innerQuery) => $innerQuery->where('created_by', $request->created_by)
                );
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Category list retrieved successfully.',
            additionalData: $categories->withQueryString()->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        $createdCategory = $this->repository->create(
            $validatedData + ['created_by' => authPayload('sub')]
        );

        return $this->response(
            success: true,
            message: 'Category created successfully.',
            code: 201,
            data: $createdCategory->only(self::API_FIELDS)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        $category = $this->repository->first(
            criteria: function($query) use ($request, $slug){
                $query->with($this->getRequestedRelations($request))
                    ->where('slug', $slug);
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $category,
            message: $category
                ? 'Category retrieved successfully.'
                : 'Category not found.',
            code: $category ? 200 : 404,
            data: $category?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $slug)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: fn($query) => $query->where('slug', $slug),
            attributes: $validatedData,
            updatedModel: $updatedCategory
        );
        $updatedCategory = $updatedCategory->first();

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Category updated successfully.'
                : 'Category not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedCategory?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $isDeleted = $this->repository->delete(
            idOrCriteria: fn($query) => $query->where('slug', $slug)
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Category deleted successfully.'
                : 'Category not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
