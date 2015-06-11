<?php
//print_r($_POST['src']);exit;
$dst_x = 0;   // X-coordinate of destination point
$dst_y = 0;   // Y-coordinate of destination point
$src_x = $_POST['x']; // Crop Start X position in original image
$src_y = $_POST['y']; // Crop Srart Y position in original image
$dst_w = $_POST['w']; // Thumb width
$dst_h = $_POST['h']; // Thumb height
$src_w = $src_x+$dst_w; // $src_x + $dst_w Crop end X position in original image
$src_h = $src_y+$dst_h; // $src_y + $dst_h Crop end Y position in original image


$file = ltrim($_POST['src'],'/');
$filebreak = explode('/',$file);
$dst_loc = 'images/'.rand(0,10) . '_' . $filebreak[2];
// Creating an image with true colors having thumb dimensions (to merge with the original image)
$dst_image = imagecreatetruecolor($dst_w, $dst_h);
// Get original image
switch (exif_imagetype($file)) {
  case IMAGETYPE_GIF:
	$src_image = imagecreatefromgif($file);
	break;

  case IMAGETYPE_JPEG:
	$src_image = imagecreatefromjpeg($file);
	break;

  case IMAGETYPE_PNG:
	$src_image = imagecreatefrompng($file);
	break;
}

// Cropping
imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
 // Add transparent background to destination image
        imagefill($dst_image, 0, 0, imagecolorallocatealpha($dst_image, 0, 0, 0, 127));
        imagesavealpha($dst_image, true);
// Saving

switch (exif_imagetype($file)) {
  case IMAGETYPE_GIF:
	//header('Content-Type: image/gif');
	imagegif($dst_image, $dst_loc);
	break;

  case IMAGETYPE_JPEG:
	//header('Content-Type: image/jpeg');
	imagejpeg($dst_image, $dst_loc);
	break;

  case IMAGETYPE_PNG:
	//header('Content-Type: image/png');
	imagepng($dst_image, $dst_loc);
	break;
}

echo '<img src="'.$dst_loc.'" alt="" />';

?>