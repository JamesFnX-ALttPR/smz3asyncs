<?php

function getRequestURL() {
    if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    $url = $protocol . $_SERVER['HTTP_HOST'];
    return $url;
}

function is_post_request(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
}

function is_get_request(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'GET';
}

function curlData($s) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $s);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    return curl_exec($ch);
}

function convertTimestamp($s) {
    $a = substr($s, 0, 10) . ' ' . substr($s, 11, 8);
    return $a;
}

function alttprValidateInfoBot($s) {
    if($s == '') {
        return false;
    }
    $sArray = explode(' - ', $s, 3);
    if(count($sArray) < 3) {
        return false;
    } else {
        return true;
    }
}

function inertia_validation($s) {
    if ($s == '') {
        return false;
    }
    $s_array = preg_split('/\r\n|\r|\n/', $s, 2);
    if (count($s_array) != 2) {
        return false;
    }
    $url_to_test = $s_array[0];
    $hash_to_test = $s_array[1];
    $valid_hashes = ["AIGORU", "ALCOON", "ARRGI", "ATOMIC", "BABURU", "BABUSU", "BEETOM", "BORU", "BOYON", "BULL", "CHOOT", "COVERN", "EVIR",
    "FANGIN", "FUNE", "GAMET", "GAPURA", "GEEMER", "GERUDO", "GERUTA", "GIBO", "GUZU", "HACHI", "HEISHI", "HOBA", "HOLTZ", "HYU",
    "KAGO", "KISU", "KOPPI", "KURIPI", "KYUNE", "MEDUSA", "NAMIHE", "NOMOSU", "OUM", "OWTCH", "PIKKU", "PON", "POPO", "POWAMP",
    "PUROMI", "PUYO", "RIBA", "RINKA", "RIPPER", "ROPA", "SCISER", "SKREE", "SOVA", "SUTARU", "TAINON", "TATORI", "TERU", "TOPPO",
    "TOZOKU", "VIOLA", "WAINDA", "WAVER", "YARD", "ZEBBO", "ZEELA", "ZOA", "ZORA"];
    if (substr($url_to_test, 0, 13) != 'https://samus') {
        return false;
    }
    $hash_array = explode(' ', $hash_to_test, 4);
    if (count($hash_array) != 4) {
        return false;
    }
    for ($i=0; $i < count($hash_array); $i++) {
        if (!in_array($hash_array[$i], $valid_hashes)) {
            return false;
        }
    }
    return true;
}

function alttprParseInfoBot($s) {
    $sArray = explode(' - ', $s, 3);
    return $sArray;
}

function convertFinishTime($s) {
    $hours = substr($s, 4, 2);
    $minutes = substr($s, 7, 2);
    $seconds = substr($s, 10, 2);
    return 3600 * intval($hours) + 60 * intval($minutes) + intval($seconds);
}

function hashToTable($s) {
    $s = str_replace('(','',str_replace(')','',$s));
    $sArray = explode(' ', $s);
    $rtn = '<table class="hash"><tr>';
    $eleCount = 0;
    foreach($sArray as $str) {
        $eleCount++;
        $rtn .= '<td class="hash">' . $str . '</td>';
        if ($eleCount % 2 == 0) {
            $rtn .= '</tr><tr>';
        }
    }
    $rtn .= '</tr></table>';
    return $rtn;
}

function Utf8_ansi($valor='') {

    $utf8_ansi2 = array(
    "\u00c0" =>"À",
    "\u00c1" =>"Á",
    "\u00c2" =>"Â",
    "\u00c3" =>"Ã",
    "\u00c4" =>"Ä",
    "\u00c5" =>"Å",
    "\u00c6" =>"Æ",
    "\u00c7" =>"Ç",
    "\u00c8" =>"È",
    "\u00c9" =>"É",
    "\u00ca" =>"Ê",
    "\u00cb" =>"Ë",
    "\u00cc" =>"Ì",
    "\u00cd" =>"Í",
    "\u00ce" =>"Î",
    "\u00cf" =>"Ï",
    "\u00d1" =>"Ñ",
    "\u00d2" =>"Ò",
    "\u00d3" =>"Ó",
    "\u00d4" =>"Ô",
    "\u00d5" =>"Õ",
    "\u00d6" =>"Ö",
    "\u00d8" =>"Ø",
    "\u00d9" =>"Ù",
    "\u00da" =>"Ú",
    "\u00db" =>"Û",
    "\u00dc" =>"Ü",
    "\u00dd" =>"Ý",
    "\u00df" =>"ß",
    "\u00e0" =>"à",
    "\u00e1" =>"á",
    "\u00e2" =>"â",
    "\u00e3" =>"ã",
    "\u00e4" =>"ä",
    "\u00e5" =>"å",
    "\u00e6" =>"æ",
    "\u00e7" =>"ç",
    "\u00e8" =>"è",
    "\u00e9" =>"é",
    "\u00ea" =>"ê",
    "\u00eb" =>"ë",
    "\u00ec" =>"ì",
    "\u00ed" =>"í",
    "\u00ee" =>"î",
    "\u00ef" =>"ï",
    "\u00f0" =>"ð",
    "\u00f1" =>"ñ",
    "\u00f2" =>"ò",
    "\u00f3" =>"ó",
    "\u00f4" =>"ô",
    "\u00f5" =>"õ",
    "\u00f6" =>"ö",
    "\u00f8" =>"ø",
    "\u00f9" =>"ù",
    "\u00fa" =>"ú",
    "\u00fb" =>"û",
    "\u00fc" =>"ü",
    "\u00fd" =>"ý",
    "\u00ff" =>"ÿ");

    return strtr($valor, $utf8_ansi2);

}

function parseSynackAPI($s) {
    $sArray = explode("\\n", $s);
    foreach($sArray as $str) {
        $parse = explode(":", $str, 2);
        if($parse[0] == 'description') {
            $desc = $parse[1];
        } else {
            $desc = null;
        }
	}
    if(!$desc) {
        return '';
    } else {
        $desc = Utf8_ansi(substr(str_replace('\\"', '', str_replace('\\r', '', $desc)),1));
        return $desc;
    }
}

function generateRaceSlug() {
    $part1 = ['terrible', 'silly', 'resonant', 'wideeyed', 'economic', 'kindly', 'obsequious', 'salty', 'material', 'colorful', 'vengeful', 'smelly', 'hesitant', 'dispensable',
    'hissing', 'stupendous', 'fretful', 'dapper', 'fluttering', 'idiotic', 'omniscient', 'weary', 'successful', 'apathetic', 'stormy', 'chivalrous', 'synonymous', 'fearless',
    'mindless', 'spiky', 'tidy', 'courageous', 'awake', 'rampant', 'enormous', 'eatable', 'boorish', 'legal', 'tangy', 'deft'];
    $part2 = ['cutman', 'elecman', 'fireman', 'iceman', 'gutsman', 'bombman', 'flashman', 'crashman', 'metalman', 'bubbleman', 'heatman', 'airman', 'quickman', 'woodman', 'topman',
    'hardman', 'magnetman', 'geminiman', 'snakeman', 'sparkman', 'shadowman', 'needleman', 'brightman', 'pharaohman', 'skullman', 'diveman', 'drillman', 'dustman', 'toadman',
    'ringman', 'starman', 'gravityman', 'gyroman', 'chargeman', 'napalmman', 'crystalman', 'waveman', 'stoneman', 'blizzardman', 'flameman', 'centaurman', 'knightman',
    'yamatoman', 'windman', 'plantman'];
    $rand1 = random_int(0, 39);
    $rand2 = random_int(0, 44);
    $rand3 = random_int(1, 99999);
    $slugPart1 = $part1[$rand1];
    $slugPart2 = $part2[$rand2];
    $slugPart3 = sprintf('%05d', $rand3);
    return $slugPart1 . '-' . $slugPart2 . '-' . $slugPart3;
}

function generateRacerID() {
    $array = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
    'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . 
    $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . 
    $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)] . $array[random_int(0,61)];
}

function createHashDropdown($str = '') {
    $array = ["AIGORU", "ALCOON", "ARRGI", "ATOMIC", "BABURU", "BABUSU", "BEETOM", "BORU", "BOYON", "BULL", "CHOOT", "COVERN", "EVIR",
    "FANGIN", "FUNE", "GAMET", "GAPURA", "GEEMER", "GERUDO", "GERUTA", "GIBO", "GUZU", "HACHI", "HEISHI", "HOBA", "HOLTZ", "HYU",
    "KAGO", "KISU", "KOPPI", "KURIPI", "KYUNE", "MEDUSA", "NAMIHE", "NOMOSU", "OUM", "OWTCH", "PIKKU", "PON", "POPO", "POWAMP",
    "PUROMI", "PUYO", "RIBA", "RINKA", "RIPPER", "ROPA", "SCISER", "SKREE", "SOVA", "SUTARU", "TAINON", "TATORI", "TERU", "TOPPO",
    "TOZOKU", "VIOLA", "WAINDA", "WAVER", "YARD", "ZEBBO", "ZEELA", "ZOA", "ZORA"];
    echo '                    <option value=""></option>' . PHP_EOL;
    foreach ($array as $s) {
        echo '                    <option value="' . $s . '"';
        if ($str == $s) {
            echo ' selected';
        }
        echo '>' . $s . '</option>' . PHP_EOL;
    }
    unset ($s);
    unset ($convertedString);
}

function unparseHash($str) {
    // Strip parentheses
    $str = str_replace('(', '', str_replace(')', '', $str));
    // Explode hash into array
    $strArray = explode(' ', $str);
    return $strArray;
}

function generateTourneySlug() {
    $array = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return $array[random_int(0,35)] . $array[random_int(0,35)] . $array[random_int(0,35)] . $array[random_int(0,35)] . $array[random_int(0,35)] . $array[random_int(0,35)] . 
    $array[random_int(0,35)] . $array[random_int(0,35)];
}

function createCallbackLink() {
    return bin2hex(random_bytes(12));
}

