<?php

namespace SrvKit\Auth\Utils\Avatars\Driver;

use SrvKit\Auth\Utils\Avatars\Driver\BaseAvatarDriver;

class GDAvatar extends BaseAvatarDriver
{
	protected int $width = 100;
	
	protected int $height = 100;

	private function getColors(): object
	{
		// Generate a random background color
	    $r = rand(0, 255);
	    $g = rand(0, 255);
	    $b = rand(0, 255);

	    // Calculate brightness using the YIQ formula
	    $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

	    // Predefined text color sets
	        $lightTextColors = [
	            [255, 255, 255],  // Pure white
	            [240, 240, 240],  // Light gray
	            [255, 248, 220],  // Cornsilk
	            [255, 250, 240],  // FloralWhite
	        ];

	        $darkTextColors = [
	            [0, 0, 0],        // Black
	            [30, 30, 30],     // Charcoal
	            [60, 60, 60],     // Dark gray
	            [25, 25, 112],    // MidnightBlue
	        ];

        // Select contrasting text color group
        $textOptions = ($brightness > 150) ? $darkTextColors : $lightTextColors;
        $textColor = $textOptions[array_rand($textOptions)];

	    // Choose text color: white if background is dark, black if background is light
	    // $textColor = ($brightness > 128) ? [0, 0, 0] : [255, 255, 255];

		return (object) array('text' => $textColor, 'background' => [$r, $g, $b]);
	}

	public function create(string $name): ?string
	{
		$width = $this->width;
		$height = $this->height;
		$image = imagecreatetruecolor($width, $height);

		$colors = $this->getColors();
		
		// $backgroundColor = $colors->background;
		$layerBackground = imagecolorallocate($image, ...$colors->background);
		imagefill($image, 0, 0, $layerBackground);

		$textColor = imagecolorallocate($image, ...$colors->text);

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