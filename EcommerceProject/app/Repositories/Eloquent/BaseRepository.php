<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Repository;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Expression;

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

    public function update($idOrCriteria, $attributes, $rawEnabled = false, $forceFill = false, &$updatedModel = null)
    {
        $query = $this->model->query();
        $shouldReturnUpdatedModel = func_num_args() > 4;

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            $updatedModel = $query->find($idOrCriteria);

            if($rawEnabled && $updatedModel){
                return (bool) $this->safeFill($updatedModel, $attributes)
                    ->query()->where($updatedModel->getKeyName(), $updatedModel->getKey())
                    ->update($attributes);
            }

            return $updatedModel?->update($attributes);
        }else {
            $this->buildCriteria($query, $idOrCriteria);

            if($shouldReturnUpdatedModel){
                $updatedModel = $query->get();

                $updatedModel->when($updatedModel->isNotEmpty(), function($models) use ($attributes, $rawEnabled, $forceFill){
                    $models->each(function($model) use ($attributes, $rawEnabled, $forceFill) {
                        $rawEnabled
                            ? $this->safeFill($model, $attributes, $forceFill)
                            : ($forceFill ? $model->forceFill($attributes) : $model->fill($attributes));
                    });
                });
            }
        }

        return $shouldReturnUpdatedModel
            ? ($updatedModel->isNotEmpty() ? $query->update($attributes) : 0)
            : $query->update($attributes);
    }

    public function upsert($values, $uniqueBy, $update = null)
    {
        return $this->model->upsert($values, $uniqueBy, $update);
    }

    public function delete($idOrCriteria, ?callable $beforeDelete = null)
    {
        $query = $this->model->query();

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            $targetModel = $query->find($idOrCriteria);
            $beforeDelete && $beforeDelete($targetModel);

            return $targetModel->delete();
        }else {
            $this->buildCriteria($query, $idOrCriteria);
            $beforeDelete && $beforeDelete($query->get());
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

    public function forceDelete($idOrCriteria = null, ?callable $beforeDelete = null)
    {
        $query = $this->model->query();
        if(!in_array(SoftDeletes::class, class_uses($this->getModel()) ?: [])) {
            config('app.env') !== "production" && trigger_error("Warning: You are calling forceDelete() on model [{$this->getModel()}] which does not use SoftDeletes.", E_USER_WARNING);
        }else {
            is_null($idOrCriteria) ? $query->onlyTrashed() : $query->withTrashed();
        }

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            $targetModel = $query->find($idOrCriteria);
            $beforeDelete && $beforeDelete($targetModel);

            return $targetModel->forceDelete();
        }else {
            is_null($idOrCriteria) ?: $this->buildCriteria($query, $idOrCriteria);
            $beforeDelete && $beforeDelete($query->get());
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

    /**
     * Safely fill a model with attributes, excluding raw SQL expressions.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The model instance to force fill (passed by reference).
     * @param array $attributes The attributes array that may contain both regular values and Expression instances.
     * @param bool $forceFill Optional, default false. Controls which fill method is used
     *
     * @return \Illuminate\Database\Eloquent\Model Returns the filled model instance for method chaining.
     */
    protected function safeFill(Model &$model, array $attributes, bool $forceFill = false): Model
    {
        $cleanAttributes = array_filter($attributes, fn($value) => !($value instanceof Expression));
        $forceFill ? $model->forceFill($cleanAttributes) : $model->fill($cleanAttributes);

        return $model;
    }
}
