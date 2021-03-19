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
    $name = str_replace('-o', 'o', $name);
    $name = str_replace('é', 'e', $name);
    if (strpos($name, 'nidoran') !== false || strpos($name, 'porygon') !== false) {
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

$image = $imagine->create(new Box(518, 288));

$bg = $imagine->open('./img/bgs/-1.jpeg');
$pokemonImages = [];
$totalWidth = 0;
for($i = 0; $i < count($data->pokemons); $i++) {
    $pkmImg = $imagine->open('./img/front' . ($data->pokemons[$i]->shiny ? '-shiny' : '') . '/' . normalizeName($data->pokemons[$i]->name) . '.gif');
    $pokemonImages[] = $pkmImg;
    $totalWidth += $pkmImg->getSize()->getWidth();
}
/*try {
    $enemy_pokemon = $imagine->open('./img/front' . ($data->p2->shiny ? '-shiny' : '') . '/' . normalizeName($data->p2->name) . $data->p2->forme . '.gif');
} catch (Exception $e) {
    $enemy_pokemon = $imagine->open('./img/missingno.png');
}
$enemy_pokemon_size = $enemy_pokemon->getSize();
try {
    $player_pokemon = $imagine->open('./img/back' . ($data->p1->shiny ? '-shiny' : '') . '/' . normalizeName($data->p1->name) . $data->p1->forme . '.gif');
} catch (Exception $e) {
    $player_pokemon = $imagine->open('./img/missingno.png');
}*/

$palette = new RGB();
$font = new Font('./img/pokemon_fire_red.ttf', 18, $palette->color('#000'));

$image->paste($bg, new Point(0, 0));

for($i = 0; $i < count($pokemonImages); $i++) {
    $image->paste($pokemonImages[$i], new Point($i * 150 - $pokemonImages[$i]->getSize()->getWidth() / 2 + 110, 175 - $pokemonImages[$i]->getSize()->getHeight()));
    $nameBox = $font->box(strtoupper($data->pokemons[$i]->name));
    $image->draw()->text(strtoupper($data->pokemons[$i]->name), $font, new Point($i * 150 - $nameBox->getWidth() / 2 + 110, 180));
    $image->draw()->text('Lv.'. ($data->pokemons[$i]->level), $font, new Point($i * 150 - $nameBox->getWidth() / 2 + 122, 200));
}

/*$image->paste($enemy_pokemon, new Point($pos_x_enemy, $pos_y_enemy));
$image->paste($player_pokemon, new Point($pos_x_player, $pos_y_player));

$image->draw()->text(strtoupper($nameEnemy), $font, new Point(31, 28));
$image->draw()->text('Lv.' . $data->p2->level, $font, new Point(155, 28), 0, 200);
$image->draw()->text(strtoupper($namePlayer), $font, new Point(310, $canvasHeight - 95));
$image->draw()->text('Lv.' . $data->p1->level, $font, new Point(430, $canvasHeight - 95));*/

$options = array(
    'jpeg_quality' => 90,
 );

$image
    ->show('jpg', $options);
