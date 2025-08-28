<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

$name = [
    '10' => 'Caterpie',
    '11' => 'Metapod',
    '13' => 'Weedle',
    '14' => 'Kakuna',
    '16' => 'Pidgey',
    '19' => 'Rattata',
    '21' => 'Spearow',
    '23' => 'Ekans',
    '25' => 'Pikachu',
    '27' => 'Sandshrew',
    '29' => 'Nidoran♀',
    '32' => 'Nidoran♂',
    '35' => 'Clefairy',
    '37' => 'Vulpix',
    '39' => 'Jigglypuff',
    '41' => 'Zubat',
    '43' => 'Oddish',
    '46' => 'Paras',
    '48' => 'Venonat',
    '50' => 'Diglett',
    '52' => 'Meowth',
    '54' => 'Psyduck',
    '56' => 'Mankey',
    '60' => 'Poliwag',
    '63' => 'Abra',
    '66' => 'Machop',
    '69' => 'Bellsprout',
    '72' => 'Tentacool',
    '74' => 'Geodude',
    '77' => 'Ponyta',
    '79' => 'Slowpoke',
    '81' => 'Magnemite',
    '84' => 'Doduo',
    '86' => 'Seel',
    '88' => 'Grimer',
    '90' => 'Shellder',
    '92' => 'Gastly',
    '95' => 'Onix',
    '96' => 'Drowzee',
    '98' => 'Krabby',
    '100' => 'Voltorb',
    '102' => 'Exeggcute',
    '104' => 'Cubone',
    '108' => 'Lickitung',
    '109' => 'Koffing',
    '114' => 'Tangela',
    '115' => 'Kangaskhan',
    '116' => 'Horsea',
    '118' => 'Goldeen',
    '120' => 'Staryu',
    '129' => 'Magikarp',
    '132' => 'Ditto',
    '133' => 'Eevee',
    '161' => 'Sentret',
    '163' => 'Hoothoot',
    '165' => 'Ledyba',
    '167' => 'Spinarak',
    '170' => 'Chinchou',
    '172' => 'Pichu',
    '177' => 'Natu',
    '179' => 'Mareep',
    '183' => 'Marill',
    '185' => 'Sudowoodo',
    '187' => 'Hoppip',
    '190' => 'Aipom',
    '191' => 'Sunkern',
    '193' => 'Yanma',
    '194' => 'Wooper',
    '198' => 'Murkrow',
    '200' => 'Misdreavus',
    '201' => 'Unown',
    '203' => 'Girafarig',
    '204' => 'Pineco',
    '206' => 'Dunsparce',
    '207' => 'Gligar',
    '209' => 'Snubbull',
    '211' => 'Qwilfish',
    '213' => 'Shuckle',
    '215' => 'Sneasel',
    '216' => 'Teddiursa',
    '218' => 'Slugma',
    '220' => 'Swinub',
    '222' => 'Corsola',
    '223' => 'Remoraid',
    '225' => 'Delibird',
    '226' => 'Mantine',
    '228' => 'Houndour',
    '231' => 'Phanpy',
    '234' => 'Stantler',
    '235' => 'Smeargle',
    '241' => 'Miltank',
    '246' => 'Larvitar',
    '261' => 'Poochyena',
    '263' => 'Zigzagoon',
    '265' => 'Wurmple',
    '266' => 'Silcoon',
    '268' => 'Cascoon',
    '270' => 'Lotad',
    '273' => 'Seedot',
    '276' => 'Taillow',
    '278' => 'Wingull',
    '280' => 'Ralts',
    '283' => 'Surskit',
    '285' => 'Shroomish',
    '287' => 'Slakoth',
    '290' => 'Nincada',
    '293' => 'Whismur',
    '296' => 'Makuhita',
    '299' => 'Nosepass',
    '300' => 'Skitty',
    '302' => 'Sableye',
    '303' => 'Mawile',
    '304' => 'Aron',
    '307' => 'Meditite',
    '309' => 'Electrike',
    '313' => 'Volbeat',
    '314' => 'Illumise',
    '315' => 'Roselia',
    '316' => 'Gulpin',
    '318' => 'Carvanha',
    '320' => 'Wailmer',
    '322' => 'Numel',
    '324' => 'Torkoal',
    '325' => 'Spoink',
    '326' => 'Grumpig',
    '327' => 'Spinda 1',
    '328' => 'Trapinch',
    '329' => 'Vibrava',
    '331' => 'Cacnea',
    '333' => 'Swablu',
    '335' => 'Zangoose',
    '336' => 'Seviper',
    '337' => 'Lunatone',
    '338' => 'Solrock',
    '339' => 'Barboach',
    '341' => 'Corphish',
    '343' => 'Baltoy',
    '349' => 'Feebas',
    '352' => 'Kecleon',
    '353' => 'Shuppet',
    '355' => 'Duskull',
    '358' => 'Chimecho',
    '359' => 'Absol',
    '361' => 'Snorunt',
    '363' => 'Spheal',
    '366' => 'Clamperl',
    '370' => 'Luvdisc',
    '396' => 'Starly',
    '399' => 'Bidoof',
    '401' => 'Kricketot',
    '403' => 'Shinx',
    '406' => 'Budew',
    '412' => 'Burmy',
    '415' => 'Combee',
    '417' => 'Pachirisu',
    '418' => 'Buizel',
    '420' => 'Cherubi',
    '422' => 'Shellos',
    '424' => 'Ambipom',
    '425' => 'Drifloon',
    '427' => 'Buneary',
    '429' => 'Mismagius',
    '431' => 'Glameow',
    '434' => 'Stunky',
    '436' => 'Bronzor',
    '441' => 'Chatot',
    '442' => 'Spiritomb',
    '443' => 'Gible',
    '449' => 'Hippopotas',
    '451' => 'Skorupi',
    '453' => 'Croagunk',
    '455' => 'Carnivine',
    '456' => 'Finneon',
    '458' => 'Mantyke',
    '459' => 'Snover',
    '461' => 'Weavile',
    '477' => 'Dusknoir',
    '504' => 'Patrat',
    '506' => 'Lillipup',
    '509' => 'Purrloin',
    '511' => 'Pansage',
    '513' => 'Pansear',
    '515' => 'Panpour',
    '517' => 'Munna',
    '519' => 'Pidove',
    '522' => 'Blitzle',
    '524' => 'Roggenrola',
    '527' => 'Woobat',
    '529' => 'Drilbur',
    '531' => 'Audino',
    '532' => 'Timburr',
    '535' => 'Tympole',
    '540' => 'Sewaddle',
    '543' => 'Venipede',
    '546' => 'Cottonee',
    '548' => 'Petilil',
    '550' => 'Basculin',
    '551' => 'Sandile',
    '554' => 'Darumaka',
    '556' => 'Maractus',
    '557' => 'Dwebble',
    '559' => 'Scraggy',
    '561' => 'Sigilyph',
    '562' => 'Yamask',
    '568' => 'Trubbish',
    '570' => 'Zorua',
    '572' => 'Minccino',
    '574' => 'Gothita',
    '577' => 'Solosis',
    '580' => 'Ducklett',
    '582' => 'Vanillite',
    '585' => 'Deerling',
    '587' => 'Emolga',
    '588' => 'Karrablast',
    '590' => 'Foongus',
    '592' => 'Frillish',
    '594' => 'Alomomola',
    '595' => 'Joltik',
    '597' => 'Ferroseed',
    '599' => 'Klink',
    '602' => 'Tynamo',
    '605' => 'Elgyem',
    '607' => 'Litwick',
    '610' => 'Axew',
    '613' => 'Cubchoo',
    '616' => 'Shelmet',
    '618' => 'Stunfisk',
    '619' => 'Mienfoo',
    '621' => 'Druddigon',
    '622' => 'Golett',
    '624' => 'Pawniard',
    '626' => 'Bouffalant',
    '627' => 'Rufflet',
    '629' => 'Vullaby',
    '631' => 'Heatmor',
    '632' => 'Durant',
    '633' => 'Deino',
    '636' => 'Larvesta',
    '659' => 'Bunnelby',
    '661' => 'Fletchling',
    '664' => 'Scatterbug',
    '667' => 'Litleo',
    '669' => 'Flabébé',
    '672' => 'Skiddo',
    '674' => 'Pancham',
    '676' => 'Furfrou',
    '677' => 'Espurr',
    '679' => 'Honedge',
    '682' => 'Spritzee',
    '684' => 'Swirlix',
    '686' => 'Inkay',
    '688' => 'Binacle',
    '690' => 'Skrelp',
    '692' => 'Clauncher',
    '694' => 'Helioptile',
    '701' => 'Hawlucha',
    '702' => 'Dedenne',
    '703' => 'Carbink',
    '704' => 'Goomy',
    '707' => 'Klefki',
    '708' => 'Phantump',
    '710' => 'Pumpkaboo',
    '712' => 'Bergmite',
    '714' => 'Noibat',
    '731' => 'Pikipek',
    '734' => 'Yungoos',
    '736' => 'Grubbin',
    '738' => 'Vikavolt',
    '739' => 'Crabrawler',
    '741' => 'Oricorio',
    '742' => 'Cutiefly',
    '744' => 'Rockruff',
    '746' => 'Wishiwashi',
    '747' => 'Mareanie',
    '749' => 'Mudbray',
    '751' => 'Dewpider',
    '753' => 'Fomantis',
    '755' => 'Morelull',
    '757' => 'Salandit',
    '759' => 'Stufful',
    '761' => 'Bounsweet',
    '764' => 'Comfey',
    '765' => 'Oranguru',
    '766' => 'Passimian',
    '767' => 'Wimpod',
    '769' => 'Sandygast',
    '771' => 'Pyukumuku',
    '774' => 'Minior',
    '775' => 'Komala',
    '776' => 'Turtonator',
    '777' => 'Togedemaru',
    '778' => 'Mimikyu',
    '779' => 'Bruxish',
    '780' => 'Drampa',
    '781' => 'Dhelmise',
    '782' => 'Jangmo-o',
    '819' => 'Skwovet',
    '821' => 'Rookidee',
    '824' => 'Blipbug',
    '827' => 'Nickit',
    '829' => 'Gossifleur',
    '831' => 'Wooloo',
    '833' => 'Chewtle',
    '835' => 'Yamper',
    '837' => 'Rolycoly',
    '840' => 'Applin',
    '843' => 'Silicobra',
    '845' => 'Cramorant',
    '846' => 'Arrokuda',
    '848' => 'Toxel',
    '850' => 'Sizzlipede',
    '852' => 'Clobbopus',
    '854' => 'Sinistea',
    '856' => 'Hatenna',
    '859' => 'Impidimp',
    '868' => 'Milcery',
    '870' => 'Falinks',
    '871' => 'Pincurchin',
    '872' => 'Snom',
    '874' => 'Stonjourner',
    '875' => 'Eiscue',
    '876' => 'Indeedee',
    '877' => 'Morpeko',
    '878' => 'Cufant',
    '884' => 'Duraludon',
    '885' => 'Dreepy'
];

use Imagine\Gd\Font;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;

$imagine = new Imagine\Gd\Imagine();

$image = $imagine->create(new Box(480, 480));

$bg = $imagine->open('./img/bingobg.jpg');
$bg->resize(new Box(640, 480));
//$bg->effects()->colorize($bg->palette()->color("#000000"));
$palette = new RGB();
$font = new Font('./img/pokemon_fire_red.ttf', 18, $palette->color('#000'));
$stamp = $imagine->open('./img/stamp.png');
$stamp->resize(new Box(96, 96));
$stamp->effects()->colorize($stamp->palette()->color("#FF0000"));

$image->paste($bg, new Point(0, 0), 100);

for ($y = 0; $y < 5; $y++) {
    $image->draw()->line(new Point(($y + 1) * 96, 0), new Point(($y + 1) * 96, 480), $palette->color('#000000'));
    $image->draw()->line(new Point(0, ($y + 1) * 96), new Point(480, ($y + 1) * 96), $palette->color('#000000'));
    for ($x = 0; $x < 5; $x++) {
        if ($x == 2 && $y == 2) {
            $pokemon = $imagine->open('./img/logo.png');
            $image->paste($pokemon, new Point($x * 96 + 3, $y * 96));
        } else {
            $pokemon = $imagine->open('./img/mini/' . abs($data->data[$y][$x]) . '.png');
            $image->paste($pokemon, new Point($x * 96, $y * 96));
            if ($data->data[$y][$x] < 0) {
                $image->paste($stamp, new Point($x * 96, $y * 96));
            }
        }
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
