<?php

require('../includes/functions.php');
require('../config/settings.php');
// Create DB connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $options);

$slug_list = array('');
$url = 'https://racetime.gg/smz3/races/data';
$url_data = curlData($url);
$url_json = json_decode($url_data, true);
$numPages = $url_json['num_pages'];
unset($url); unset($url_data); unset($url_json);
for($i=1;$i<=20;$i++) {
    $url = 'https://racetime.gg/smz3/races/data?page=' . $i;
    $url_data = curlData($url);
    $url_json = json_decode($url_data, true);
    $num_races = count($url_json['races']);
    for($j=0;$j<$num_races;$j++) {
        $race_name = substr($url_json['races'][$j]['name'], 5);
        array_unshift($slug_list, $race_name);
    }
    unset($url); unset($url_data); unset($url_json);
}
array_pop($slug_list);
$slug_count = count($slug_list);
for($i=0;$i<$slug_count;$i++) {
    $url = 'https://racetime.gg/smz3/' . $slug_list[$i] . '/data';
    $url_data = curlData($url);
    $url_json = json_decode($url_data, true);
    $info_bot = $url_json['info_bot'];
    $info_user = $url_json['info_user'];
    if($url_json['team_race'] == false) {
        $team_flag = 'n';
    } else {
        $team_flag = 'y';
    }
    $race_start = convertTimestamp($url_json['opened_at']);
    if(alttprValidateInfoBot($info_bot)) {
        $info_bot_array = alttprParseInfoBot($info_bot);
        $race_mode = $info_bot_array[0];
        $race_seed = $info_bot_array[1];
        $race_hash = $info_bot_array[2];
    } elseif (inertia_validation($info_bot)) {
        $info_bot_array = preg_split('/\r\n|\r|\n/', $info_bot, 2);
        $race_mode = 'mm2nescartridge/normalboots';
        $race_seed = $info_bot_array[0];
        $race_hash = $info_bot_array[1];
    } else {
        $race_mode = '';
        $race_seed = '';
        $race_hash = '';
    }
    if($race_seed != '') {
        $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = ?");
        $stmt->execute([$slug_list[$i]]);
        $race_exists = $stmt->fetchColumn();
        if(! $race_exists) {
            if(substr($race_mode, 0, 7) == 'spoiler') {
                $chat_log = 'https://racetime.gg/smz3/' . $slug_list[$i] . '.txt';
                preg_match('/https:\/\/.+\/api\/spoiler.+/', curlData($chat_log), $matches);
                $spoiler_link = $matches[0];
                $spoiler_flag = 'y';
            } else {
                $spoiler_link = '';
                $spoiler_flag = 'n';
            }
            $sql = "INSERT INTO races (raceSlug, raceStart, raceMode, raceSeed, raceHash, raceDescription, raceIsTeam, raceFromRacetime, raceIsSpoiler, raceSpoilerLink) VALUES (?, ?, ?, ?, ?, ?, ?, 'y', ?, ?)";
            $pdo->prepare($sql)->execute([$slug_list[$i], $race_start, $race_mode, $race_seed, $race_hash, $info_user, $team_flag, $spoiler_flag, $spoiler_link]);
            $racePlayerCount = count($url_json['entrants']);
            for($j=0;$j<$racePlayerCount;$j++) {
                $playerRacetimeID = $url_json['entrants'][$j]['user']['id'];
                if ($playerRacetimeID == null) {
                    $playerRacetimeID = generateRacerID();
                }
                $playerName = $url_json['entrants'][$j]['user']['name'];
                if ($playerName == null) {
                    $playerName = 'Unknown User ' . random_int(1, 999999);
                }
                $playerDiscriminator = $url_json['entrants'][$j]['user']['discriminator'];
                if($url_json['team_race'] == true) {
                    $playerTeam = $url_json['entrants'][$j]['team']['name'];
                } else {
                    $playerTeam = '';
                }
                $playerRealTime = $url_json['entrants'][$j]['finish_time'];
                if($playerRealTime == '') {
                    $playerRealTime = 20000;
                    $playerIsForfeit = 'y';
                } else {
                    $playerRealTime = convertFinishTime($playerRealTime);
                    $playerIsForfeit = 'n';
                }
                $playerComment = $url_json['entrants'][$j]['comment'];
                if($playerComment == null) {
                    $playerComment = '';
                }
                $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = ? AND racerRacetimeID = ?");
                $stmt->execute([$slug_list[$i], $playerRacetimeID]);
                $resultExists = $stmt->fetchColumn();
                if(! $resultExists) {
                    $sql = "INSERT INTO results (raceSlug, racerRacetimeID, racerTeam, racerRealTime, racerComment, racerForfeit, racerFromRacetime) VALUES (?, ?, ?, ?, ?, ?, 'y')";
                    $pdo->prepare($sql)->execute([$slug_list[$i], $playerRacetimeID, $playerTeam, $playerRealTime, $playerComment, $playerIsForfeit]);
                    $stmt = $pdo->prepare("SELECT id FROM racerinfo WHERE racetimeID = ?");
                    $stmt->execute([$playerRacetimeID]);
                    $racetimeIDExists = $stmt->fetchColumn();
                    if(! $racetimeIDExists) {
                        $sql = "INSERT INTO racerinfo (racetimeID, rtgg_name, rtgg_discriminator) VALUES (?, ?, ?)";
                        $pdo->prepare($sql)->execute([$playerRacetimeID, $playerName, $playerDiscriminator]);
                    } else {
                        $sql = "UPDATE racerinfo SET rtgg_name = ?, rtgg_discriminator = ? WHERE racetimeID = ?";
                        $pdo->prepare($sql)->execute([$playerName, $playerDiscriminator, $playerRacetimeID]);
                    }
                }
            }
        }
    }
}
