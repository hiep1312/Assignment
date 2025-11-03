<?php

namespace App\Enums\MailPlaceholders;

use App\Enums\DefaultImage;
use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Arr;

enum CustomMailPlaceholder: string
{
    case EMAIL = '{{email}}';
    case USERNAME = '{{username}}';
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case BIRTHDAY = '{{birthday}}';
    case AVATAR = '{{avatar}}';
    case ROLE = '{{role}}';
    case CREATED_AT = '{{created_at}}';

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
            self::CREATED_AT => 'Account creation date',
        };
    }

    protected static function resolveValues(User $user, Message $message): array
    {
        return [
            self::EMAIL->value => fn() => $user->email,
            self::USERNAME->value => fn() => $user->username,
            self::FIRST_NAME->value => fn() => $user->first_name,
            self::LAST_NAME->value => fn() => $user->last_name,
            self::NAME->value => fn() => $user->name,
            self::BIRTHDAY->value => fn() => $user->birthday,
            self::AVATAR->value => function() use ($user, $message){
                $avatarCid = $message->embed(public_path("storage/" . ($user->avatar ?? DefaultImage::AVATAR->value)));
                return <<<HTML
                    <div style="flex-shrink: 0;">
                        <img src="{$avatarCid}" alt="" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 1px solid #667eea;">
                    </div>
                HTML;
            },
            self::ROLE->value => fn() => ucfirst($user->role->value),
            self::CREATED_AT->value => fn() => $user->created_at->format('m/d/Y H:i A'),
        ];
    }

    public static function replacePlaceholders(string $template, User $user, Message $message, ?array $variable = null): string
    {
        $placeholders = Arr::only(self::resolveValues($user, $message), $variable ?? []);

        return str_ireplace(array_keys($placeholders), array_map(fn($resolver) => $resolver(), array_values($placeholders)), $template);
    }
}
