<?php

namespace App\Helpers;

use App\Enums\DefaultImage;
use App\Enums\MailPlaceholders\{CustomMailPlaceholder, OrderSuccessPlaceholder, OrderFailedPlaceholder, ShippingUpdatePlaceholder, ForgotPasswordPlaceholder, RegisterSuccessPlaceholder};
use App\Models\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Cache;
use Pelago\Emogrifier\CssInliner;
use Pelago\Emogrifier\HtmlProcessor\CssVariableEvaluator;

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

    public static function applyInlineCss(string $content): string
    {
        Cache::remember('ckeditor5_content_css', 60 * 60 * 3, function(){
            return file_get_contents(
                filename: base_path("node_modules\ckeditor5\dist\ckeditor5-content.css"),
                use_include_path: false,
                offset: 184
            );
        });

        $inlinedHtml = CssInliner::fromHtml("<div class=\"ck-content\">{$content}</div>")->inlineCss(Cache::get('ckeditor5_content_css'))->render();
        return CssVariableEvaluator::fromHtml($inlinedHtml)->evaluateVariables()->renderBodyContent();
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
