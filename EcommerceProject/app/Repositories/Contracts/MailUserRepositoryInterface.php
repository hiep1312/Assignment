<?php

namespace App\Repositories\Contracts;

interface MailUserRepositoryInterface extends RepositoryInterface
{
    /**
     * Get all mail batches with aggregated recipient information and statistics.
     *
     * This method retrieves mail batches grouped by batch_key, including detailed information
     * about the mail content, recipient counts by status, and a complete list of recipients
     * with their details.
     *
     * @param callable(\Illuminate\Database\Query\Builder $query)|array|null $criteria Optional. An array of conditions or a callback function to apply custom query criteria.
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
    public function getAllMailBatches($criteria = null, $perPage = 20, $columns = ['*'], $pageName = 'page');
}
