<?php

namespace App\Enums\NotificationPlaceholders;

use Illuminate\Mail\Message;
use Illuminate\Support\Arr;

enum InternalSystemPlaceholder: string
{
    case ERROR_TITLE = '{{error_title}}';
    case ERROR_DETAILS = '{{error_details}}';
    case ERROR_TIME = '{{error_time}}';
    case AFFECTED_USERS = '{{affected_users}}';
    case AFFECTED_COUNT = '{{affected_count}}';
    case SEVERITY_LEVEL = '{{severity_level}}';

    public function description(): string
    {
        return match ($this) {
            self::ERROR_TITLE => 'Brief title/summary of the error',
            self::ERROR_DETAILS => 'Detailed description of the error',
            self::ERROR_TIME => 'Timestamp when the error occurred',
            self::AFFECTED_USERS => 'User(s) affected by this error',
            self::AFFECTED_COUNT => 'Number of users affected',
            self::SEVERITY_LEVEL => 'Error severity level (Critical, High, Medium, Low)',
        };
    }

    protected static function resolveValues(object $systemInfo, Message $message): array
    {
        return [
            self::ERROR_TITLE->value => fn() => $systemInfo->title,
            self::ERROR_DETAILS->value => fn() => $systemInfo->details,
            self::ERROR_TIME->value => fn() => $systemInfo->time,
            self::AFFECTED_USERS->value => fn() => $systemInfo->affected_users,
            self::AFFECTED_COUNT->value => fn() => $systemInfo->affected_count,
            self::SEVERITY_LEVEL->value => fn() => $systemInfo->severity_level
        ];
    }

    public static function replacePlaceholders(string $template, object $systemInfo, Message $message, ?array $variable = null): string
    {
        $systemInfo->loadMissing('user', 'shipping', 'items.productVariant.product');
        $placeholders = Arr::only(self::resolveValues($systemInfo, $message), $variable ?? []);

        return str_ireplace(array_keys($placeholders), array_map(fn($resolver) => $resolver(), array_values($placeholders)), $template);
    }
}
