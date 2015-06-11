<?php
$src = 'img/raw/2086d1fb61221b89246cb8a93a8e512b_9785.jpg';
$filebreak = explode('/',$src);
$dst = 'images/'.rand(0,100) . '_' . $filebreak[2];

     if (!empty($src) && !empty($dst)) {
		
        switch (exif_imagetype($src)) {
          case IMAGETYPE_GIF:
            $src_img = imagecreatefromgif($src);
            break;

          case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($src);
            break;

          case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($src);
            break;
        }

        if (!$src_img) {
          echo "Failed to read the image file";
          exit;
        }

        $size = getimagesize($src);
        $size_w = $size[0]; // natural width
        $size_h = $size[1]; // natural height
/*
        $src_img_w = $size_w;
        $src_img_h = $size_h;

		//print_r($size);
        $tmp_img_w = 100;
        $tmp_img_h = 100;
        $dst_img_w = 220;
        $dst_img_h = 220;

        $src_x = 10;
        $src_y = 10;

        if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
          $src_x = $src_w = $dst_x = $dst_w = 0;
        } else if ($src_x <= 0) {
          $dst_x = -$src_x;
          $src_x = 0;
          $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } else if ($src_x <= $src_img_w) {
          $dst_x = 0;
          $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }

        if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
          $src_y = $src_h = $dst_y = $dst_h = 0;
        } else if ($src_y <= 0) {
          $dst_y = -$src_y;
          $src_y = 0;
          $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } else if ($src_y <= $src_img_h) {
          $dst_y = 0;
          $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }

        // Scale to destination position and size
     //   $ratio = $tmp_img_w / $dst_img_w;
    //    $dst_x /= $ratio;
  //      $dst_y /= $ratio;
    //    $dst_w /= $ratio;
     //   $dst_h /= $ratio;*/
	 $dst_x = 264;
	 $dst_y = 142;
	 $dst_w = 421;
	 $dst_h = 315;
	 $dst_img_h = $dst_h;
	 $dst_img_w = $dst_w;
	 $src_x = 0;
	 $src_y = 0;
	 $src_w = $size[0];
	 $src_h = $size[1];
		//echo $ratio;
        $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

        // Add transparent background to destination image
        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagesavealpha($dst_img, true);

        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        if ($result) {
		//	echo 'yes';
          if (!imagepng($dst_img, $dst)) {
            echo "Failed to save the cropped image file";exit;
          }
        } else {
         echo "Failed to crop the image file";exit;
        }

        imagedestroy($src_img);
        imagedestroy($dst_img);
	 }
?>