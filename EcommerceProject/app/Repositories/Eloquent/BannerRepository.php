<?php

namespace App\Repositories\Eloquent;

use App\Models\Banner;
use App\Models\Imageable;
use App\Repositories\Contracts\BannerRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BannerRepository extends BaseRepository implements BannerRepositoryInterface
{
    public function getModel()
    {
        return Banner::class;
    }

    public function orderByImagePosition(&$query, $direction = 'asc')
    {
        return $query->orderBy(
            Imageable::select('position')
                ->whereColumn('imageable_id', "{$this->model->getTable()}.{$this->model->getKeyName()}")
                ->where('imageable_type', $this->getModel())
                ->limit(1),
            $direction
        );
    }

    public function toggleStatusById($id)
    {
        return $this->model->where($this->model->getKeyName(), $id)->update(['status' => DB::raw('CASE status WHEN 1 THEN 2 ELSE 1 END')]);
    }

    public function reorderPositions()
    {
        DB::statement("SET @row_num = 0");

        return DB::statement(<<<SQL
            WITH ordered AS (
                SELECT id, (@row_num := @row_num + 1) AS position_new
                FROM imageables
                WHERE imageable_type = :model
                ORDER BY position ASC
            )

            UPDATE imageables
            JOIN ordered ON imageables.id = ordered.id
            SET imageables.position = ordered.position_new
        SQL, ['model' => $this->getModel()]);
    }

    public function getUsedPositions($ignoreId = null)
    {
        return $this->model->query()
            ->join('imageables', 'imageables.imageable_id', '=', "{$this->model->getTable()}.{$this->model->getKeyName()}")
            ->where('imageable_type', $this->getModel())
            ->when($ignoreId, fn ($query) => $query->whereNot('imageables.imageable_id', $ignoreId))
            ->pluck('imageables.position')
            ->toArray();
    }
}
