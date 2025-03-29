<?php
$seriesID = $_GET['seriesID'];
$stmt = $pdo->prepare("SELECT seriesName, seriesDescription, seriesMembers FROM series WHERE id = :id");
$stmt->bindValue(':id', $seriesID, PDO::PARAM_INT);
$stmt->execute();
$rslt = $stmt->fetch();
$name = $rslt['seriesName'];
$desc = $rslt['seriesDescription'];
$members = $rslt['seriesMembers'];
echo '        <div class="asyncMiddle">Races in ' . $name . '</div><br /><div class="asyncBottom">' . $desc . '</div>' . PHP_EOL;
echo '        <table class="searchResults sortable">' . PHP_EOL;
echo '            <thead>' . PHP_EOL;
echo '                <tr><th>Date (UTC)</th><th>Mode</th><th>Description</th><th>Racetime Room</th><th>Seed</th><th>Hash</th><th>Participants</th><th>Async</th><th>Results</th></tr>' . PHP_EOL;
echo '            </thead>' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
$memberArray = explode(', ', $members);
$rowCounter = 0;
foreach($memberArray as $raceID) {
    $raceID = intval($raceID);
    $rowCounter++;
    if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    $stmt = $pdo->prepare("SELECT * FROM races WHERE id = :id");
    $stmt->bindValue(':id', $raceID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    $raceSlug = $row['raceSlug'];
    $raceStart = $row['raceStart'];
    $raceMode = $row['raceMode'];
    $raceSeed = $row['raceSeed'];
    $raceHash = $row['raceHash'];
    if(strlen($row['raceDescription']) > 63) { $raceDescription = substr($row['raceDescription'], 0, 60) . '...'; } else { $raceDescription = $row['raceDescription']; }
    $raceIsTeam = $row['raceIsTeam'];
    $raceIsSpoiler = $row['raceIsSpoiler'];
    $raceSpoilerLink = $row['raceSpoilerLink'];
    $raceFromRacetime = $row['raceFromRacetime'];
    if($raceIsTeam == 'y') {
        $raceDescription = 'CO-OP/TEAM - ' . $raceDescription;
        $teamCountSQL = $pdo->prepare("SELECT COUNT(DISTINCT racerTeam) FROM results WHERE raceSlug = ?");
        $teamCountSQL->execute([$raceSlug]);
        $participantCount = $teamCountSQL->fetchColumn();
    } else {
        $playerCountSQL = $pdo->prepare("SELECT COUNT(DISTINCT racerRacetimeID) FROM results WHERE raceSlug = ?");
        $playerCountSQL->execute([$raceSlug]);
        $participantCount = $playerCountSQL->fetchColumn();
    }
    if($raceIsSpoiler == 'y') {
        if($raceDescription == '') {
            $raceDescription = '<a target="_blank" href="' . $raceSpoilerLink . '">Link to Spoiler</a>';
        } else {
            $raceDescription = $raceDescription . ' - <a target="_blank" href="' . $raceSpoilerLink . '">Link to Spoiler</a>';
        }
    }
    echo $startOfRow . '<td>' . $raceStart . '</td><td>' . $raceMode . '</td><td>' . $raceDescription . '</td><td>';
    if ($raceFromRacetime == 'y' ) {
        echo '<a target="_blank" href="https://racetime.gg/smz3/' . $raceSlug . '">';
    }
    echo $raceSlug;
    if ($raceFromRacetime == 'y') {
        echo '</a>';
    }
    echo '</td><td><a target="_blank" href="' . $raceSeed . '">Download Seed</a></td><td>' . hashToTable($raceHash) . '</td><td>' . $participantCount . '<td><a href="' . $domain . '/async/' . $raceID . '">Submit Async</a></td><td><a href="' . $domain . '/results/' . $raceID . '">View Results</a></td></tr>' . PHP_EOL;        
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
