<?php

namespace App\Services;

use App\Repositories\Contracts\BlogCommentRepositoryInterface;
use Illuminate\Database\QueryException;

class BlogCommentService
{
    public function __construct(
        protected BlogCommentRepositoryInterface $repository,
    ){}

    public function create(array $data, string $blogId): array
    {
        try {
            $createdComment = $this->repository->create(
                attributes: array_merge(
                    $data,
                    ['blog_id' => $blogId, 'user_id' => authPayload('sub')]
                )
            );

            return [true, $createdComment];

        }catch(QueryException $queryException) {
            return [false, null];
        }
    }
}
