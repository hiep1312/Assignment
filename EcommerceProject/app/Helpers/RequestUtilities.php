<?php

namespace App\Helpers;

trait RequestUtilities
{
    protected string|true|null $routeKeyForUpdate = null;

    protected function isUpdate(string|true $routeKey): bool
    {
        $this->routeKeyForUpdate = $routeKey;

        return ($this->isMethod('put') || $this->isMethod('patch')) && (is_bool($routeKey) ?: $this->route($routeKey));
    }

    protected function applyUpdateRules(array $rules, string|true|null $routeKey = null): array
    {
        if(($routeKey || $this->routeKeyForUpdate) && $this->isUpdate($routeKey ?? $this->routeKeyForUpdate)){
            return array_map(function($rule) {
                return is_array($rule) ? ['sometimes', ...$rule] : "sometimes|{$rule}";
            }, $rules);
        }

        return $rules;
    }
}
