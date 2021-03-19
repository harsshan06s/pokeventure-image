<?php
if (!isset($_GET['d'])) {
    http_response_code(200);
    echo "Hello trainer!";
    exit();
}
$day = $_GET['d'];
// include composer autoload
require 'vendor/autoload.php';

use Imagine\Gd\Font;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

$imagine = new Imagine\Gd\Imagine();

$canvasWidth = 156 * 3;
$canvasHeight = 141 * 3;

$floorWidth = 128;
$floorHeight = 32;

$spriteWidth = 92;
$spriteHeight = 92;

$palette = new RGB();
$image = $imagine->create(new Box($canvasWidth, $canvasHeight), $palette->color('#000', 0));

$stamp = $imagine->open('./img/stamp.png')->resize(new Box(32, 32));

$bg = $imagine->open('./img/streak.png');
$bg->resize(new Box(156 * 3, 141 * 3));

$image->paste($bg, new Point(0, 0));

for ($i = 0; $i < 32; $i++) {
    if ($day >= ($i + 1)) {
        $image->paste($stamp, new Point(25 + ($i % 8) * 55, 130 + floor($i / 8) * 77));
    }
}

$image
    ->show('png');
