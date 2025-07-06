<?php
namespace SrvKit\Auth\Helpers;

class AvatarHelper
{
	
	/**
	 * It generates avatar from string's first letter
	 * @param  string      $name   [description]
	 * @param  int|integer $width  [description]
	 * @param  int|integer $height [description]
	 * @return string              Binary data of the image
	 */
	public static function generateAvatar(string $name, int $width = 100, int $height = 100): ?string
	{
	    $image = imagecreatetruecolor($width, $height);

	    // Set background color (randomized for variety)
	    $bgColors = [
	    	[0, 122, 255],
	    	[52, 170, 220],
	    	[76, 217, 100],
	    	[255, 45, 85],
	    	[255, 59, 48],
	    	[255, 149, 0],
	    	[255, 204, 0],
	    	[142, 142, 147],
	        [156, 39, 176],
	    ];
	    $selectedColor = $bgColors[array_rand($bgColors)];
	    $bgColor = imagecolorallocate($image, $selectedColor[0], $selectedColor[1], $selectedColor[2]);
	    imagefill($image, 0, 0, $bgColor);

	    $textColor = imagecolorallocate($image, 255, 255, 255);

	    $initials = '';
	    $words = explode(' ', $name);
	    foreach ($words as $word) {
	        $initials .= strtoupper(mb_substr($word, 0, 1));
	    }

	    // Set font size and path
	    $fontSize = $width / 3; // Dynamic font size based on width
	    $fontPath = FCPATH . 'fonts/libre-baskerville/LibreBaskerville-Bold.ttf';

	    if (!file_exists($fontPath)) {
	        log_message('error', 'Font file is missing at: ' . $fontPath);
	        imagedestroy($image);
	        return null;
	    }

	    // Calculate text bounding box
	    $bbox = imagettfbbox($fontSize, 0, $fontPath, $initials);
	    $textWidth = $bbox[2] - $bbox[0];
	    $textHeight = $bbox[1] - $bbox[7];

	    // Center the text
	    $x = ($width - $textWidth) / 2;
	    $y = ($height + $textHeight) / 2;

	    // Add the text to the image
	    imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $initials);

	    // Capture binary output in a buffer
	    ob_start();
	    imagepng($image);
	    $binaryData = ob_get_clean();

	    // Clean up
	    imagedestroy($image);

	    // Return the binary data for MySQL BLOB storage
	    return $binaryData ?: null;
	}
}