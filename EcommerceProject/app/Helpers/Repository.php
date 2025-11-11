<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use stdClass;

class Repository
{
    const IS_REPOSITORY_VALUE = '__is_repository_value__';
    const HAS_MULTI_PARAMS = '__has_multi_params__';

    public static function wrapValue(mixed ...$arguments): object
    {
        $hasMultipleParams = (count($arguments) > 1 || count($arguments) === 0);

        return (object)[
            'get' => (object)[
                'value' => $hasMultipleParams ? ($arguments ?? []) : ($arguments[0] ?? null),
                self::HAS_MULTI_PARAMS => $hasMultipleParams,
            ],
            self::IS_REPOSITORY_VALUE => true
        ];
    }

    public static function isRepositoryValue(mixed $value): bool
    {
        return (($value instanceof stdClass) && isset($value->{self::IS_REPOSITORY_VALUE}) && isset($value->get->{self::HAS_MULTI_PARAMS}));
    }

    public static function handleCriteria(QueryBuilder|EloquentBuilder &$query, array $criteria): void
    {
        foreach($criteria as $method => $value) {
            $isMethodAsValue = is_int($method);
            $method = preg_replace('/[^a-zA-Z-_]/', '', strval($isMethodAsValue ? $value : $method));

            if(self::isRepositoryValue($value)) {
                $value->get->{self::HAS_MULTI_PARAMS}
                    ? $query->{$method}(...$value->get->value)
                    : $query->{$method}($value->get->value);
            }else {
                $isMethodAsValue ? $query->{$method}() : $query->{$method}($value);
            }
        }
    }
}
