<?php

	/*
	 * CAPTCHA
	 * Keep it simple, stupid :)
	 * albert@tigr.net
	 */

	function zcaptcha_create($width='120',$height='40',$characters='5',$font = 'monofont.ttf') {
		$font = dirname(__FILE__) . '/' . $font;
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		/* font size will be 75% of the image height */
		$font_size = $height * 0.70;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 220, 220, 220);
		$text_color = imagecolorallocate($image, 10, 30, 80);
		$noise_color = imagecolorallocate($image, 150, 180, 220);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		$y -= 5;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code) or die('Error in imagettftext function');
		// add ellipses
		//imageellipse($image,rand(1,$width),rand(1,$height),rand(50,$width*2),rand(12,$height/2),$text_color);
		//for($i=1; $i<=4;$i++) {
		//	imageellipse($image,rand(1,$width),rand(1,$height),rand(50,$width*2),rand(12,$height),$background_color);
		//}

		// prevent client side caching
		header('Expires: Wed, 1 Jan 1997 00:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header('Content-Type: image/jpeg');
		imagejpeg($image, NULL, 25);
		imagedestroy($image);
		
		return $code;
	}
?>
