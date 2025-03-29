<?php

// Variables from processAsync - newSeed, newMode, newHash, newSpoiler (newSpoilerLog), newTeam, newLoginRequired, newVODRequired, newtournament_mode

$newSeed = $_POST['newSeed'];
$newMode = $_POST['newMode'];
$newHash = $_POST['newHash'];
if (isset($_POST['newDescription'])) {
    $newDescription = $_POST['newDescription'];
} else {
    $newDescription = null;
}
if (isset($_POST['newSpoiler'])) {
    $newSpoiler = $_POST['newSpoiler'];
} else {
    $newSpoiler = 'n';
}
if (isset($_POST['newSpoilerLog'])) {
    $newSpoilerLog = $_POST['newSpoilerLog'];
} else {
    $newSpoilerLog = null;
}
if (isset($_POST['newTeam'])) {
    $newTeam = $_POST['newTeam'];
} else {
    $newTeam = 'n';
}
if (isset($_POST['newLoginRequired'])) {
    $newLoginRequired = $_POST['newLoginRequired'];
} else {
    $newLoginRequired = 'n';
}
if (isset($_POST['newVODRequired'])) {
    $newVODRequired = $_POST['newVODRequired'];
} else {
    $newVODRequired = 'n';
}
if (isset($_POST['newAllowResultEdits'])) {
    $newAllowResultEdits = 'n';
} else {
    $newAllowResultEdits = 'y';
}
if (isset($_POST['newtournament_seed'])) {
    $newtournament_seed = 'y';
} else {
    $newtournament_seed = 'n';
}

$newCreatedBy = $_SESSION['userid'];

$newSlug = generateRaceSlug();

while (1 == 1) {
    $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
    $stmt->bindValue(':raceSlug', $newSlug, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetchColumn();
    if (! $row) {
        break;
    } else {
        $newSlug = generateRaceSlug();
    }
}

$stmt = $pdo->prepare("INSERT INTO races (raceSlug, raceStart, raceMode, raceSeed, raceHash, raceDescription, raceIsTeam, raceIsSpoiler, raceSpoilerLink, raceFromRacetime, vodRequired, loginRequired, allowResultEdits, tournament_seed, createdBy) VALUES (:raceSlug, NOW(), :raceMode, :raceSeed, :raceHash, :raceDescription, :raceIsTeam, :raceIsSpoiler, :raceSpoilerLink, 'n', :vodRequired, :loginRequired, :allowResultEdits, :tournament, :createdBy)");
$stmt->bindValue(':raceSlug', $newSlug, PDO::PARAM_STR);
$stmt->bindValue(':raceMode', $newMode, PDO::PARAM_STR);
$stmt->bindValue(':raceSeed', $newSeed, PDO::PARAM_STR);
$stmt->bindValue(':raceHash', $newHash, PDO::PARAM_STR);
$stmt->bindValue(':raceDescription', $newDescription, PDO::PARAM_STR);
$stmt->bindValue(':raceIsTeam', $newTeam, PDO::PARAM_STR);
$stmt->bindValue(':raceIsSpoiler', $newSpoiler, PDO::PARAM_STR);
$stmt->bindValue(':raceSpoilerLink', $newSpoilerLog, PDO::PARAM_STR);
$stmt->bindValue(':vodRequired', $newVODRequired, PDO::PARAM_STR);
$stmt->bindValue(':loginRequired', $newLoginRequired, PDO::PARAM_STR);
$stmt->bindValue(':allowResultEdits', $newAllowResultEdits, PDO::PARAM_STR);
$stmt->bindParam(':tournament', $newtournament_seed, PDO::PARAM_STR);
$stmt->bindValue(':createdBy', $newCreatedBy, PDO::PARAM_INT);
$stmt->execute();

$stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
$stmt->bindValue(':raceSlug', $newSlug, PDO::PARAM_STR);
$stmt->execute();
$newID = $stmt->fetchColumn();
$notes = 'Async Accepted! View your async here:<br /><a href="' . $domain . '/async/' . $newID . '">' . $domain . '/async/' . $newID . '</a>';
echo '        <table class="submitAsync">' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
echo '                <tr><td colspan="2">' . $notes . '</td><tr>' . PHP_EOL;
echo '                <tr><th class="rightAlign">Link to Seed:</th><td>' . $newSeed . '</td></tr>' . PHP_EOL;
echo '                <tr><th class="rightAlign">Mode:</th><td>' . $newMode . '</td></tr>' . PHP_EOL;
echo '                <tr><th class="rightAlign">Hash:</th><td>' . hashToTable($newHash) . '</td></tr>' . PHP_EOL;
if ($newDescription != '') {
    echo '                <tr><th class="rightAlign">Description:</th><td>' . $newDescription . '</td></tr>' . PHP_EOL;
}
$toggleModes = '';
if ($newSpoiler == 'y') {
    $toggleModes .= 'Spoiler';
}
if ($newTeam == 'y') {
    if ($toggleModes) {
        $toggleModes .= ' - Team/Co-op';
    } else {
        $toggleModes .= 'Team/Co-op';
    }
}
if ($newLoginRequired == 'y') {
    if ($toggleModes) {
        $toggleModes .= ' - Login Required';
    } else {
        $toggleModes .= 'Login Required';
    }
}
if ($newVODRequired == 'y') {
    if ($toggleModes) {
        $toggleModes .= ' - VOD Required';
    } else {
        $toggleModes .= 'VOD Required';
    }
}
if ($newAllowResultEdits == 'n') {
    if ($toggleModes) {
        $toggleModes .= ' - Edits Disallowed';
    } else {
        $toggleModes .= 'Edits Disallowed';
    }
}
if ($newtournament_seed == 'y') {
    if ($toggleModes) {
        $toggleModes .= ' - Tournament Seed';
    } else {
        $toggleModes .= 'Tournament Seed';
    }
}
if ($toggleModes != '') {
    echo '                <tr><td colspan="2" class="centerAlign">' . $toggleModes . '</td></tr>' . PHP_EOL;
}
if ($newSpoiler == 'y') {
    echo '                <tr><td>Link to Spoiler Log: </td><td>' . $newSpoilerLog . '</td></tr>' . PHP_EOL;
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
require_once ('../src/inputAsync.php');
