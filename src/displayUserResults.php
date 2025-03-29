<?php

//Determine if logged in account is an admin
$stmt = $pdo->prepare("SELECT is_admin FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$isAdmin = $stmt->fetchColumn();

//Display asyncs that the user has created and give options for editing
$rowCounter = 0;
echo '        <table class="searchResults sortable">' . PHP_EOL;
echo '            <caption class="searchResults">Results Submitted By ' . $_SESSION['displayName'] . '</caption>' . PHP_EOL;
if ($isAdmin == 'y') { //Admins can edit *all* submitted asyncs
    $stmt = $pdo->prepare("SELECT * FROM results WHERE racerFromRacetime = 'n'");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM results WHERE enteredBy = :enteredBy");
    $stmt->bindValue(':enteredBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
}
echo '                <tr><th>Race Room</th><th>Name</th><th>Team</th><th>Real Time</th><th>In-Game Time</th><th><span title="Collection Rate">CR</span></th><th>Comments</th><th>Link to VOD</th><th>Edit</th><th><form method="post" action="' . $domain . '/deleteresult" id="deleteresult"><input type="submit" class="submitButton" form="deleteresult" value="Delete Results" /></form></th></tr>' . PHP_EOL;
while($row = $stmt->fetch()) {
    $rowCounter++;
    if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    $resultID = $row['id'];
    $raceSlug = $row['raceSlug'];
    $racetimeID = $row['racerRacetimeID'];
    $racerTeam = $row['racerTeam'];
    $racerRealTime = $row['racerRealTime'];
    $racerCR = $row['racerCheckCount'];
    $racerComment = $row['racerComment'];
    $racerForfeit = $row['racerForfeit'];
    $racerVODLink = $row['racerVODLink'];
    $enteredBy = $row['enteredBy'];
    $stmt2 = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :racetimeID");
    $stmt2->bindValue(':racetimeID', $racetimeID, PDO::PARAM_STR);
    $stmt2->execute();
    $racerName = $stmt2->fetchColumn();
    $stmt2 = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
    $stmt2->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt2->execute();
    $raceID = $stmt2->fetchColumn();
    echo $startOfRow . '<td><a href="' . $domain . '/results/' . $raceID . '">' . $raceSlug . '</a></td><td>' . $racerName . '</td><td>' . $racerTeam . '</td><td>';
    if ($racerForfeit == 'y') {
        echo 'FF</td>';
    } else {
        echo gmdate('G:i:s', $racerRealTime) . '</td>';
    }
    echo '<td>' . $racerCR . '<td>' . $racerComment . '</td><td>' . $racerVODLink . '</td><td><a href="' . $domain . '/editresult/' . $resultID .'">Edit Result</a></td>';
    echo '<td><input type="checkbox" form="deleteresult" id="result_' . $resultID . '" name="result_' . $resultID . '" /><label for="result_' . $resultID . '"> Check To Delete</label></td></tr>' . PHP_EOL;
}
echo '        </table>' . PHP_EOL;