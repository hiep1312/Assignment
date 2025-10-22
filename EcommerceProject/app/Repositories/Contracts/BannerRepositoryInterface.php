<?php

namespace App\Repositories\Contracts;

interface BannerRepositoryInterface extends RepositoryInterface
{
    /**
     * Apply an ORDER BY clause to the query based on the related image position.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     *     The query builder instance to apply the ordering to (passed by reference).
     * @param string $direction
     *     The sort direction ('asc' or 'desc'). Defaults to 'asc'.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     *     The modified query builder instance for method chaining.
     */
    public function orderByImagePosition(&$query, $direction = 'asc');

    /**
     * Toggle the `status` field of a record by its ID.
     *
     * @param int|string $id
     *     The ID of the record whose status should be toggled. Can be an integer
     *     or string depending on the database type.
     *
     * @return int
     *     The number of records affected by the update. Typically 1 if the record
     *     exists, or 0 if no record with the given ID was found.
     */
    public function toggleStatusById($id);

    /**
     * Reorders the positions of related images for banners.
     *
     * @return int The number of rows affected by the update query.
     */
    public function reorderPositions();

    /**
     * Get all used positions for imageables, optionally excluding a specific record.
     *
     * @param int|string|null $ignoreId Optional. The ID of the record to exclude from the results.
     *                                  If null, all positions will be returned.
     * @return array An array of used position values
     */
    public function getUsedPositions($ignoreId = null);
}
