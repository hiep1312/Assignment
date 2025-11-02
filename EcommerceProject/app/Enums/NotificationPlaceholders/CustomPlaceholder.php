<?php

namespace App\Enums\NotificationPlaceholders;

enum CustomPlaceholder: string
{
    case EMAIL = '{{email}}';
    case USERNAME = '{{username}}';
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case BIRTHDAY = '{{birthday}}';
    case AVATAR = '{{avatar}}';
    case ROLE = '{{role}}';

    public function description(): string
    {
        return match ($this) {
            self::EMAIL => 'User email address',
            self::USERNAME => 'User account username',
            self::FIRST_NAME => 'User first name',
            self::LAST_NAME => 'User last name',
            self::NAME => 'Full name of the user',
            self::BIRTHDAY => 'User date of birth',
            self::AVATAR => 'URL or path to the user avatar image',
            self::ROLE => 'User role name',
        };
    }
}
