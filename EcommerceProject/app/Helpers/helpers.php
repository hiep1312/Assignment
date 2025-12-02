<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Payload;

if(!function_exists('getFileName')){
    /**
     * Generate a safe and unique filename for an uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $file The uploaded file instance.
     *
     * @return string The generated unique filename.
     */
    function getFileName(UploadedFile $file): string
    {
        $originalName = $file->getClientOriginalName();
        $originalExtension = $file->getClientOriginalExtension();
        $baseName = basename($originalName, '.' . $originalExtension);
        $filename = Str::limit($baseName, 80, '') . uniqid() . '.' . $file->extension();

        return $filename;
    }
}

if(!function_exists('storeImage')){
    /**
     * Store an uploaded image file to the specified disk and folder.
     *
     * @param mixed $image The uploaded file instance (should be UploadedFile)
     * @param string $folder The folder path where the image will be stored (default: 'images')
     * @param string|null $filename Optional custom filename for the stored image
     * @param string $disk The storage disk to use (default: 'public')
     * @return string|null Returns the stored file path on success, null if image is not an UploadedFile
     */
    function storeImage(mixed $image, string $folder = 'images', ?string $filename = null, string $disk = 'public'): ?string
    {
        if($image instanceof UploadedFile){
            return $filename ? $image->storeAs($folder, $filename, $disk) : $image->store($folder, $disk);
        }

        return null;
    }
}

if(!function_exists('updateImage')){
    /**
     * Update an existing image by deleting the old one and storing the new one.
     *
     * If no new image is provided, returns the old image path.
     * If a new image is uploaded and an old image exists, deletes the old image before storing the new one.
     *
     * @param mixed $image The new uploaded file instance (should be UploadedFile) or null
     * @param string|null $oldImage The path of the existing image to be replaced
     * @param string $folder The folder path where the new image will be stored (default: 'images')
     * @param string|null $filename Optional custom filename for the stored image
     * @param string $disk The storage disk to use (default: 'public')
     * @return string|null Returns the new file path if updated, old path if no update, or null if storage fails
     */
    function updateImage(mixed $image, ?string $oldImage = null, string $folder = 'images', ?string $filename = null, string $disk = 'public'): ?string
    {
        $isImage = $image instanceof UploadedFile;
        if(is_null($oldImage) || $isImage){
            if($isImage && $oldImage) deleteImage($oldImage, $disk);

            return storeImage($image, $folder, $filename, $disk);
        }

        return $oldImage;
    }
}

if(!function_exists('deleteImage')){
    /**
     * Delete an image file from the specified storage disk.
     *
     * Checks if the image exists before attempting to delete it.
     * Returns true only if the image exists and is successfully deleted.
     *
     * @param string $imagePath The path of the image file to be deleted
     * @param string $disk The storage disk where the image is stored (default: 'public')
     * @return bool Returns true if image exists and was successfully deleted, false otherwise
     */
    function deleteImage(?string $imagePath, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($imagePath) && Storage::disk($disk)->delete($imagePath);
    }
}

if(!function_exists('formatFileSize')){
    /**
     * Format bytes into a human-readable file size string.
     *
     * Converts a byte value into an appropriate unit (TB, GB, MB, KB, or B)
     * with two decimal places for easier reading.
     *
     * @param int $bytes The file size in bytes
     * @return string The formatted file size (e.g., "2.50 MB", "1.25 GB")
     */
    function formatFileSize(int $bytes): string
    {
        $units = [
            1099511627776 => 'TB',
            1073741824 => 'GB',
            1048576 => 'MB',
            1024 => 'KB',
        ];

        foreach ($units as $threshold => $unit) {
            if ($bytes >= $threshold || $unit === 'KB') {
                return number_format($bytes / $threshold, 2) . ' ' . $unit;
            }
        }

        return $bytes . ' B';
    }
}

if(!function_exists('formatJsonToHtml')){
    /**
     * Converts JSON data into formatted, syntax-highlighted HTML.
     *
     * Takes JSON input (either as a string or array/object) and transforms it into
     * an HTML representation with CSS classes for syntax highlighting. Each JSON
     * element type (string, number, boolean, null) receives a specific CSS class
     * for styling purposes.
     *
     * @param mixed $json The JSON data to format. Can be:
     *                    - A JSON string to be decoded
     *                    - An array or object (already decoded JSON)
     *                    - null or empty string (returns null)
     * @param int $indentCount The number of spaces to use for each indentation level. Defaults to 4 spaces.
     * @return string|null Returns the formatted HTML string with the following CSS classes:
     *                     - 'json-key': for object keys
     *                     - 'json-string': for string values
     *                     - 'json-number': for numeric values
     *                     - 'json-boolean': for boolean values
     *                     - 'json-null': for null values
     *                     - 'json-error': for invalid JSON input
     *                     Returns null if input is null or empty string.
     */
    function formatJsonToHtml(mixed $json, int $indentCount = 4): string|null
    {
        if(is_null($json) || $json === '') return null;

        $data = is_string($json) ? json_decode($json, true, 512, JSON_OBJECT_AS_ARRAY) : $json;

        if(json_last_error() !== JSON_ERROR_NONE) {
            return '<span class="json-error">Invalid JSON</span>';
        }

        $tagName = 'span';
        $keyClassName = 'json-key';

        $getClassName = function($value){
            return match(true){
                is_string($value) => 'json-string',
                is_numeric($value) => 'json-number',
                is_bool($value) => 'json-boolean',
                is_null($value) => 'json-null',
                default => 'json-error',
            };
        };

        $getDisplayValue = function($value){
            return match(true){
                is_string($value) => '"'. htmlspecialchars($value) .'"',
                is_numeric($value) => htmlspecialchars($value),
                is_bool($value) => $value ? 'true' : 'false',
                is_null($value) => 'null',
            };
        };

        $formatValue = function (mixed $value, int $level = 0, int $indentCount = 4) use (&$formatValue, $tagName, $keyClassName, $getClassName, $getDisplayValue){
            $currentIndent = str_repeat("&nbsp;", $indentCount * $level);
            $nextIndent = str_repeat("&nbsp;", $indentCount * ($level + 1));

            if(is_string($value) || is_numeric($value) || is_bool($value) || is_null($value)){
                return "<{$tagName} class=\"{$getClassName($value)}\">{$getDisplayValue($value)}</{$tagName}>";
            }elseif(is_array($value) || is_object($value)){
                $value = (array) $value;
                $isAssociative = !array_is_list($value);

                $html = $isAssociative ? "{\n" : "[\n";
                $elements = [];

                foreach($value as $key => $item){
                    $formattedValue = $formatValue($item, $level + 1, $indentCount);

                    if($isAssociative){
                        $elements[] = "{$nextIndent}<{$tagName} class=\"{$keyClassName}\">\"{$key}\"</{$tagName}>: {$formattedValue}";
                    }else{
                        $elements[] = "{$nextIndent}{$formattedValue}";
                    }
                }

                $html .= implode(",\n", $elements) . "\n";
                $html .= $currentIndent . ($isAssociative ? "}" : "]");
                return $html;
            }

            return htmlspecialchars((string)$value);
        };

        return $formatValue($data, 0, $indentCount);
    }
}

if(!function_exists('formatNumberCompact')){
    /**
     * Format a number into a compact, human-readable string with unit suffixes.
     *
     * @param int|float $number The number to format. Can be an integer or floating-point number.
     *
     * @return string The formatted number with appropriate unit suffix.
     *                Returns the number with one decimal place (if needed) followed by:
     *                - '' (empty) for numbers < 1,000
     *                - 'K' for thousands (1,000 - 999,999)
     *                - 'M' for millions (1,000,000 - 999,999,999)
     *                - 'B' for billions (1,000,000,000 - 999,999,999,999)
     *                - 'T' for trillions (1,000,000,000,000+)
     */
    function formatNumberCompact(int|float $number): string
    {
        $units = ['', 'K', 'M', 'B', 'T'];
        $unitIndex = 0;

        while($number >= 1000 && $unitIndex < count($units) - 1) {
            $number /= 1000;
            $unitIndex++;
        }

        return rtrim(rtrim(number_format($number, 1), '0'), '.') . $units[$unitIndex];
    }
}

if(!function_exists('authPayload')){
    /**
     * Retrieve a value from the JWT payload of the currently authenticated user.
     *
     * @param string|null $key The payload key to retrieve. If null, the full payload is returned.
     * @param mixed|null $default The default value to return when the key does not exist. Default is null.
     * @param bool $throw Whether to throw an exception on JWT errors. If false, returns $default instead. Default is true.
     *
     * @return \Tymon\JWTAuth\Payload|mixed The payload value associated with the key, the full Payload object if $key is null, or $default on error when $throw is false.
     *
     * @throws \Tymon\JWTAuth\Exceptions\JWTException When token parsing fails and $throw is true.
     */
    function authPayload($key = null, $default = null, $throw = true)
    {
        try {
            $payload = JWTAuth::parseToken()->payload();

            return $payload->get($key) ?? $default;
        }catch (JWTException $error) {
            if($throw) throw $error;
            return $default;
        }
    }
}
