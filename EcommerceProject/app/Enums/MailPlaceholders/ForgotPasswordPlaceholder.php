<?php

namespace App\Enums\MailPlaceholders;

enum ForgotPasswordPlaceholder: string
{
    case USERNAME = '{{username}}';
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case RESET_LINK = '{{reset_link}}';
    case TOKEN = '{{token}}';
    case EXPIRED_AT = '{{expired_at}}';

    public function description(): string
    {
        return match ($this) {
            self::USERNAME => 'Username of the account',
            self::FIRST_NAME => 'User first name',
            self::LAST_NAME => 'User last name',
            self::NAME => 'Full name of the user',
            self::RESET_LINK => 'Password reset link',
            self::TOKEN => 'Password reset token',
            self::EXPIRED_AT => 'Token expiration time',
        };
    }
}
