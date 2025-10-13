<?php

namespace App\Repositories;

use App\Helpers\Repository;
use App\Models\User;

class UserRepository
{
    public function getAll(callable|array|null $filters = null, int $perPage = 20, array $columns = ['*'], string $pageName = 'page')
    {
        $query = User::query();
        if($filters){
            is_callable($filters) ? $filters($query) : Repository::handleFilters($query, $filters);
        }

        return $query->paginate($perPage, $columns, $pageName);
    }

    public function count(callable|array|null $filters = null, string $colums = '*')
    {
        $query = User::query();
        if($filters){
            is_callable($filters) ? $filters($query) : Repository::handleFilters($query, $filters);
        }

        return $query->count($colums);
    }
}
