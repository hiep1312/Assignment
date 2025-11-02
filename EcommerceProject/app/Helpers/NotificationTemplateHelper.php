<?php

namespace App\Helpers;

use App\Enums\NotificationPlaceholders\{AccountUpdatePlaceholder, CustomPlaceholder, OrderUpdatePlaceholder, PaymentUpdatePlaceholder, PromotionPlaceholder, SystemPlaceholder};

class NotificationTemplateHelper
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
            0 => CustomPlaceholder::class,
            1 => OrderUpdatePlaceholder::class,
            2 => PaymentUpdatePlaceholder::class,
            3 => PromotionPlaceholder::class,
            4 => AccountUpdatePlaceholder::class,
            5 => SystemPlaceholder::class,
        };
    }
}
