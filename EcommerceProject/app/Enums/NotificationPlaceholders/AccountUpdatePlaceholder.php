<?php

namespace App\Enums\NotificationPlaceholders;

enum AccountUpdatePlaceholder: string
{
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case USERNAME = '{{username}}';
    case EMAIL = '{{email}}';
    case OLD_EMAIL = '{{old_email}}';
    case NEW_EMAIL = '{{new_email}}';
    case UPDATED_AT = '{{updated_at}}';
    case IP_ADDRESS = '{{ip_address}}';

    public function description(): string
    {
        return match ($this) {
            self::FIRST_NAME => 'User first name',
            self::LAST_NAME => 'User last name',
            self::NAME => 'User full name',
            self::USERNAME => 'User account username',
            self::EMAIL => 'Current user email address',
            self::OLD_EMAIL => 'Previous email address (before update)',
            self::NEW_EMAIL => 'New email address (after update)',
            self::UPDATED_AT => 'Time when the account information was updated',
            self::IP_ADDRESS => 'IP address used during the update',
        };
    }
}
