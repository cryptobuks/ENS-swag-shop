<?php

function calculateTextBox($font_size, $font_angle, $font_file, $text)
{
    $box = imagettfbbox($font_size, $font_angle, $font_file, $text);
    if (!$box)
        return false;
    $min_x = min(array($box[0], $box[2], $box[4], $box[6]));
    $max_x = max(array($box[0], $box[2], $box[4], $box[6]));
    $min_y = min(array($box[1], $box[3], $box[5], $box[7]));
    $max_y = max(array($box[1], $box[3], $box[5], $box[7]));
    $width = ($max_x - $min_x);
    $height = ($max_y - $min_y);
    $left = abs($min_x) + $width;
    $top = abs($min_y) + $height;
    // to calculate the exact bounding box i write the text in a large image
    $img = @imagecreatetruecolor($width << 2, $height << 2);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black);
    // for sure the text is completely in the image!
    imagettftext($img, $font_size,
        $font_angle, $left, $top,
        $white, $font_file, $text);
    // start scanning (0=> black => empty)
    $rleft = $w4 = $width << 2;
    $rright = 0;
    $rbottom = 0;
    $rtop = $h4 = $height << 2;
    for ($x = 0; $x < $w4; $x++)
        for ($y = 0; $y < $h4; $y++)
            if (imagecolorat($img, $x, $y)) {
                $rleft = min($rleft, $x);
                $rright = max($rright, $x);
                $rtop = min($rtop, $y);
                $rbottom = max($rbottom, $y);
            }
    // destroy img and serve the result
    imagedestroy($img);
    return array("left" => $left - $rleft,
        "top" => $top - $rtop,
        "width" => $rright - $rleft + 1,
        "height" => $rbottom - $rtop + 1);
}

$string = (isset($_GET['name'])) ? filter_var($_GET['name']) : 'eth.eth';
$width = (isset($_GET['width'])) ? (int)filter_var($_GET['width']) : 1000;
$height = (isset($_GET['height'])) ? (int)filter_var($_GET['height']) : 300;

$stringLength = strlen($string);
$font = './OpenSans-Bold.ttf';
$fontSize = 120 * (11/$stringLength);

// check if name has "&" mark
if($stringLength == 1 || str_contains($_SERVER['QUERY_STRING'], '&')){
    $serverName = $_SERVER['QUERY_STRING'];
    $serverName = str_replace('name=', '', $serverName);
    $string = $serverName;
    $stringLength = strlen($string);
    $fontSize = 120 * (11/$stringLength);
}

$im = imagecreatetruecolor($width, $height);

imagesavealpha($im, true);
imagealphablending($im, false);

# important part two
$whiteBG = imagecolorallocatealpha($im, 255, 255, 255, 127);
imagefill($im, 0, 0, $whiteBG);

$white = imagecolorallocate($im, 255, 255, 255);

// Create the next bounding box for the second text
$dataBOX = calculateTextBox($fontSize, 0, $font, $string);

$x = (imagesx($im) - $dataBOX['width']) / 2;
$y = (imagesy($im) + 80 - ($dataBOX['height'] - $dataBOX['top'])) / 2;

// Write it
imagettftext($im, $fontSize, 0, $x, $y, $white, $font, $string);

header("Content-type: image/png");
imagepng($im);
imagedestroy($im);