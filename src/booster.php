<?php
if (empty($_GET['d'])) {
    http_response_code(200);
    echo "Hello trainer!";
    exit();
}
$data = $_GET['d'];
$data = json_decode(base64_decode($data));
// include composer autoload
require 'vendor/autoload.php';

use Imagine\Imagick\Font;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

$imagine = new Imagine\Gd\Imagine();

$canvasWidth = 518;
$canvasHeight = 288;

$floorWidth = 128;
$floorHeight = 32;

$spriteWidth = 92;
$spriteHeight = 92;

$palette = new Imagine\Image\Palette\RGB();
$color = $palette->color('#ff0000', 0);

$card1 = $imagine->open($data[0]);
$card2 = $imagine->open($data[1]);
$card3 = $imagine->open($data[2]);
$card4 = $imagine->open($data[3]);
$card5 = $imagine->open($data[4]);

$image = $imagine->create(new Box(545, 482), $color);

$image->paste($card1, new Point(0, 0));
$image->paste($card2, new Point(75, 35));
$image->paste($card3, new Point(150, 70));
$image->paste($card4, new Point(225, 105));
$image->paste($card5, new Point(300, 140));

$image
    ->show('png', array(
    ));
