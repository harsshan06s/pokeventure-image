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

$egg = $imagine->open('./img/egg.png')->resize(new Box(32, 32));
$pass = $imagine->open('./img/pass.png')->resize(new Box(32, 32));
$candy = $imagine->open('./img/candy.png')->resize(new Box(32, 32));
$stamp = $imagine->open('./img/stamp.png')->resize(new Box(32, 32));


function item($original, $itemId)
{
    $copy = $original->copy();
    $copy->crop(new Point(24 * ($itemId % 16), (floor($itemId / 16)) * 24), new Box(24, 24));
    $copy->resize(new Box(32, 32));
    return $copy;
}
$items = $imagine->open('./img/itemicons-sheet.png');

$dayItem = [
    item($items, 103),
    item($items, 103),
    item($items, 103),
    item($items, 103),
    $egg,
    item($items, 103),
    item($items, 103),
    item($items, 103),
    item($items, 103),
    $pass,
    item($items, 442),
    item($items, 442),
    item($items, 442),
    item($items, 442),
    $candy,
    item($items, 442),
    item($items, 442),
    item($items, 442),
    item($items, 442),
    item($items, 276),
    item($items, 54),
    item($items, 54),
    item($items, 54),
    item($items, 54),
    item($items, 429),
    item($items, 54),
    item($items, 54),
    item($items, 54),
    item($items, 54),
    item($items, 574),
    item($items, 54),
    item($items, 611),
];

$bg = $imagine->open('./img/bg_vote.png');
$bg->resize(new Box(156 * 3, 141 * 3));


$font = new Font('./img/pokemon_fire_red.ttf', 18, $palette->color('#000'));
$fontTitle = new Font('./img/pokemon_fire_red.ttf', 36, $palette->color('#000'));

$image->paste($bg, new Point(0, 0));
$image->draw()->text(strtoupper("Vote Streak"), $fontTitle, new Point(142, 10));

for ($i = 0; $i < 32; $i++) {
    $image->draw()->text(strtoupper("#" . ($i + 1)), $font, new Point(25 + ($i % 8) * 55, 105 + floor($i / 8) * 77));
    $image->paste($dayItem[$i], new Point(25 + ($i % 8) * 55, 130 + floor($i / 8) * 77));
    if ($day >= ($i + 1)) {
        $image->paste($stamp, new Point(25 + ($i % 8) * 55, 130 + floor($i / 8) * 77));
    }
}

$options = array(
    'jpeg_quality' => 90,
);

$image
    ->show('png', $options);
