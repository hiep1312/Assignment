<?php

namespace App\Enums;

enum DefaultImage: string
{
    case NOT_FOUND = '404.webp';
    case AVATAR = 'avatar-default.webp';
    case BANNER = 'banner-default.webp';
    case PRODUCT = 'product-default.webp';
    case BLOG = 'blog-default.webp';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getDefaultPath(string $type): ?string
    {
        $type = strtoupper($type);
        return defined(self::class . "::" . $type) ? asset("storage/" . constant(self::class . "::" . $type)->value) : null;
    }
}
