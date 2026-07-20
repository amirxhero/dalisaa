<?php

namespace Tests\Unit;

use App\Services\WebpImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebpImageServiceTest extends TestCase
{
    public function test_uploaded_images_are_stored_as_webp(): void
    {
        Storage::fake('public');

        $source = tempnam(sys_get_temp_dir(), 'webp-source-').'.jpg';
        $canvas = imagecreatetruecolor(8, 8);
        imagejpeg($canvas, $source, 90);
        imagedestroy($canvas);

        try {
            $upload = new UploadedFile($source, 'sample.jpg', 'image/jpeg', null, true);
            $path = app(WebpImageService::class)->storePublic($upload, 'webp-test');

            Storage::disk('public')->assertExists($path);
            $this->assertSame('image/webp', mime_content_type(Storage::disk('public')->path($path)));
            $this->assertStringEndsWith('.webp', $path);
        } finally {
            @unlink($source);
        }
    }
}
