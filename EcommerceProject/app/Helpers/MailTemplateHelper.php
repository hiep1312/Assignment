<?php

namespace App\Helpers;

use App\Enums\DefaultImage;
use App\Enums\MailPlaceholders\{CustomMailPlaceholder, OrderSuccessPlaceholder, OrderFailedPlaceholder, ShippingUpdatePlaceholder, ForgotPasswordPlaceholder, RegisterSuccessPlaceholder};
use App\Models\Mail;
use Illuminate\Mail\Message;

class MailTemplateHelper
{
    public static function getPlaceholdersWithDescription(int $type): array
    {
        $enumClass = self::getEnumClass($type);

        return array_map(
            fn($case) => [
                'placeholder' => $case->value,
                'description' => $case->description(),
            ],
            $enumClass::cases()
        );
    }

    public static function getUsedPlaceholders(string $content, int $type): ?array
    {
        $content = mb_strtolower($content, 'UTF-8');
        $enumClass = self::getEnumClass($type);
        $usedPlaceholders = [];

        foreach($enumClass::cases() as $case){
            if(str_contains($content, $case->value)){
                $usedPlaceholders[] = $case->value;
            }
        }

        return $usedPlaceholders ?: null;
    }

    public static function fillPlaceholders(Mail $mail, object $source, Message $message): string
    {
        $enumClass = self::getEnumClass($mail->type);

        return $enumClass::replacePlaceholders(self::convertImagesToEmbedded($mail->body, $message), $source, $message, $mail->variable);
    }

    protected static function convertImagesToEmbedded(string $body, Message $message): string
    {
        $baseUrl = preg_quote(config('app.url'), '/');

        return preg_replace_callback(
            pattern: "/<img([^>]+)src\s*=\s*[\'\"]{$baseUrl}\/?([^\'\"]*)[\'\"]([^>]*)\/?>/i",
            callback: function(array $matches) use ($message){
                $imageCid = $message->embed(public_path($matches[2] ?? DefaultImage::NOT_FOUND->value));

                return "<img{$matches[1]}src=\"{$imageCid}\"{$matches[3]}>";
            },
            subject: $body,
            flags: PREG_UNMATCHED_AS_NULL
        );
    }

    protected static function getEnumClass(int $type): string
    {
        return match($type){
            0 => CustomMailPlaceholder::class,
            1 => OrderSuccessPlaceholder::class,
            2 => OrderFailedPlaceholder::class,
            3 => ShippingUpdatePlaceholder::class,
            4 => ForgotPasswordPlaceholder::class,
            5 => RegisterSuccessPlaceholder::class,
        };
    }
}
