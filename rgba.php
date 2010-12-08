<?php
/***************************************************************************************
 * Script for automatic generation of one pixel
 * alpha-transparent images for non-RGBA browsers.
 * @author Lea Verou
 * @version 1.1.1
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 ***************************************************************************************/

######## SETTINGS ##############################################################

/**
 * Enter the directory in which to store cached color images.
 * This should be relative and SHOULD contain a trailing slash.
 * The directory you specify should exist and be writeable (usually chmoded to 777).
 * If you want to store the pngs at the same directory, leave blank ('').
 */
define('COLORDIR', 'colors/');


/**
 * If you frequently use a color with varying alphas, you can name it
 * below, to save you some typing and make your CSS easier to read.
 */
$color_names = array(
	'white' => array(255, 255, 255),
	'black' => array(0, 0, 0)
	// , 'mycolor' => array(red, green, blue)
);

/**
 * If you want the generated image to have a greater size than 1x1, you may adjust the following.
 * Apart from debugging purposes (it's easier to see if the image has a problem when it's something
 * larger than 1x1), some people argue that the browser needs to spend substantially more resources
 * to render the background when the image is small.
 */
define('SIZE', 10);

/**
 * If you don't want the generated pngs to be cached on the server, set the following to
 * false. This is STRONGLY NOT RECOMMENDED. It's here only for testing/debugging purposes.
 */
define('CACHEPNGS', true);

######## NO FURTHER EDITING, UNLESS YOU REALLY KNOW WHAT YOU'RE DOING ##########

// Only report errors that would stop the script anyway, since the output is an image so even an
// extra byte will prevent it from showing up
error_reporting(E_ERROR | E_PARSE);

$dir = substr(COLORDIR, 0, strlen(COLORDIR) - 1);

if (!is_writable($dir)) {
	die("The directory '$dir' either doesn't exist or isn't writable.");
}

// Are the RGB values provided directly or implied through a named color?
if (isset($color_names[$_REQUEST['name']])) {
	list($red, $green, $blue) = $color_names[$_REQUEST['name']];
}
else {
	$red	= intval($_REQUEST['r']);
	$green	= intval($_REQUEST['g']);
	$blue	= intval($_REQUEST['b']);
}

// "A value between 0 and 127. 0 indicates completely opaque while 127 indicates completely transparent."
// http://www.php.net/manual/en/function.imagecolorallocatealpha.php
$alpha = intval(127 - 127 * ($_REQUEST['a'] / 100));

// Send headers
header('Content-type: image/png');
header('Expires: 01 Jan 2026 00:00:00 GMT');
header('Cache-control: max-age=2903040000');

// Does it already exist?
$filepath = COLORDIR . "color_r{$red}_g{$green}_b{$blue}_a$alpha.png";

if(CACHEPNGS and file_exists($filepath)) {

	// The file exists, is it cached by the browser?
	if (function_exists('apache_request_headers')) {
		$headers = apache_request_headers();

		// We don't need to check if it was actually modified since then as it never changes.
		$responsecode = isset($headers['If-Modified-Since'])? 304 : 200;
	}
	else {
		$responsecode = 200;
	}

	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filepath)) . ' GMT', true, $responsecode);

	if ($responsecode == 200) {
		header('Content-Length: '.filesize($filepath));
		die(file_get_contents($filepath));
	}
}
else {
	$img = @imagecreatetruecolor(SIZE, SIZE)
		  or die('Cannot Initialize new GD image stream');

	// This is to allow the final image to have actual transparency
	// http://www.php.net/manual/en/function.imagesavealpha.php
	imagealphablending($img, false);
	imagesavealpha($img, true);

	// Allocate our requested color
	$color = imagecolorallocatealpha($img, $red, $green, $blue, $alpha);

	// Fill the image with it
	imagefill($img, 0, 0, $color);

	// Save the file (if caching is allowed)
	if (CACHEPNGS) {
		// Check PHP version to solve a bug that caused the script to fail on PHP versions < 5.1.7
		if (strnatcmp(phpversion(), '5.1.7') >= 0) {
			imagepng($img, $filepath, 0, NULL);
		}
		else {
			imagepng($img, $filepath);
		}
	}

	// Serve the file
	imagepng($img);

	// Free up memory
	imagedestroy($img);
}

?>
