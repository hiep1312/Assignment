<?php

namespace App\Enums\MailPlaceholders;

enum RegisterSuccessPlaceholder: string
{
    case USERNAME = '{{username}}';
    case EMAIL = '{{email}}';
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case VERIFICATION_LINK = '{{verification_link}}';

    public function description(): string
    {
        return match ($this) {
            self::USERNAME => 'Username of the registered account',
            self::EMAIL => 'Registered email address',
            self::FIRST_NAME => 'User first name',
            self::LAST_NAME => 'User last name',
            self::NAME => 'Full name of the user',
            self::VERIFICATION_LINK => 'Email verification link',
        };
    }
}
