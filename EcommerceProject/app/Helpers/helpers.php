<?php

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
