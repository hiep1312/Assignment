<?php

namespace App\Repositories\Eloquent;

use App\Models\BlogComment;
use App\Repositories\Contracts\BlogCommentRepositoryInterface;

class BlogCommentRepository extends BaseRepository implements BlogCommentRepositoryInterface
{
    public function getModel()
    {
        return BlogComment::class;
    }
}
