<?php

// Variables from processAsync - seed, mode, hash, spoiler (spoiler_log), team, login_required, vod_required, tournament_mode

$seed = $_POST['seed'];
$mode = $_POST['mode'];
$hash = $_POST['hash'];
if (isset($_POST['description'])) {
    $description = $_POST['description'];
} else {
    $description = null;
}
if (isset($_POST['spoiler'])) {
    $spoiler = $_POST['spoiler'];
} else {
    $spoiler = 'n';
}
if (isset($_POST['spoiler_log'])) {
    $spoiler_log = $_POST['spoiler_log'];
} else {
    $spoiler_log = null;
}
if (isset($_POST['team'])) {
    $team = $_POST['team'];
} else {
    $team = 'n';
}
if (isset($_POST['login_required'])) {
    $login_required = $_POST['login_required'];
} else {
    $login_required = 'n';
}
if (isset($_POST['vod_required'])) {
    $vod_required = $_POST['vod_required'];
} else {
    $vod_required = 'n';
}
if (isset($_POST['edits_allowed'])) {
    $edits_allowed = 'n';
} else {
    $edits_allowed = 'y';
}
if (isset($_POST['tournament_seed'])) {
    $tournament_seed = 'y';
} else {
    $tournament_seed = 'n';
}

$created_by = $_SESSION['userid'];

$slug = generateRaceSlug();

while (1 == 1) {
    $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
    $stmt->bindValue(':raceSlug', $slug, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetchColumn();
    if (! $row) {
        break;
    } else {
        $slug = generateRaceSlug();
    }
}

$stmt = $pdo->prepare("INSERT INTO races (raceSlug, raceStart, raceMode, raceSeed, raceHash, raceDescription, raceIsTeam, raceIsSpoiler, raceSpoilerLink, raceFromRacetime, vodRequired, loginRequired, allowResultEdits, tournament_seed, createdBy) VALUES (:raceSlug, NOW(), :raceMode, :raceSeed, :raceHash, :raceDescription, :raceIsTeam, :raceIsSpoiler, :raceSpoilerLink, 'n', :vodRequired, :loginRequired, :allowResultEdits, :tournament, :createdBy)");
$stmt->bindValue(':raceSlug', $slug, PDO::PARAM_STR);
$stmt->bindValue(':raceMode', $mode, PDO::PARAM_STR);
$stmt->bindValue(':raceSeed', $seed, PDO::PARAM_STR);
$stmt->bindValue(':raceHash', $hash, PDO::PARAM_STR);
$stmt->bindValue(':raceDescription', $description, PDO::PARAM_STR);
$stmt->bindValue(':raceIsTeam', $team, PDO::PARAM_STR);
$stmt->bindValue(':raceIsSpoiler', $spoiler, PDO::PARAM_STR);
$stmt->bindValue(':raceSpoilerLink', $spoiler_log, PDO::PARAM_STR);
$stmt->bindValue(':vodRequired', $vod_required, PDO::PARAM_STR);
$stmt->bindValue(':loginRequired', $login_required, PDO::PARAM_STR);
$stmt->bindValue(':allowResultEdits', $edits_allowed, PDO::PARAM_STR);
$stmt->bindValue(':tournament', $tournament_seed, PDO::PARAM_STR);
$stmt->bindValue(':createdBy', $created_by, PDO::PARAM_INT);
$stmt->execute();

$stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
$stmt->bindValue(':raceSlug', $slug, PDO::PARAM_STR);
$stmt->execute();
$race_id = $stmt->fetchColumn();
$notes = 'Async Accepted! View your async here:<br /><a href="' . $domain . '/async/' . $race_id . '">' . $domain . '/async/' . $race_id . '</a>';
echo '        <table class="submitAsync">' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
echo '                <tr><td colspan="2">' . $notes . '</td><tr>' . PHP_EOL;
echo '                <tr><th class="rightAlign">Link to Seed:</th><td>' . $seed . '</td></tr>' . PHP_EOL;
echo '                <tr><th class="rightAlign">Mode:</th><td>' . $mode . '</td></tr>' . PHP_EOL;
echo '                <tr><th class="rightAlign">Hash:</th><td>' . hashToTable($hash) . '</td></tr>' . PHP_EOL;
if ($description != '') {
    echo '                <tr><th class="rightAlign">Description:</th><td>' . $description . '</td></tr>' . PHP_EOL;
}
$toggleModes = '';
if ($spoiler == 'y') {
    $toggleModes .= 'Spoiler';
}
if ($team == 'y') {
    if ($toggleModes) {
        $toggleModes .= ' - Team/Co-op';
    } else {
        $toggleModes .= 'Team/Co-op';
    }
}
if ($login_required == 'y') {
    if ($toggleModes) {
        $toggleModes .= ' - Login Required';
    } else {
        $toggleModes .= 'Login Required';
    }
}
if ($vod_required == 'y') {
    if ($toggleModes) {
        $toggleModes .= ' - VOD Required';
    } else {
        $toggleModes .= 'VOD Required';
    }
}
if ($edits_allowed == 'n') {
    if ($toggleModes) {
        $toggleModes .= ' - Edits Disallowed';
    } else {
        $toggleModes .= 'Edits Disallowed';
    }
}
if ($tournament_seed == 'y') {
    if ($toggleModes) {
        $toggleModes .= ' - Tournament Seed';
    } else {
        $toggleModes .= 'Tournament Seed';
    }
}
if ($toggleModes != '') {
    echo '                <tr><td colspan="2" class="centerAlign">' . $toggleModes . '</td></tr>' . PHP_EOL;
}
if ($spoiler == 'y') {
    echo '                <tr><td>Link to Spoiler Log: </td><td>' . $spoiler_log . '</td></tr>' . PHP_EOL;
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
require_once ('../src/inputAsync.php');
