<?php

use PHPUnit\Framework\TestCase;
use SrvKit\Auth\Helpers\AvatarHelper;

class AvatarHelperTest extends TestCase
{
    protected function setUp(): void
    {
        // Define FCPATH if not defined
        if (!defined('FCPATH')) {
            define('FCPATH', __DIR__ . '/../../public/'); // adjust path to match your structure
        }

        // Ensure font exists
        $fontDir = FCPATH . 'fonts/libre-baskerville/';
        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0777, true);
        }
        $fontPath = $fontDir . 'LibreBaskerville-Bold.ttf';
        if (!file_exists($fontPath)) {
            // Copy or download the font if needed
            // For this test, you might want to copy a placeholder TTF here
            copy(__DIR__ . '/fake-font.ttf', $fontPath);
        }
    }

    public function testGenerateAvatarReturnsImageData()
    {
        $name = 'John Doe';
        $width = 200;
        $height = 200;

        $imageData = AvatarHelper::generateAvatar($name, $width, $height);

        $this->assertNotEmpty($imageData, 'Avatar binary data should not be empty');
        $this->assertIsString($imageData, 'Avatar data should be a string');

        // Check if it's a valid PNG image
        $image = @imagecreatefromstring($imageData);
        $this->assertNotFalse($image, 'Should be valid image binary');

        // Optional: verify size
        $this->assertEquals($width, imagesx($image));
        $this->assertEquals($height, imagesy($image));

        imagedestroy($image);
    }

    public function testGenerateAvatarHandlesMissingFont()
    {
        // Temporarily rename font to simulate missing font
        $fontPath = FCPATH . 'fonts/libre-baskerville/LibreBaskerville-Bold.ttf';
        $backupPath = $fontPath . '.bak';

        if (file_exists($fontPath)) {
            rename($fontPath, $backupPath);
        }

        $result = AvatarHelper::generateAvatar('Jane Doe');

        $this->assertNull($result, 'Should return null if font is missing');

        // Restore font
        if (file_exists($backupPath)) {
            rename($backupPath, $fontPath);
        }
    }
}
