<?php

namespace App\Repositories\Eloquent;

use App\Models\Imageable;
use App\Repositories\Contracts\ImageableRepositoryInterface;

class ImageableRepository extends BaseRepository implements ImageableRepositoryInterface
{
    public function getModel()
    {
        return Imageable::class;
    }
}
