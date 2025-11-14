<?php
if (empty($_GET['d'])) {
    http_response_code(200);
    echo "Hello trainer!";
    exit();
}
$data = json_decode(base64_decode($_GET['d']));
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
    $name = str_replace('-strike', 'strike', $name);
    $name = str_replace('white-striped', 'whitestriped', $name);
    $name = str_replace('blue-striped', 'bluestriped', $name);
    $name = str_replace('rock-star', 'rockstar', $name);
    $name = str_replace('pop-star', 'popstar', $name);
    $name = str_replace('dusk-mane', 'duskmane', $name);
    $name = str_replace('dawn-wings', 'dawnwings', $name);
    $name = str_replace('vdays', '-vdays', $name);
    if(strpos($name, "giratina") === false && strpos($name, "dialga") === false && strpos($name, "palkia") === false) {
        $name = str_replace('-o', 'o', $name);
    }
    $name = str_replace('é', 'e', $name);
    if (strpos($name, 'nidoran') !== false || (strpos($name, 'porygon') !== false && strpos($name, 'xmas') === false)) {
        $name = str_replace('-', '', $name);
    }
    return $name;
}

use Imagine\Gd\Font;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

$imagine = new Imagine\Gd\Imagine();

$canvasWidth = 518;
$canvasHeight = 288;

$image = $imagine->create(new Box($canvasWidth, $canvasHeight));

$bg = $imagine->open('./img/bgs/-1.jpeg');
$pokemonImages = [];
$totalWidth = 0;
for($i = 0; $i < count($data->pokemons); $i++) {
    try {
        if ($data->pokemons[$i]->name === "?") {
            // Use a default image for empty slots
            $pkmImg = $imagine->open('./img/missingno.png');
        } else {
            $pkmImg = $imagine->open('./img/front' . ($data->pokemons[$i]->shiny ? '-shiny' : '') . '/' . normalizeName($data->pokemons[$i]->name) . normalizeName($data->pokemons[$i]->forme) . '.gif');
        }
        $pokemonImages[] = $pkmImg;
        $totalWidth += $pkmImg->getSize()->getWidth();
    } catch(Exception $e) {
        echo $e;
        die;
    }
}

$palette = new RGB();
$font = new Font('./img/pokemon_fire_red.ttf', 18, $palette->color('#000'));

$image->paste($bg, new Point(0, 0));

for($i = 0; $i < count($pokemonImages); $i++) {
    $image->paste($pokemonImages[$i], new Point(max(0, $i * 150 - $pokemonImages[$i]->getSize()->getWidth() / 2 + 110), max(0, 175 - $pokemonImages[$i]->getSize()->getHeight())));
    $nameBox = $font->box(strtoupper($data->pokemons[$i]->name));
    $image->draw()->text(strtoupper($data->pokemons[$i]->name), $font, new Point($i * 150 - $nameBox->getWidth() / 2 + 110, 180));
    $image->draw()->text('Lv.'. ($data->pokemons[$i]->level), $font, new Point($i * 150 - $nameBox->getWidth() / 2 + 122, 200));
}

$options = array(
    'jpeg_quality' => 90,
 );

$image
    ->show('jpg', $options);