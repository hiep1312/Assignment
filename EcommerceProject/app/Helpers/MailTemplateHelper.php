<?php

namespace App\Helpers;

use App\Enums\MailPlaceholders\{CustomMailPlaceholder, OrderSuccessPlaceholder, OrderFailedPlaceholder, ShippingUpdatePlaceholder, ForgotPasswordPlaceholder, RegisterSuccessPlaceholder};

class MailTemplateHelper
{
    public static function getMailPlaceholdersWithDescription(int $type): array
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

    public static function getUsedPlaceholders(string $content, int $type): array
    {
        $enumClass = self::getEnumClass($type);
        $usedPlaceholders = [];

        foreach($enumClass::cases() as $case){
            if(str_contains($content, $case->value)){
                $usedPlaceholders[] = $case->value;
            }
        }

        return $usedPlaceholders;
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
