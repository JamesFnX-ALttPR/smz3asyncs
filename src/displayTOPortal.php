<?php

//Determine if logged in account is an admin
$stmt = $pdo->prepare("SELECT is_admin FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$isAdmin = $stmt->fetchColumn();

//Display tournaments that the user has created and give options for viewing/editing
$rowCounter = 0;
echo '        <table class="searchResults sortable">' . PHP_EOL;
echo '            <caption class="searchResults">Tournaments Created By ' . $_SESSION['displayName'] . '</caption>' . PHP_EOL;
if ($isAdmin == 'y') { //Admins can edit *all* submitted series
    $stmt = $pdo->prepare("SELECT * FROM tournaments");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM tournaments WHERE createdBy = :createdBy");
    $stmt->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
}
echo '                <tr><th>Name</th><th>Description</th><th>Players Enrolled</th><th>Max Players</th><th>Edit</th></tr>' . PHP_EOL;
while($row = $stmt->fetch()) {
    $rowCounter++;
    if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    $tourneyID = $row['id'];
    $tourneyName = $row['name'];
    $tourneyDesc = $row['description'];
    $tourneySlug = $row['slug'];
    $maxPlayers = $row['max_players'];
    if ($maxPlayers == 0) {
        $maxPlayers = 'Unlimited';
    }
    $sql = "SELECT COUNT(player_id) FROM " . $tourneySlug . "_players";
    $stmt2 = $pdo->prepare($sql);
    if (! $stmt2->fetchColumn()) {
        $playersEnrolled = 0;
    } else {
        $playersEnrolled = $stmt2->fetchColumn();
    }
    echo $startOfRow . '<td><a href="' . $domain . '/tourney/' . $tourneySlug . '">' . $tourneyName . '</a></td><td>' . $tourneyDesc . '</td><td>' . $playersEnrolled . '</td><td>' . $maxPlayers . '</td><td><a href="' . $domain . '/edittournament/' . $tourneySlug . '">Edit Tournament</a></td></tr>' . PHP_EOL;
}
echo '        </table><br /><hr />' . PHP_EOL;