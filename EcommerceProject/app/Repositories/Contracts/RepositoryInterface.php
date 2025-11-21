<?php

namespace App\Repositories\Contracts;

use ErrorException;

interface RepositoryInterface
{
    /**
     * Get all records from the repository with optional criteria and pagination.
     *
     * @param callable(\Illuminate\Database\Eloquent\Builder $query)|array|null $criteria Optional. An array of conditions or a callback function to apply custom query criteria.
     * @param int|false $perPage Optional. Number of items per page for pagination.
     *        - If an integer is provided, results are paginated.
     *        - If false, all results are returned without pagination.
     * @param array $columns Optional. The columns to select. Default is ['*'] (all columns).
     * @param string $pageName Optional. The page query parameter name used for pagination. Default is 'page'.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     *         - Returns a LengthAwarePaginator if $perPage is an integer (paginated results).
     *         - Returns a Collection if $perPage is false (all results).
     */
    public function getAll($criteria = null, $perPage = 20, $columns = ['*'], $pageName = 'page');

    /**
     * Get the first record that matches the given criteria.
     *
     * @param callable(\Illuminate\Database\Eloquent\Builder $query)|array|null $criteria Optional. An array of conditions or a callback function to apply custom query criteria.
     * @param array|string $columns Optional. The columns to select. Default is ['*'] (all columns).
     * @param bool $throwNotFound Optional. If true, throws a ModelNotFoundException when no record is found.
     *        Default is false (returns null if no record is found).
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     *         - Returns the first matching model.
     *         - Returns null if no record is found and $throwNotFound is false.
     *         - Throws \Illuminate\Database\Eloquent\ModelNotFoundException if $throwNotFound is true and no record is found.
     */
    public function first($criteria = null, $columns = ['*'], $throwNotFound = false);

    /**
     * Find one or multiple records from the repository.
     *
     * @param int|string|array|callable(\Illuminate\Database\Eloquent\Builder $query) $idOrCriteria
     *        - If int|string: treated as the ID of a single record to find.
     *        - If array: treated as a set of conditions to apply complex queries.
     *        - If callable: a function that receives the query builder for custom, complex filtering.
     * @param array|string $columns Optional. The columns to select. Default is ['*'] (all columns).
     * @param bool $throwNotFound Optional. If true, throws a ModelNotFoundException when no record is found.
     *        Default is false.
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|null
     *         - Returns a single Model if searching by ID.
     *         - Returns a Collection of models if filtering by array or callable.
     *         - Returns null if no record is found and $throwNotFound is false.
     *         - Throws \Illuminate\Database\Eloquent\ModelNotFoundException if $throwNotFound is true and no record is found.
     */
    public function find($idOrCriteria, $columns = ['*'], $throwNotFound = false);

    /**
     * Create a new record.
     *
     * @param array $attributes The data to create the record.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($attributes = []);

    /**
     * Update a record or multiple records in the repository.
     *
     * @param int|string|array|callable(\Illuminate\Database\Eloquent\Builder $query) $idOrCriteria
     *        - If int|string: treated as the ID of a single record to update.
     *        - If array: treated as criteria to apply complex queries for multiple records.
     *        - If callable: a function that receives the query builder for custom, complex filtering.
     * @param array $attributes The attributes/data to update the record(s) with.
     * @param bool $rawEnabled (Optional, default: false) When true, allows raw SQL expressions (e.g., DB::raw())
     *        in the update attributes. The model will be filled only with non-Expression values for safety.
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|null $updatedModel (Optional, passed by reference)
     *        - Receives the model (for single update) or collection (for batch update) before the update is executed.
     *        - Null if no record is found.
     *
     * @return bool|int
     *         - Returns true if a single record was successfully updated, false if update failed or record not found.
     *         - Returns the number of affected rows (int) when updating multiple records via criteria.
     */
    public function update($idOrCriteria, $attributes, $rawEnabled = false, &$updatedModel = null);

    /**
     * Insert or update multiple records in the database in a single query.
     *
     * @param array $values An array of values to insert or update.
     *                         Each item should be an associative array representing a row.
     * @param array|string $uniqueBy The column(s) that uniquely identify records.
     *                                  Can be a single column name (string) or an array of column names.
     * @param  array|null $update The columns to update if a matching record exists.
     *                              If null or empty, all columns except those in $uniqueBy will be updated.
     *
     * @return int  The number of affected rows.
     *
     * @throws \Illuminate\Database\QueryException If the query fails.
     *
     * @see \Illuminate\Database\Eloquent\Builder::upsert()
     */
    public function upsert($values, $uniqueBy, $update = null);

    /**
     * Delete a record or multiple records from the repository.
     *
     * @param int|string|array|callable(\Illuminate\Database\Eloquent\Builder $query) $idOrCriteria
     *        - If int|string: treated as the ID of a single record to delete.
     *        - If array: treated as criteria to apply complex queries for multiple records.
     *        - If callable: a function that receives the query builder for custom, complex filtering.
     * @param callable|null $beforeDelete
     *        - Optional callback executed before deletion.
     *        - For single record: receives the model instance.
     *        - For multiple records: receives a collection of models to be deleted.
     *
     * @return bool|int
     *         - Returns true if a single record was successfully deleted.
     *         - Returns the number of affected rows if multiple records were deleted.
     */
    public function delete($idOrCriteria, ?callable $beforeDelete = null);

    /**
     * Restore a soft-deleted record or multiple records in the repository.
     *
     * @param int|string|array|callable(\Illuminate\Database\Eloquent\Builder $query)|null $idOrCriteria
     *        - If null: restore ALL soft-deleted records (only trashed records).
     *        - If int|string: treated as the ID of a single record to restore.
     *        - If array: treated as criteria to apply complex queries for multiple records.
     *        - If callable: a function that receives the query builder for custom, complex filtering.
     *        - If the model does not use SoftDeletes, the method will return false and optionally trigger a warning in non-production environments.
     *
     * @return bool|int
     *         - Returns false if the model does not use SoftDeletes trait.
     *         - Returns true if a single record was successfully restored.
     *         - Returns the number of affected rows if multiple records were restored.
     * @throws ErrorException
     *         - Triggers a warning (E_USER_WARNING) in non-production environments if the model does not use SoftDeletes trait.
     */
    public function restore($idOrCriteria = null);

    /**
     * Permanently delete a record or multiple records from the repository, bypassing soft deletes (if applicable).
     *
     * @param int|string|array|callable(\Illuminate\Database\Eloquent\Builder $query)|null $idOrCriteria
     *        - If null: force delete ALL soft-deleted records (only trashed records).
     *        - If int|string: treated as the ID of a single record to permanently delete.
     *        - If array: treated as criteria to apply complex queries for multiple records.
     *        - If callable: a function that receives the query builder for custom, complex filtering.
     * @param callable|null $beforeDelete
     *        - Optional callback executed before force deletion.
     *        - For single record: receives the model instance.
     *        - For multiple records: receives a collection of models to be force deleted.
     *
     * @return bool|int
     *         - Returns true if a single record was successfully deleted permanently.
     *         - Returns the number of affected rows if multiple records were deleted permanently.
     * @throws ErrorException
     *         - Triggers a warning (E_USER_WARNING) in non-production environments if the model does not use SoftDeletes trait.
     */
    public function forceDelete($idOrCriteria = null, ?callable $beforeDelete = null);

    /**
     * Count the number of records that match the given criteria.
     *
     * @param callable(\Illuminate\Database\Eloquent\Builder $query)|array|null $criteria
     *        Optional. A callback or an array of conditions to apply complex queries.
     *        - If a callable is provided, it receives the query builder instance for custom, complex filtering.
     *        - If an array is provided, each element should represent a condition for complex queries.
     *        - If null, all records will be counted.
     * @param string $column
     *        Optional. The column name to count. Default is '*'.
     *
     * @return int The total number of matching records.
     */
    public function count($criteria = null, $column = '*');

    /**
     * Calculate the sum of values in a specific column that match the given criteria.
     *
     * @param string $column The column name to calculate the sum for.
     *
     * @param callable(\Illuminate\Database\Eloquent\Builder $query)|array|null $criteria
     *        Optional. A callback or an array of conditions to apply complex queries.
     *        - If a callable is provided, it receives the query builder instance for custom, complex filtering.
     *        - If an array is provided, each element should represent a condition for complex queries.
     *        - If null, all records will be included in the calculation.
     *
     * @return float|int The total sum of the specified column for the matching records.
     */
    public function sum($column, $criteria = null);

    /**
     * Calculate the average values of a specific column that match the given criteria.
     *
     * @param string $column The column name to calculate the average for.
     *
     * @param callable(\Illuminate\Database\Eloquent\Builder $query)|array|null $criteria
     *        Optional. A callback or an array of conditions to apply complex queries.
     *        - If a callable is provided, it receives the query builder instance for custom, complex filtering.
     *        - If an array is provided, each element should represent a condition for complex queries.
     *        - If null, all records will be included in the calculation.
     *
     * @return float|null The average value of the specified column for the matching records, or null if no records match.
     */
    public function avg($column, $criteria = null);

    /**
     * Determine whether any records exist that match the given criteria.
     *
     * @param callable(\Illuminate\Database\Eloquent\Builder $query)|array $criteria
     *        Required. A callback or an array of conditions to apply complex queries.
     *        - If a callable is provided, it receives the query builder instance for custom, complex filtering.
     *        - If an array is provided, each element should represent a condition for complex queries.
     *
     * @return bool Returns true if at least one matching record exists, false otherwise.
     */
    public function exists($criteria);

    /**
     * Get the fully qualified class name of the Eloquent model associated with this repository.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model> The fully qualified model class name.
     */
    public function getModel();
}
