<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\BlogCommentRequest;
use App\Repositories\Contracts\BlogCommentRepositoryInterface;
use Illuminate\Http\Request;

class BlogCommentController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'blog_id', 'user_id', 'content', 'parent_id', 'reply_to', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        $nestedCommentFields = (object) [
            'fields' => self::API_FIELDS,
            'user' => UserController::API_FIELDS
        ];

        return [
            'user' => UserController::API_FIELDS,
            'parent' => $nestedCommentFields,
            'children' => $nestedCommentFields,
            'replyTo' => $nestedCommentFields,
            'replies' => $nestedCommentFields
        ];
    }

    public function __construct(
        protected BlogCommentRepositoryInterface $repository,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slugBlog)
    {
        $comments = $this->repository->getAll(
            criteria: function(&$query) use ($request, $slugBlog) {
                $query->with($this->getRequestedRelations($request));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->whereLike('content', '%'. trim($request->search) .'%');
                })->when(
                    isset($request->root_only) && boolval($request->root_only),
                    fn($innerQuery) => $innerQuery->whereNull('parent_id')
                )->when(
                    isset($request->include_deleted) && boolval($request->include_deleted),
                    fn($innerQuery) => $innerQuery->withTrashed()
                );

                $query->whereHas(
                    'blog',
                    fn($subQuery) => $subQuery->where('slug', $slugBlog)
                );
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Blog comment list retrieved successfully.',
            additionalData: $comments->withQueryString()->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogCommentRequest $request, string $slugBlog)
    {
        $validatedData = $request->validated();
        $isCreated = $this->repository->createByBlogSlug(
            attributes: $validatedData + ['user_id' => authPayload('sub')],
            slug: $slugBlog,
            createdModel: $createdComment
        );

        return $this->response(
            success: (bool) $isCreated,
            message: $isCreated
                ? 'Blog comment created successfully.'
                : 'Failed to create blog comment.',
            code: $isCreated ? 201 : 400,
            data: $createdComment?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $comment = $this->repository->first(
            criteria: function($query) use ($request, $id){
                $query->with($this->getRequestedRelations($request))
                    ->where('id', $id);
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $comment,
            message: $comment
                ? 'Blog comment retrieved successfully.'
                : 'Blog comment not found.',
            code: $comment ? 200 : 404,
            data: $comment?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogCommentRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: fn($query) => $query->where('id', $id)
                ->where('user_id', authPayload('sub')),
            attributes: $validatedData,
            updatedModel: $updatedComment
        );
        $updatedComment = $updatedComment->first();

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Blog comment updated successfully.'
                : 'Blog comment not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedComment?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ['role' => $role, 'sub' => $userId] = authPayload();

        $isDeleted = $this->repository->delete(
            idOrCriteria: function($query) use ($id, $role, $userId){
                $query->where('id', $id)
                    ->when($role === UserRole::USER->value, fn($innerQuery) => $innerQuery->where('user_id', $userId));
            }
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Blog comment deleted successfully.'
                : 'Blog comment not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
