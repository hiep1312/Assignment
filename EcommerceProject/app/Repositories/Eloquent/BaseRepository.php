<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Repository;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->model = app()->make($this->getModel());
    }

    public function getAll($criteria = null, $perPage = 20, $columns = ['*'], $pageName = 'page')
    {
        $query = $this->model->query();
        if($criteria) $this->buildCriteria($query, $criteria);

        return is_int($perPage) ? $query->paginate($perPage, $columns, $pageName) : $query->get($columns);
    }

    public function first($criteria = null, $columns = ['*'], $throwNotFound = false)
    {
        $query = $this->model->query();
        if($criteria) $this->buildCriteria($query, $criteria);

        return $throwNotFound ? $query->firstOrFail($columns) : $query->first($columns);
    }

    public function find($idOrCriteria, $columns = ['*'], $throwNotFound = false)
    {
        $query = $this->model->query();

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            return $throwNotFound
                ? $query->findOrFail($idOrCriteria, $columns)
                : $query->find($idOrCriteria, $columns);
        }else {
            $this->buildCriteria($query, $idOrCriteria);
        }

        return $query->get($columns);
    }

    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    public function update($idOrCriteria, $attributes, &$updatedModel = null)
    {
        $query = $this->model->query();
        $shouldReturnUpdatedModel = func_num_args() > 2;

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            $updatedModel = $query->find($idOrCriteria);

            return $updatedModel?->update($attributes);
        }else {
            $this->buildCriteria($query, $idOrCriteria);
            $shouldReturnUpdatedModel && ($updatedModel = $query->get());
        }

        return $shouldReturnUpdatedModel
            ? ($updatedModel->isNotEmpty() ? $updatedModel->toQuery()->update($attributes) : 0)
            : $query->update($attributes);
    }

    public function delete($idOrCriteria)
    {
        $query = $this->model->query();

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            return $query->find($idOrCriteria)->delete();
        }else {
            $this->buildCriteria($query, $idOrCriteria);
        }

        return $query->delete();
    }

    public function restore($idOrCriteria = null)
    {
        $query = $this->model->query();
        if(!in_array(SoftDeletes::class, class_uses($this->getModel()) ?: [])) {
            config('app.env') !== "production" && trigger_error("Warning: You are calling restore() on model [{$this->getModel()}] which does not use SoftDeletes.", E_USER_WARNING);

            return false;
        }else {
            is_null($idOrCriteria) ? $query->onlyTrashed() : $query->withTrashed();
        }

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            return $query->find($idOrCriteria)->restore();
        }else {
            is_null($idOrCriteria) ?: $this->buildCriteria($query, $idOrCriteria);
        }

        return $query->restore();
    }

    public function forceDelete($idOrCriteria = null)
    {
        $query = $this->model->query();
        if(!in_array(SoftDeletes::class, class_uses($this->getModel()) ?: [])) {
            config('app.env') !== "production" && trigger_error("Warning: You are calling forceDelete() on model [{$this->getModel()}] which does not use SoftDeletes.", E_USER_WARNING);
        }else {
            is_null($idOrCriteria) ? $query->onlyTrashed() : $query->withTrashed();
        }

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            return $query->find($idOrCriteria)->forceDelete();
        }else {
            is_null($idOrCriteria) ?: $this->buildCriteria($query, $idOrCriteria);
        }

        return $query->forceDelete();
    }

    public function count($criteria = null, $column = '*')
    {
        $query = $this->model->query();
        if($criteria) $this->buildCriteria($query, $criteria);

        return $query->count($column);
    }

    public function sum($column, $criteria = null){
        $query = $this->model->query();
        if($criteria) $this->buildCriteria($query, $criteria);

        return $query->sum($column);
    }

    public function avg($column, $criteria = null){
        $query = $this->model->query();
        if($criteria) $this->buildCriteria($query, $criteria);

        return $query->avg($column);
    }

    public function exists($criteria)
    {
        $query = $this->model->query();
        $this->buildCriteria($query, $criteria);

        return $query->exists();
    }

    protected function buildCriteria(QueryBuilder|EloquentBuilder &$query, array|callable $criteria): void
    {
        is_callable($criteria) ? $criteria($query) : Repository::handleCriteria($query, $criteria);
    }
}
