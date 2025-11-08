<?php

namespace App\Repositories\Eloquent;

use App\Models\MailUser;
use App\Repositories\Contracts\MailUserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class MailUserRepository extends BaseRepository implements MailUserRepositoryInterface
{
    public function getModel()
    {
        return MailUser::class;
    }

    public function getAllMailBatches($criteria = null, $perPage = 20, $columns = ['*'], $pageName = 'page')
    {
        $query = DB::query();
        if($criteria) $this->buildCriteria($query, $criteria);

        $query->from($this->model->getTable(), 'mu')
            ->leftJoin('mails as m', 'mu.mail_id', '=', 'm.id')
            ->leftJoin('users as u', 'mu.user_id', '=', 'u.id')
            ->select([
                'mu.batch_key',
                'm.id as mail_id',
                'm.subject',
                'm.body',
                'm.variable',
                'm.type',
                DB::raw('COUNT(mu.id) as total_recipients'),
                DB::raw('SUM(CASE WHEN mu.status = 0 THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN mu.status = 1 THEN 1 ELSE 0 END) as sent_count'),
                DB::raw('SUM(CASE WHEN mu.status = 2 THEN 1 ELSE 0 END) as failed_count'),
                DB::raw('MIN(mu.created_at) as created_at'),
                DB::raw("JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', mu.id,
                        'user_id', u.id,
                        'email', u.email,
                        'first_name', u.first_name,
                        'last_name', u.last_name,
                        'name', TRIM(CONCAT(u.first_name, ' ', u.last_name)),
                        'avatar', u.avatar,
                        'status', mu.status,
                        'sent_at', mu.sent_at,
                        'error_message', mu.error_message
                    )
                ) as recipients")
            ])->groupBy('mu.batch_key', 'm.id', 'm.subject', 'm.body', 'm.variable', 'm.type');

        return is_int($perPage) ? $query->paginate($perPage, $columns, $pageName) : $query->get($columns);
    }
}
