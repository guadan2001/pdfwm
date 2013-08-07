<?php
// File and rotation


$src_file = 'jpg-test.jpg';
$degrees = 45;
$src_info = getimagesize($src_file);

switch($src_info[2])
{
	case 1:
	$source = imagecreatefromgif($src_file);
	break;
	case 2:
	$source = imagecreatefromjpeg($src_file);
	break;
	case 3:
	$source = imagecreatefrompng($src_file);
	break;
	default:
	die("Unsupported Image Format");
}


// Load
// Rotate
$rotate = imagerotate($source, $degrees,  imageColorAllocateAlpha($source, 255, 255, 255, 127));
imagealphablending($rotate, false);
imagesavealpha($rotate, true);
//$black = imagecolorallocate($rotate, 0, 0, 0);
//imagecolortransparent($rotate, $black);

// Output
imagepng($rotate,"rotate.png");

$TestLayer = ImageWorkShop::initFromPath("rotate.png");

echo $TestLayer->opacity(70);

$TestLayer->save('./','TEST1.png');

//$newOpacity = 50;

//$layer->opacity($newOpacity);

// Free the memory
imagedestroy($source);
imagedestroy($rotate);
?>