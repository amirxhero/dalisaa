<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Image;
use Spatie\MediaLibrary\HasMedia;

class WebpImageService
{
    /**
     * Convert an uploaded image and add the WebP file to a media collection.
     */
    public function addToMediaCollection(HasMedia $model, UploadedFile $upload, string $collection): void
    {
        $convertedPath = $this->convert($upload);

        try {
            $model->addMedia($convertedPath)
                ->usingFileName($this->webpFileName($upload))
                ->toMediaCollection($collection);
        } finally {
            $this->deleteTemporaryFile($convertedPath);
        }
    }

    /**
     * Convert an uploaded image and store the WebP file on the public disk.
     */
    public function storePublic(UploadedFile $upload, string $directory): string
    {
        $convertedPath = $this->convert($upload);
        $fileName = $this->webpFileName($upload);

        try {
            Storage::disk('public')->putFileAs($directory, $convertedPath, $fileName);

            return trim($directory, '/').'/'.$fileName;
        } finally {
            $this->deleteTemporaryFile($convertedPath);
        }
    }

    private function convert(UploadedFile $upload): string
    {
        $directory = storage_path('app/temp/webp');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory.'/'.Str::uuid().'.webp';

        Image::load($upload->getRealPath())
            ->format('webp')
            ->quality(85)
            ->save($path);

        return $path;
    }

    private function webpFileName(UploadedFile $upload): string
    {
        $baseName = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
        $baseName = preg_replace('/[^\pL\pN._-]+/u', '-', $baseName) ?: 'image';

        return trim($baseName, '.-_').'.webp';
    }

    private function deleteTemporaryFile(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
