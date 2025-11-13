<?php
if (empty($_GET['d'])) {
    http_response_code(200);
    echo "Hello trainer!";
    exit();
}

$data = json_decode(base64_decode($_GET['d']));
require 'vendor/autoload.php';

function normalizeName($name) {
    $name = strtolower($name);
    $name = str_replace([" ", "'", ".", ":", "é"], ["", "", "", "", "e"], $name);
    $name = str_replace(
        ['mega-y','mega-x','-strike','white-striped','blue-striped','rock-star','pop-star','dusk-mane','dawn-wings'],
        ['megay','megax','strike','whitestriped','bluestriped','rockstar','popstar','duskmane','dawnwings'],
        $name
    );
    
    // Remove leading hyphen (normalize "-origin" to "origin")
    $name = ltrim($name, '-');
    
    // Only replace internal '-o' with 'o' for Pokémon that are NOT Origin forms
    if (!in_array($name, ['giratina-origin', 'dialga-origin', 'palkia-origin'])) {
        $name = str_replace('-o', 'o', $name);
    }
    
    // Remove hyphens for nidoran and porygon (non-xmas)
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
$bg = $imagine->open('./img/bgs/' . $data->location . '.jpeg');

/**
 * Try several candidate filenames for a pokemon image to support form naming variations.
 * Preference order:
 *  1) direct concat: pokemon + forme
 *  2) pokemon + '-' + (forme without leading '-'),
 *  3) pokemon only
 */
function openPokemonImage($imagine, $sideDir, $pokemonName, $formeName, $shiny = false) {
    $baseDir = './img/' . $sideDir . ($shiny ? '-shiny' : '') . '/';
    $p = normalizeName($pokemonName);
    $f = normalizeName($formeName);

    $candidates = [];

    // 1) direct concat (existing behavior)
    if ($f !== '') $candidates[] = $baseDir . $p . $f . '.gif';

    // 2) explicit hyphen between pokemon and forme (handles cases where forme doesn't include leading hyphen)
    if ($f !== '') $candidates[] = $baseDir . $p . '-' . ltrim($f, '-') . '.gif';

    // 3) fallback to pokemon alone
    $candidates[] = $baseDir . $p . '.gif';

    foreach ($candidates as $path) {
        try {
            if (is_file($path)) {
                return $imagine->open($path);
            }
        } catch (Exception $e) {
            // ignore and try next
        }
    }

    // final fallback
    return $imagine->open('./img/missingno.png');
}

$enemy_pokemon = !empty($data->p2->substitute)
    ? $imagine->open('./img/front/substitute.gif')
    : openPokemonImage($imagine, 'front', $data->p2->pokemon, $data->p2->forme, $data->p2->shiny);
$enemy_pokemon_size = $enemy_pokemon->getSize();

$player_pokemon = !empty($data->p1->substitute)
    ? $imagine->open('./img/back/substitute.gif')
    : openPokemonImage($imagine, 'back', $data->p1->pokemon, $data->p1->forme, $data->p1->shiny);
$player_pokemon_size = $player_pokemon->getSize();

$male = $imagine->open('./img/male.png');
$female = $imagine->open('./img/female.png');
$ui = $imagine->open('./img/ui.png');

$palette = new RGB();
$font = new Font('./img/pokemon_fire_red.ttf', 18, $palette->color('#000'));

$image->paste($bg, new Point(0, 0));
$image->paste($ui, new Point(0, 0));

$pos_x_player = 50 + 92/4 + 128/2 - $player_pokemon_size->getWidth() / 2;
$pos_y_player = $canvasHeight - 33 - $player_pokemon_size->getHeight();
$pos_x_enemy = $canvasWidth - 30 - 105 - $enemy_pokemon_size->getWidth() / 2;
$pos_y_enemy = max(0, 32 + 80 + 32 - $enemy_pokemon_size->getHeight());

$image->paste($enemy_pokemon, new Point($pos_x_enemy, $pos_y_enemy));
$image->paste($player_pokemon, new Point($pos_x_player, $pos_y_player));

$namePlayer = str_replace('é', 'é', $data->p1->name);
$nameEnemy = str_replace('é', 'é', $data->p2->name);

$image->draw()->text(strtoupper($nameEnemy), $font, new Point(31, 28));
$image->draw()->text('Lv.' . $data->p2->level, $font, new Point(155, 28));
$image->draw()->text(strtoupper($namePlayer), $font, new Point(310, $canvasHeight - 95));
$image->draw()->text('Lv. ' . $data->p1->level, $font, new Point(430, $canvasHeight - 95));

if (!empty($data->p1->gender)) {
    $image->paste($data->p1->gender == "M" ? $male : $female, new Point(304, $canvasHeight - 67));
}
if (!empty($data->p2->gender)) {
    $image->paste($data->p2->gender == "M" ? $male : $female, new Point(26, 58));
}

$data1 = explode(' ', $data->p1->health);
$data2 = explode(' ', $data->p2->health);

if (!empty($data1[1])) $image->draw()->text(strtoupper($data1[1]), $font, new Point(328, $canvasHeight - 63));
if (!empty($data2[1])) $image->draw()->text(strtoupper($data2[1]), $font, new Point(50, 62));

$hpTextP1 = $data1[0];
$hpTextP2 = $data2[0];

$boxHpEnemy = $font->box(($hpTextP2 == '0' ? 'FAINTED' : $hpTextP2));
$image->draw()->text(($hpTextP2 == '0' ? 'FAINTED' : $hpTextP2), $font, new Point(193 - $boxHpEnemy->getWidth(), 62));

$boxHpPlayer = $font->box(($hpTextP1 == '0' ? 'FAINTED' : $hpTextP1));
$image->draw()->text(($hpTextP1 == '0' ? 'FAINTED' : $hpTextP1), $font, new Point(470 - $boxHpPlayer->getWidth(), $canvasHeight - 62));

function drawHpBar($hpText, $x1, $y1, $x2, $y2, $image, $palette) {
    $clean = str_replace("HP", "", strtoupper($hpText));
    $parts = explode('/', trim($clean));
    if (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1]) && $parts[1] > 0) {
        $percent = ($parts[0] / $parts[1]) * 95;
        $image->draw()->rectangle(new Point($x1, $y1), new Point($x1 + $percent, $y2), $palette->color('#16bf1d'), true);
    }
}

drawHpBar($hpTextP2, 99, 53, 99 + 95, 58, $image, $palette);
drawHpBar($hpTextP1, 376, 218, 376 + 95, 223, $image, $palette);

$image->show('jpg', ['jpeg_quality' => 90, 'resolution-x' => $canvasWidth, 'resolution-y' => $canvasHeight]);