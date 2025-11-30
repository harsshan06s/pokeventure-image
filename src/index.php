<?php
// web/index.php
// Image endpoint that renders battle images and sends a privacy-preserving IP fingerprint to the bot server.

if (empty($_GET['d'])) {
    http_response_code(200);
    echo "Hello trainer!";
    exit();
}

$data = json_decode(base64_decode($_GET['d']));
require 'vendor/autoload.php';

// Configuration: set these environment variables on the webserver
// IMAGE_LOG_SECRET: shared secret header value to authenticate POSTs to bot
// IP_SALT: secret used to HMAC the remote IP (never store raw IPs)
// BOT_LOG_URL: full URL to POST image logs to (e.g. https://bot.example.com/api/image-log)

$IMAGE_LOG_SECRET = getenv('IMAGE_LOG_SECRET');
$IP_SALT = getenv('IP_SALT');
$BOT_LOG_URL = getenv('BOT_LOG_URL');
// If set to '1', trust the X-Forwarded-For header (only enable when behind a trusted proxy/loadbalancer)
$TRUST_PROXY = getenv('TRUST_PROXY');

function normalizeName($name) {
    $name = strtolower($name);
    if (class_exists('Normalizer')) {
        $name = Normalizer::normalize($name, Normalizer::FORM_C);
    } else {
        $name = str_replace(["e\u{0301}", "E\u{0301}"], ["é", "É"], $name);
    }
    $name = str_replace([" ", "'", "’", "‘", ".", ":", "é"], ["", "", "", "", "", "", "e"], $name);
    $name = str_replace(
        ['mega-y','mega-x','-strike','white-striped','blue-striped','rock-star','pop-star','dusk-mane','dawn-wings'],
        ['megay','megax','strike','whitestriped','bluestriped','rockstar','popstar','duskmane','dawnwings'],
        $name
    );
    $name = ltrim($name, '-');
    if (!in_array($name, ['giratina-origin', 'dialga-origin', 'palkia-origin'])) {
        $name = str_replace('-o', 'o', $name);
    }
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

function openPokemonImage($imagine, $sideDir, $pokemonName, $formeName, $shiny = false) {
    $baseDir = './img/' . $sideDir . ($shiny ? '-shiny' : '') . '/';
    $p = normalizeName($pokemonName);
    $f = normalizeName($formeName);

    $candidates = [];
    if ($f !== '') $candidates[] = $baseDir . $p . $f . '.gif';
    if ($f !== '') $candidates[] = $baseDir . $p . '-' . ltrim($f, '-') . '.gif';
    $candidates[] = $baseDir . $p . '.gif';

    foreach ($candidates as $path) {
        try {
            if (is_file($path)) {
                return $imagine->open($path);
            }
        } catch (Exception $e) {
        }
    }
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

$g1 = isset($data->p1->gender) ? strtoupper(trim($data->p1->gender)) : '';
$g2 = isset($data->p2->gender) ? strtoupper(trim($data->p2->gender)) : '';
if ($g1 === 'M' || $g1 === 'F') {
    $image->paste($g1 === 'M' ? $male : $female, new Point(304, $canvasHeight - 67));
}
if ($g2 === 'M' || $g2 === 'F') {
    $image->paste($g2 === 'M' ? $male : $female, new Point(26, 58));
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

// Determine client IP safely. Use X-Forwarded-For only when TRUST_PROXY is explicitly enabled.
function getClientIp() {
    global $TRUST_PROXY;
    // Prefer X-Forwarded-For when trust is enabled and header is present
    if ($TRUST_PROXY === '1' && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $candidate = trim($parts[0]);
        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            return $candidate;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

// Privacy-preserving IP logging: compute HMAC(ip) and send minimal info to bot
$ip = getClientIp();
if ($IP_SALT && $BOT_LOG_URL && $IMAGE_LOG_SECRET) {
    $ipHash = hash_hmac('sha256', $ip, $IP_SALT);
    // Prefer discord_id from the payload; fallback to GET param
    $discordId = $data->p1->discord_id ?? ($_GET['discord_id'] ?? null);

    // Build JSON body with unescaped unicode/slashes for clarity
    $payload = json_encode([
        'ip_hash' => $ipHash,
        'discord_id' => $discordId,
        'image' => $data->type ?? 'wild',
        'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'ts' => time()
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // Trim quotes or accidental whitespace around the secret so header is exact
    $trimmedSecret = trim($IMAGE_LOG_SECRET, "'\"\s\t\n\r\0\x0B");

    $ch = curl_init($BOT_LOG_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Image-Log-Secret: ' . $trimmedSecret
    ]);
    // Make request non-blocking/quiet for the image response path
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 200);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $curlErrNo = curl_errno($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curlErrNo) {
        error_log('image-log curl error: ' . curl_error($ch) . ' (errno ' . $curlErrNo . ')');
    } elseif ($httpCode >= 400) {
        error_log('image-log http failure: HTTP ' . $httpCode . ' response: ' . substr((string)$result, 0, 1000));
    }
    curl_close($ch);
}

$image->show('jpg', ['jpeg_quality' => 90, 'resolution-x' => $canvasWidth, 'resolution-y' => $canvasHeight]);

?>
