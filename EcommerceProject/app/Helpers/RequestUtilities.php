<?php

namespace App\Helpers;

trait RequestUtilities
{
    protected function isUpdate(string|true $routeKey): bool
    {
        return ($this->isMethod('put') || $this->isMethod('patch')) && (is_bool($routeKey) ?: $this->route($routeKey));
    }
}
