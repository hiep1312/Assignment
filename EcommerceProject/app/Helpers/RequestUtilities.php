<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use LogicException;

trait RequestUtilities
{
    protected function getFillableFields()
    {
        throw new LogicException("The method getFillableFields() is not defined. You must provide the fields to fill or merge.");
    }

    protected function isUpdate(string|true $routeKey): bool
    {
        return ($this->isMethod('put') || $this->isMethod('patch')) && (is_bool($routeKey) ?: $this->route($routeKey));
    }

    protected function fillMissingWithExisting(?Model $model, ?array $dataOld, array $dataNew): void
    {
        unset($this['id']);
        $model && $this->merge(array_merge($dataOld, $dataNew));
    }
}
