<?php

echo '        <table class="searchResults">' . PHP_EOL;
echo '            <tr><td class="rightAlign" colspan="3"><label for="name">Series Name:</label> </td><td colspan="3"><input size="50" type="text" id="name" name="name" form="editSeries" value="' . $seriesName . '" required /></td></tr>' . PHP_EOL;
echo '            <tr><td class="rightAlign" colspan="3"><label for="desc">Series Description:</label> </td><td colspan="3"><textarea id="desc" name="desc" form="editSeries" rows="3" cols="49" required>' . $seriesDesc . '</textarea></td></tr>' . PHP_EOL;
echo '            <tr><td class="centerAlign" colspan="6"><input type="submit" class="submitButton" form="editSeries" value="Update Series Info" /></td></tr>' . PHP_EOL;
echo '            <tr><th>Date (UTC)</th><th>Mode</th><th>Description</th><th>Racetime Room</th><th>Hash</th><th><form method="post" action="' . $domain . '/editseries/' . $seriesID . '" id="editSeries"><input type="submit" class="submitButton" form="editSeries" value="Delete Checked Races" /></form></tr>' . PHP_EOL;
$memberArray = explode(', ', $seriesMembers);
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
    echo '</td><td>' . hashToTable($raceHash) . '</td>';
    echo '<td><input type="checkbox" form="editSeries" id="seed_' . $raceID . '" name="seed_' . $raceID . '" /><label for="seed_' . $raceID . '"> Check To Delete</label></td>';
    echo '</tr>' . PHP_EOL;        
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;