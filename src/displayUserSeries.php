<?php

//Determine if logged in account is an admin
$stmt = $pdo->prepare("SELECT admin_flag FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$isAdmin = $stmt->fetchColumn();

//Display asyncs that the user has created and give options for editing
$rowCounter = 0;
echo '        <table class="searchResults sortable">' . PHP_EOL;
echo '            <caption class="searchResults">Series Created By ' . $_SESSION['display_name'] . '</caption>' . PHP_EOL;
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
    $series_name = $row['series_name'];
    $series_description = $row['series_description'];
    $series_members = $row['series_members'];
    if ($series_members == null) {
        $seriesCount = 0;
    } else {
        $memberArray = explode(', ', $series_members);
        $seriesCount = count($memberArray);
    }
    echo $startOfRow . '<td><a href="' . $domain . '/series/' . $seriesID . '">' . $series_name . '</a></td><td>' . $series_description . '</td><td>' . $seriesCount . '</td><td><a href="' . $domain . '/editseries/' . $seriesID . '">Edit Series</a></td></tr>' . PHP_EOL;
}
echo '        </table><br /><hr />' . PHP_EOL;

require_once ('../src/inputSeries.php');