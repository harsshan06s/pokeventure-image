<?php
if (empty($_GET['d'])) {
    http_response_code(200);
    echo "Hello trainer!";
    exit();
}
$data = json_decode($_GET['d']);
// include composer autoload
require 'vendor/autoload.php';
function normalizeName($name)
{
    $name = strtolower($name);
    $name = str_replace(" ", "", $name);
    $name = str_replace('’', '', $name);
    $name = str_replace('.', '', $name);
    $name = str_replace(':', '', $name);
    $name = str_replace('mega-y', 'megay', $name);
    $name = str_replace('mega-x', 'megax', $name);
    $name = str_replace('dusk-mane', 'duskmane', $name);
    $name = str_replace('dawn-wings', 'dawnwings', $name);
    $name = str_replace('-strike', 'strike', $name);
    if (strpos($name, "giratina") === false) {
        $name = str_replace('-o', 'o', $name);
    }
    $name = str_replace('é', 'e', $name);
    if (strpos($name, 'nidoran') !== false || strpos($name, 'porygon') !== false) {
        $name = str_replace('-', '', $name);
    }
    return $name;
    }
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
use Imagine\Gd\Font;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

$imagine = new Imagine\Gd\Imagine();

$image = $imagine->create(new Box(256 * 2.5, 183 * 2.5));

$bg = $imagine->open('./img/slot.jpg');
$bg->resize(new Box(256 * 2.5, 183 * 2.5));
//$bg->effects()->colorize($bg->palette()->color("#000000"));
$palette = new RGB();
$font = new Font('./img/pokemon_fire_red.ttf', 18, $palette->color('#000'));
$stamp = $imagine->open('./img/stamp.png');
//$stamp->resize(new Box(96, 96));
$stamp->effects()->colorize($stamp->palette()->color("#FF0000"));

$image->paste($bg, new Point(0, 0), 100);

for ($y = 0; $y < 3; $y++) {
    for ($x = 0; $x < 3; $x++) {
        $pokemon = $imagine->open('./img/shiny/' . abs($data->data[$y][$x]) . '.png');
        $image->paste($pokemon, new Point($x * 147 + 124, $y * 80 + 50));
    }
}

// $image->draw()->text('Lv.' . $data->p2->level, $font, new Point(155, 28), 0, 200);
// $image->draw()->text(strtoupper($namePlayer), $font, new Point(310, $canvasHeight - 95));
// $image->draw()->text('Lv. ' . $data->p1->level, $font, new Point(430, $canvasHeight - 95));

$options = array(
    'jpeg_quality' => 100,
);

$image
    ->show('jpg', $options);
