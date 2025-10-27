<?php

namespace App\Services;

use Parsedown;

class MarkdownService extends Parsedown
{
    protected string $baseImagePath = '';

    public function __construct()
    {
        $this->baseImagePath = url('strorage/images/');

        $this->setBreaksEnabled(true);

        $this->setMarkupEscaped(false);

        $this->setUrlsLinked(true);

        $this->setSafeMode(true);
    }

    protected function inlineImage($excerpt)
    {
        $imageElement = parent::inlineImage($excerpt);

        if(!isset($imageElement)) {
            return null;
        }

        $hasHost = parse_url($imageElement['element']['attributes']['src'], PHP_URL_HOST)
            ?? (bool)preg_match('/^[^\.\/]+\.[^\/]+\//', parse_url($imageElement['element']['attributes']['src'], PHP_URL_PATH) ?? '');

        if(!$hasHost) {
            $imageElement['element']['attributes']['src'] = $this->baseImagePath . $imageElement['element']['attributes']['src'];
        }

        return $imageElement;
    }
}
