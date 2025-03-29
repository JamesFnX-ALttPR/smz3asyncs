<?php

//Determine if logged in account is an admin
$stmt = $pdo->prepare("SELECT is_admin FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$isAdmin = $stmt->fetchColumn();

//Display asyncs that the user has created and give options for editing
$rowCounter = 0;
echo '        <table class="searchResults sortable">' . PHP_EOL;
echo '            <caption class="searchResults">Series Created By ' . $_SESSION['displayName'] . '</caption>' . PHP_EOL;
if ($isAdmin == 'y') { //Admins can edit *all* submitted series
    $stmt = $pdo->prepare("SELECT * FROM series");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM series WHERE createdBy = :createdBy");
    $stmt->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
}
echo '                <tr><th>Name</th><th>Description</th><th>Number of Races</th><th>Edit</th></tr>' . PHP_EOL;
while($row = $stmt->fetch()) {
    $rowCounter++;
    if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    $seriesID = $row['id'];
    $seriesName = $row['seriesName'];
    $seriesDescription = $row['seriesDescription'];
    $seriesMembers = $row['seriesMembers'];
    if ($seriesMembers == null) {
        $seriesCount = 0;
    } else {
        $memberArray = explode(', ', $seriesMembers);
        $seriesCount = count($memberArray);
    }
    echo $startOfRow . '<td><a href="' . $domain . '/series/' . $seriesID . '">' . $seriesName . '</a></td><td>' . $seriesDescription . '</td><td>' . $seriesCount . '</td><td><a href="' . $domain . '/editseries/' . $seriesID . '">Edit Series</a></td></tr>' . PHP_EOL;
}
echo '        </table><br /><hr />' . PHP_EOL;

require_once ('../src/inputSeries.php');