<?php

namespace App\Enums\NotificationPlaceholders;

enum PromotionPlaceholder: string
{
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case EMAIL = '{{email}}';
    case BIRTHDAY = '{{birthday}}';
    case AVATAR = '{{avatar}}';

    public function description(): string
    {
        return match ($this) {
            self::FIRST_NAME => 'User first name',
            self::LAST_NAME => 'User last name',
            self::NAME => 'User full name',
            self::EMAIL => 'User email address',
            self::BIRTHDAY => 'User date of birth',
            self::AVATAR => 'URL or path to the user avatar image',
        };
    }
}
