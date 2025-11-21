<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use LogicException;

trait ApiQueryRelation
{
    protected function getAllowedRelationsWithFields()
    {
        throw new LogicException("Class ". static::class ." must implement getAllowedRelationsWithFields() to use getRequestedRelations().");
    }

    protected function getRequestedRelations(Request $request): array
    {
        $validRelations = [];

        if($request->has('include')) {
            $rawIncludeParam = $request->query('include', '');
            $requestedRelations = preg_split('/\s*,\s*/', $rawIncludeParam);
            $allowedRelations = array_keys($this->getAllowedRelationsWithFields());

            foreach($requestedRelations as $relation) {
                $relationParts = explode('.', $relation);

                if(in_array($relationParts[0], $allowedRelations, true)){
                    $relationDefinition = $this->getAllowedRelationsWithFields()[$relationParts[0]];
                    $allowedFields = is_object($relationDefinition) ? $relationDefinition->fields : $relationDefinition;

                    $validRelations[] = "{$relationParts[0]}:" . implode(',', $allowedFields);

                    if(count($relationParts) > 1 && is_object($relationDefinition)) {
                        array_push($validRelations, ...$this->resolveNestedRelations(array_slice($relationParts, 1), $relationDefinition, ["{$relationParts[0]}."]));
                    }
                }
            }
        }

        return array_unique($validRelations, SORT_STRING);
    }

    protected function resolveNestedRelations(array $remainingSegments, object $parentSchema, array $prefixedRelations = []): array
    {
        $resolvedRelations = [];

        if(count($remainingSegments) > 0 && isset($parentSchema->{$remainingSegments[0]})) {
            $currentSchema = $parentSchema->{$remainingSegments[0]};
            $resolvedRelations[] = implode('', $prefixedRelations) . "{$remainingSegments[0]}:" . implode(',', is_object($currentSchema) ? $currentSchema->fields : $currentSchema);

            if(is_object($currentSchema)){
                $resolvedRelations = array_merge($resolvedRelations, $this->resolveNestedRelations(array_slice($remainingSegments, 1), $currentSchema, [...$prefixedRelations, "{$remainingSegments[0]}."]));
            }
        }

        return $resolvedRelations;
    }

    protected function getAllowedAggregateRelations()
    {
        throw new LogicException("Class ". static::class ." must implement getAllowedAggregateRelations() to use getRequestedAggregateRelations().");
    }

    protected function getRequestedAggregateRelations(Request $request, QueryBuilder|EloquentBuilder &$query): QueryBuilder|EloquentBuilder
    {
        $aggregateMethodMap = [
            'count' => 'withCount',
            'min' => 'withMin',
            'max' => 'withMax',
            'avg' => 'withAvg',
            'sum' => 'withSum',
        ];

        if($request->has('aggregate')) {
            $rawAggregateParam  = $request->query('aggregate', '');
            $requestedAggregates = preg_split('/\s*,\s*/', $rawAggregateParam);
            $allowedDefinedAggregates = array_keys($this->getAllowedAggregateRelations());
            $allowedAggregateFunctions = array_keys($aggregateMethodMap);
            $allowedAggregateRelations = $this->getAllowedAggregateRelations();

            foreach($requestedAggregates as $aggregate) {
                $aggregateParts = explode(':', $aggregate, 2);
                $aggregateMethod = strtolower($aggregateParts[0]);

                if(
                    !empty($aggregateParts[1]) &&
                    in_array($aggregateMethod, $allowedAggregateFunctions, true) &&
                    in_array($aggregateMethod, $allowedDefinedAggregates, true) &&
                    in_array($aggregateParts[1], is_array($allowedAggregateRelations[$aggregateMethod])
                        ? $allowedAggregateRelations[$aggregateMethod]
                        : [$allowedAggregateRelations[$aggregateMethod]], true)
                ){
                    $eloquentMethod = $aggregateMethodMap[$aggregateMethod];

                    $query->{$eloquentMethod}(...explode('.', $aggregateParts[1] ?? '', 2));
                }
            }
        }

        return $query;
    }
}
