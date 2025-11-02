<?php

namespace App\Enums\NotificationPlaceholders;

enum SystemPlaceholder: string
{
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case EMAIL = '{{email}}';
    case MAINTENANCE_START = '{{maintenance_start}}';
    case MAINTENANCE_END = '{{maintenance_end}}';
    case VERSION = '{{version}}';
    case FEATURE_NAME = '{{feature_name}}';

    public function description(): string
    {
        return match ($this) {
            self::FIRST_NAME => 'User first name',
            self::LAST_NAME => 'User last name',
            self::NAME => 'User full name',
            self::EMAIL => 'User email address',
            self::MAINTENANCE_START => 'Start time of the scheduled maintenance period',
            self::MAINTENANCE_END => 'End time of the scheduled maintenance period',
            self::VERSION => 'System or application version number',
            self::FEATURE_NAME => 'Name of the updated or new system feature',
        };
    }
}
