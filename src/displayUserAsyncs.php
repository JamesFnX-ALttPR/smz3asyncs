<?php

//Determine if logged in account is an admin or a series maker
$stmt = $pdo->prepare("SELECT is_admin, is_seriesMaker FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
$isAdmin = $row['is_admin'];
$isSeriesMaker = $row['is_seriesMaker'];

// Find out if there are any series to add races to - if so, we'll add a column and form for that
if ($isAdmin == 'y') {
    $stmt = $pdo->prepare("SELECT id FROM series");
    $stmt->execute();
    $rslt = $stmt->fetchColumn();
    if ($rslt) {
        $seriesColumn = 'y';
    } else {
        $seriesColumn = 'n';
    }
} elseif ($isSeriesMaker == 'y') {
    $stmt = $pdo->prepare("SELECT id FROM series WHERE createdBy = :createdBy");
    $stmt->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    $rslt = $stmt->fetchColumn();
    if ($rslt) {
        $seriesColumn = 'y';
    } else {
        $seriesColumn = 'n';
    }
} else {
    $seriesColumn = 'n';
}

//Display asyncs that the user has created and give options for editing
$rowCounter = 0;
echo '        <table class="searchResults sortable">' . PHP_EOL;
echo '            <caption class="searchResults">Asyncs Created By ' . $_SESSION['displayName'] . '</caption>' . PHP_EOL;
if ($isAdmin == 'y') { //Admins can edit *all* submitted asyncs
    $stmt = $pdo->prepare("SELECT * FROM races WHERE raceFromRacetime = 'n'");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM races WHERE createdBy = :createdBy");
    $stmt->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
}
echo '                <tr><th>Date (UTC)</th><th>Mode</th><th>Description</th><th>Racetime Room</th><th>Seed</th><th>Participants</th><th>View Results</th><th>Edit</th>';
if ($seriesColumn == 'y') {
    echo '<th><form method="post" action="' . $domain . '/addtoseries" id="addtoseries"><input type="submit" class="submitButton" form="addtoseries" value="Add to Series" /></form></th>';
}
echo '</tr>' . PHP_EOL;
while($row = $stmt->fetch()) {
    $rowCounter++;
    if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    $raceID = $row['id'];
    $raceSlug = $row['raceSlug'];
    $raceStart = $row['raceStart'];
    $raceMode = $row['raceMode'];
    $raceSeed = $row['raceSeed'];
    $raceHash = $row['raceHash'];
    $raceDescription = $row['raceDescription'];
    $raceIsTeam = $row['raceIsTeam'];
    $raceIsSpoiler = $row['raceIsSpoiler'];
    $raceSpoilerLink = $row['raceSpoilerLink'];
    if($raceIsTeam == 'y') {
        $raceDescription = 'CO-OP/TEAM - ' . $raceDescription;
        $teamCountSQL = $pdo->prepare("SELECT COUNT(DISTINCT racerTeam) FROM results WHERE raceSlug = :raceSlug");
        $teamCountSQL->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
        $teamCountSQL->execute();
        $participantCount = $teamCountSQL->fetchColumn();
    } else {
        $playerCountSQL = $pdo->prepare("SELECT COUNT(DISTINCT racerRacetimeID) FROM results WHERE raceSlug = :raceSlug");
        $playerCountSQL->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
        $playerCountSQL->execute();
        $participantCount = $playerCountSQL->fetchColumn();
    }
    if($raceIsSpoiler == 'y') {
        if($raceDescription == '') {
            $raceDescription = '<a target="_blank" href="' . $raceSpoilerLink . '">Link to Spoiler</a>';
        } else {
            $raceDescription = $raceDescription . ' - <a target="_blank" href="' . $raceSpoilerLink . '">Link to Spoiler</a>';
        }
    }
    echo $startOfRow . '<td>' . $raceStart . '</td><td>' . $raceMode . '</td><td>' . $raceDescription . '</td><td>' . $raceSlug . '</td><td><a target="_blank" href="' . $raceSeed . '">Download Seed</a></td><td>' . $participantCount . '</td><td><a href="' . $domain . '/results/' . $raceID . '">View Results</a></td><td><a href="' . $domain . '/editasync/' . $raceID . '">Edit Async</a></td>';
    if ($seriesColumn == 'y') {
        echo '<td><select form="addtoseries" id="seed_' . $raceID . '" name="seed_' . $raceID . '"><option value=""></option>';
        if ($isAdmin == 'y') {
            $stmt2 = $pdo->prepare("SELECT id, seriesName FROM series");
        } elseif ($isSeriesMaker == 'y') {
            $stmt2 = $pdo->prepare("SELECT id, seriesName FROM series WHERE createdBy = :createdBy");
            $stmt2->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
        }
        $stmt2->execute();
        while ($row2 = $stmt2->fetch()) {
            echo '<option value="' . $row2['id'] . '">' . $row2['seriesName'] . '</option>';
        }
        echo '</select></td>';
    }
    echo '</tr>' . PHP_EOL;
}
echo '        </table><br /><hr />' . PHP_EOL;

require_once ('../src/inputAsync.php');