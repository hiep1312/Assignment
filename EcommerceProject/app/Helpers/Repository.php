<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use InvalidArgumentException;
use stdClass;

class Repository
{
    const IS_REPOSITORY_VALUE = '__is_repository_value__';
    const HAS_MULTI_PARAMS = '__has_multi_params__';

    public static function wrapValue(mixed ...$arguments): object
    {
        $hasMultipleParams = count($arguments) > 1;

        return (object)[
            'get' => (object)[
                'value' => $hasMultipleParams ? $arguments : ($arguments[0] ?? null),
                '__has_multi_params__' => $hasMultipleParams,
            ],
            '__is_repository_value__' => true
        ];
    }

    public static function isRepositoryValue(mixed $value): bool
    {
        return (($value instanceof stdClass) && isset($value->{self::IS_REPOSITORY_VALUE}) && isset($value->get->{self::HAS_MULTI_PARAMS}));
    }

    public static function handleFilters(QueryBuilder|EloquentBuilder &$query, array $filters): void
    {
        foreach($filters as $method => $value) {
            if(is_int($method)){
                if(config('app.env') === "production") continue;
                else throw new InvalidArgumentException("Invalid filter key at position {$method}. Each filter key must be a string representing a query method name.");
            }
            $method = preg_replace('/[^a-zA-Z-_]/', '', $method);

            if(self::isRepositoryValue($value)) {
                $value->get->{self::HAS_MULTI_PARAMS}
                    ? $query->{$method}(...$value->get->value)
                    : $query->{$method}($value->get->value);
            }else {
                $query->{$method}($value);
            }
        }
    }
}
