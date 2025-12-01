<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\BlogRequest;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'title', 'slug', 'content', 'author_id', 'status', 'created_at'];
    const THUMBNAIL_PRIVATE_FIELDS = ['images.id', 'images.image_url', 'images.created_at', 'imageables.imageable_id', 'imageables.imageable_type'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'author' => UserController::API_FIELDS,
            'categories' => CategoryController::API_FIELDS,
            'comments' => BlogCommentController::API_FIELDS
        ];
    }

    protected function getAllowedAggregateRelations(): array
    {
        return [
            'count' => 'comments',
        ];
    }

    public function __construct(
        protected BlogRepositoryInterface $repository,
        protected BlogService $service
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $blogs = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $this->getRequestedAggregateRelations($request, $query)
                    ->with(['thumbnail:' . (implode(',', self::THUMBNAIL_PRIVATE_FIELDS)), ...$this->getRequestedRelations($request)]);

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->where(function($subQuery) use ($request){
                        $subQuery->whereLike('title', '%'. trim($request->search) .'%')
                            ->orWhereLike('content', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->category),
                    function($innerQuery) use ($request){
                        $innerQuery->whereHas('categories', function($subQuery) use ($request){
                            $subQuery->where('categories.slug', $request->category)
                                ->orWhere('categories.id', $request->category);
                        });
                    }
                )->when(
                    isset($request->author),
                    fn($innerQuery) => $innerQuery->where('author_id', $request->author)
                );

                if(authPayload('sub', null, false) === UserRole::ADMIN->value){
                    $query->when(
                        isset($request->status),
                        fn($innerQuery) => $innerQuery->where('status', $request->status)
                    );
                }else {
                    $query->where('status', 1);
                }
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Blog list retrieved successfully.',
            additionalData: $blogs->withQueryString()->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogRequest $request)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        [$createdBlog] = $this->service->create($validatedData);

        return $this->response(
            success: true,
            message: 'Blog created successfully.',
            code: 201,
            data: $createdBlog->only([...self::API_FIELDS, 'thumbnail', 'categories'])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        $blog = $this->repository->first(
            criteria: function($query) use ($request, $slug){
                $this->getRequestedAggregateRelations($request, $query)
                    ->with(['thumbnail:' . (implode(',', self::THUMBNAIL_PRIVATE_FIELDS)), ...$this->getRequestedRelations($request)])
                    ->where('slug', $slug);
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $blog,
            message: $blog
                ? 'Blog retrieved successfully.'
                : 'Blog not found.',
            code: $blog ? 200 : 404,
            data: $blog?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogRequest $request, string $slug)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        [$isUpdated, $updatedBlog] = $this->service->update($validatedData, $slug);

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Blog updated successfully.'
                : 'Blog not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedBlog?->only([...self::API_FIELDS, 'thumbnail', 'categories']) ?? [],
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
                ? 'Blog deleted successfully.'
                : 'Blog not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
