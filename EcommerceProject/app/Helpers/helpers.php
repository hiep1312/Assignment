<?php

use App\Enums\MailPlaceholders\CustomMailPlaceholder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
