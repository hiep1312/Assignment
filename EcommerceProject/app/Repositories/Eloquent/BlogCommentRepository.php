<?php

namespace App\Repositories\Eloquent;

use App\Models\BlogComment;
use App\Repositories\Contracts\BlogCommentRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BlogCommentRepository extends BaseRepository implements BlogCommentRepositoryInterface
{
    public function getModel()
    {
        return BlogComment::class;
    }
}
