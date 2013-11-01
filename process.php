<?php

if( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
    die("Access denied");
}

$images_path = "creatures/";

if(isset($_POST['data']) && isset($_POST['filename']) && isset($_POST['filter']) && isset($_POST['headline'])){

	$image_data = $_POST['data'];

	$image_src = str_replace("data:image/jpeg;base64,", "", $image_data);
	$image_src = base64_decode($image_src);

	$filename = $_POST['filename'];
	$filter = $_POST['filter'];
	$headline = $_POST['headline'];

	

	if( @file_put_contents( $images_path . $filename, $image_src)) {

	    // echo json_encode( array("true") );

	    $url = gd_filter_image($images_path . $filename, $filter, $headline);

	    $url = "http://" . $_SERVER['HTTP_HOST'] . "/labs-photobooth-filters/" . $url;

	    echo json_encode(array('url'=>$url));
	} else {

	    echo json_encode( array("false") );

	}

} else {
	die("Missing params");
}


/** Apply and deliver the image and clean up */
function gd_filter_image($image_path, $filter_name, $headline)
{
	$filter = 'gd_filter_' . $filter_name;
	if (function_exists($filter)) {
		list($width, $height) = getimagesize($image_path);
		
		$im = imagecreatetruecolor($width, $height);
		$src = imagecreatefromjpeg($image_path);
		imagecopyresampled($im, $src, 0, 0, 0, 0, $width, $height, $width, $height);
		
		$im = $filter($im);

		
		if($headline == 'undefined'){
			$name_container_image   = 'booth/booth.png';			
		} else {
			$name_container_image   = 'booth/booth'.$headline.'.png';			
		}
		$name_merged_image      = $image_path;

		$base_image             = $im;
		$container_image        = imagecreatefrompng( $name_container_image );
		list( $width, $height ) = getimagesize( $name_container_image );

		imagesavealpha( $container_image, TRUE );
		imagealphablending( $container_image, TRUE );

		// header( 'Content-Type: image/png' );
		imagecopy( $base_image, $container_image, 0, 0, 0, 0, $width, $height );
		// imagepng( $base_image );
		imagepng( $base_image, $name_merged_image );

		return $name_merged_image;
		// header('Content-type: image/jpeg');
		// imagejpeg($im, $image_path, 100);
		// imagedestroy($im);
		// imagedestroy($src);
			

	}
}

/** Apply 'Dreamy' preset */
function gd_filter_dreamy($im)
{
	imagefilter($im, IMG_FILTER_BRIGHTNESS, 20);
	imagefilter($im, IMG_FILTER_CONTRAST, -35);
	imagefilter($im, IMG_FILTER_COLORIZE, 60, -10, 35);
	imagefilter($im, IMG_FILTER_SMOOTH, 7);
	$im = gd_apply_overlay($im, 'scratch', 10);
	$im = gd_apply_overlay($im, 'vignette', 100);
	return $im;
}

/** Apply 'Blue Velvet' preset */
function gd_filter_velvet($im)
{
	imagefilter($im, IMG_FILTER_BRIGHTNESS, 5);
	imagefilter($im, IMG_FILTER_CONTRAST, -25);
	imagefilter($im, IMG_FILTER_COLORIZE, -10, 45, 65);
	$im = gd_apply_overlay($im, 'noise', 45);
	$im = gd_apply_overlay($im, 'vignette', 100);
	return $im;
}

/** Apply 'Chrome' preset */
function gd_filter_chrome($im)
{
	imagefilter($im, IMG_FILTER_BRIGHTNESS, 15);
	imagefilter($im, IMG_FILTER_CONTRAST, -15);
	imagefilter($im, IMG_FILTER_COLORIZE, -5, -10, -15);
	$im = gd_apply_overlay($im, 'noise', 45);
	$im = gd_apply_overlay($im, 'vignette', 100);
	return $im;
}

/** Apply 'Lift' preset */
function gd_filter_lift($im)
{
	imagefilter($im, IMG_FILTER_BRIGHTNESS, 50);
	imagefilter($im, IMG_FILTER_CONTRAST, -25);
	imagefilter($im, IMG_FILTER_COLORIZE, 75, 0, 25);
	$im = gd_apply_overlay($im, 'emulsion', 100);
	return $im;
}

/** Apply 'Canvas' preset */
function gd_filter_canvas($im)
{
	imagefilter($im, IMG_FILTER_BRIGHTNESS, 25);
	imagefilter($im, IMG_FILTER_CONTRAST, -25);
	imagefilter($im, IMG_FILTER_COLORIZE, 50, 25, -35);
	$im = gd_apply_overlay($im, 'canvas', 100);
	return $im;
}

/** Apply 'Vintage 600' preset */
function gd_filter_vintage($im)
{
	imagefilter($im, IMG_FILTER_BRIGHTNESS, 15);
	imagefilter($im, IMG_FILTER_CONTRAST, -25);
	imagefilter($im, IMG_FILTER_COLORIZE, -10, -5, -15);
	imagefilter($im, IMG_FILTER_SMOOTH, 7);
	$im = gd_apply_overlay($im, 'scratch', 7);
	return $im;
}

/** Apply 'Monopin' preset */
function gd_filter_monopin($im)
{
	imagefilter($im, IMG_FILTER_GRAYSCALE);
	imagefilter($im, IMG_FILTER_BRIGHTNESS, -15);
	imagefilter($im, IMG_FILTER_CONTRAST, -15);
	$im = gd_apply_overlay($im, 'vignette', 100);
	return $im;
}

/** Apply 'Antique' preset */
function gd_filter_antique($im)
{
	imagefilter($im, IMG_FILTER_BRIGHTNESS, 0);
	imagefilter($im, IMG_FILTER_CONTRAST, -30);
	imagefilter($im, IMG_FILTER_COLORIZE, 75, 50, 25);
	return $im;
}

/** Apply 'Black & White' preset */
function gd_filter_blackwhite($im)
{
	imagefilter($im, IMG_FILTER_GRAYSCALE);
	imagefilter($im, IMG_FILTER_BRIGHTNESS, 10);
	imagefilter($im, IMG_FILTER_CONTRAST, -20);
	return $im;
}

/** Apply 'Colour Boost' preset */
function gd_filter_boost($im)
{
	imagefilter($im, IMG_FILTER_CONTRAST, -35);
	imagefilter($im, IMG_FILTER_COLORIZE, 25, 25, 25);
	return $im;
}

/** Apply 'Sepia' preset */
function gd_filter_sepia($im)
{
	imagefilter($im, IMG_FILTER_GRAYSCALE);
	imagefilter($im, IMG_FILTER_BRIGHTNESS, -10);
	imagefilter($im, IMG_FILTER_CONTRAST, -20);
	imagefilter($im, IMG_FILTER_COLORIZE, 60, 30, -15);
	return $im;
}

/** Apply 'Partial blur' preset */
function gd_filter_blur($im)
{
	imagefilter($im, IMG_FILTER_SELECTIVE_BLUR);
	imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
	imagefilter($im, IMG_FILTER_CONTRAST, -15);
	imagefilter($im, IMG_FILTER_SMOOTH, -2);
	return $im;
}

/** Apply a PNG overlay */
function gd_apply_overlay($im, $type, $amount)
{
	$width = imagesx($im);
	$height = imagesy($im);
	$filter = imagecreatetruecolor($width, $height);
	
	imagealphablending($filter, false);
	imagesavealpha($filter, true);
	
	$transparent = imagecolorallocatealpha($filter, 255, 255, 255, 127);
	imagefilledrectangle($filter, 0, 0, $width, $height, $transparent);
	
	$overlay = 'filters/' . $type . '.png';
	$png = imagecreatefrompng($overlay);
	imagecopyresampled($filter, $png, 0, 0, 0, 0, $width, $height, $width, $height);
	
	$comp = imagecreatetruecolor($width, $height);
	imagecopy($comp, $im, 0, 0, 0, 0, $width, $height);
	imagecopy($comp, $filter, 0, 0, 0, 0, $width, $height);
	imagecopymerge($im, $comp, 0, 0, 0, 0, $width, $height, $amount);
	
	imagedestroy($comp);
	return $im;
}