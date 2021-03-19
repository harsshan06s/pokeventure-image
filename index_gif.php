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

use Imagine\Imagick\Font;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

$imagine = new Imagine\Imagick\Imagine();
$imagick = new Imagick();

$canvasWidth = 518;
$canvasHeight = 288;

$floorWidth = 128;
$floorHeight = 32;

$spriteWidth = 92;
$spriteHeight = 92;


$bg = $imagine->open('./img/bgs/' . $data->location . '.jpeg');
try {
    if (!empty($data->p2->substitute)) {
        $enemy_pokemon = $imagine->open('./img/front/substitute.gif');
    } else {
        $enemy_pokemon = $imagine->open('./img/front' . ($data->p2->shiny ? '-shiny' : '') . '/' . normalizeName($data->p2->pokemon) . $data->p2->forme . '.gif');
    }
} catch (Exception $e) {
    $enemy_pokemon = $imagine->open('./img/missingno.png');
}
$enemy_pokemon_size = $enemy_pokemon->getSize();
try {
    if (!empty($data->p1->substitute)) {
        $player_pokemon = $imagine->open('./img/back/substitute.gif');
    } else {
        $player_pokemon = $imagine->open('./img/back' . ($data->p1->shiny ? '-shiny' : '') . '/' . normalizeName($data->p1->pokemon) . $data->p1->forme . '.gif');
    }
} catch (Exception $e) {
    $player_pokemon = $imagine->open('./img/missingno.png');
}
$player_pokemon_size = $player_pokemon->getSize();


$male = $imagine->open('./img/male.png');
$female = $imagine->open('./img/female.png');

$ui = $imagine->open('./img/ui.png');
$palette = new RGB();
$font = new Font($imagick, './img/pokemon_fire_red.ttf', 18, $palette->color('#000'));

$pos_x_player = 50 + $spriteWidth / 4 + $floorWidth / 2 - $player_pokemon_size->getWidth() / 2;
$pos_y_player = $canvasHeight - 33 - $player_pokemon_size->getHeight();

$pos_x_enemy = $canvasWidth - 30 - 105 - $enemy_pokemon_size->getWidth() / 2;
$pos_y_enemy = max(0, $floorHeight + 80 + $floorHeight - $enemy_pokemon_size->getHeight());

$images = [];
for ($i = 0; $i < 100; $i++) {
    $image = $imagine->create(new Box(518, 288));

    $image->paste($bg, new Point(0, 0));
    $image->paste($ui, new Point(0, 0));

    $namePlayer = str_replace('é', 'é', $data->p1->name);
    $nameEnemy = str_replace('é', 'é', $data->p2->name);

    $image->paste($enemy_pokemon, new Point($pos_x_enemy, $pos_y_enemy));
    $image->paste($player_pokemon, new Point($pos_x_player, $pos_y_player));

    $image->draw()->text(strtoupper($nameEnemy), $font, new Point(31, 22));
    $image->draw()->text('Lv.' . $data->p2->level, $font, new Point(155, 22), 0, 200);
    $image->draw()->text(strtoupper($namePlayer), $font, new Point(310, $canvasHeight - 101));
    $image->draw()->text('Lv. ' . $data->p1->level, $font, new Point(430, $canvasHeight - 101));

    if (!empty($data->p1->gender)) {
        if ($data->p1->gender == "M") {
            $image->paste($male, new Point(304, $canvasHeight - 67));
        } else if ($data->p1->gender == "F") {
            $image->paste($female, new Point(304, $canvasHeight - 67));
        }
    }
    if (!empty($data->p2->gender)) {
        if ($data->p2->gender == "M") {
            $image->paste($male, new Point(26, 58));
        } else if ($data->p2->gender == "F") {
            $image->paste($female, new Point(26, 58));
        }
    }

    $data1 = explode(' ', $data->p1->health);
    $data2 = explode(' ', $data->p2->health);

    if (!empty($data1[1])) {
        $image->draw()->text(strtoupper($data1[1]), $font, new Point(328, $canvasHeight - 69));
    }
    if (!empty($data2[1])) {
        $image->draw()->text(strtoupper($data2[1]), $font, new Point(50, 56));
    }

    $boxHpEnemy = $font->box(($data2[0] == '0' ? 'FAINTED' : $data2[0]));
    $image->draw()->text(($data2[0] == '0' ? 'FAINTED' : $data2[0]), $font, new Point(193 - $boxHpEnemy->getWidth(), 56));

    $boxHpPlayer = $font->box(($data1[0] == '0' ? 'FAINTED' : $data1[0]));
    $image->draw()->text(($data1[0] == '0' ? 'FAINTED' : $data1[0]), $font, new Point(470 - $boxHpPlayer->getWidth(), $canvasHeight - 68));

    $healthBarP1 = explode('/', $data1[0]);
    $percentP1 = (rand(0, 100) / 100) * 95;
    $healthBarP2 = explode('/', $data2[0]);
    $percentP2 = (rand(0, 100) / 100) * 95;

    $image->draw()->rectangle(new Point(99, 53), new Point(99 + $percentP2, 58), $palette->color('#16bf1d'), true);
    $image->draw()->rectangle(new Point(376, 218), new Point(376 + $percentP1, 223), $palette->color('#16bf1d'), true);
    $images[] = $image;
}

for($i = 1; $i < count($images); $i++) {
    $images[0]->layers()->add($images[$i]);
}

$images[0]
    ->show('gif', array(
        'animated' => true,
        'animated.delay' => 1000,
    ));
