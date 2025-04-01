<?php

//Determine if logged in account is an admin or a series maker
$user_id = $_SESSION['userid'];
require_once ('../includes/user_info.php');

// If the user's an admin and has toggled User View, let's change their flags here
if (is_post_request() && $_POST['admin'] == 'n') {
    $admin_flag = 'n';
    $series_flag = 'y';
}
// Find out if there are any series to add races to - if so, we'll add a column and form for that
if ($admin_flag == 'y') {
    $stmt = $pdo->prepare("SELECT id FROM series");
    $stmt->execute();
    $rslt = $stmt->fetchColumn();
    if ($rslt) {
        $series_column = 'y';
    } else {
        $series_column = 'n';
    }
} elseif ($series_flag == 'y') {
    $stmt = $pdo->prepare("SELECT id FROM series WHERE createdBy = :createdBy");
    $stmt->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    $rslt = $stmt->fetchColumn();
    if ($rslt) {
        $series_column = 'y';
    } else {
        $series_column = 'n';
    }
} else {
    $series_column = 'n';
}
// If user is an admin, let's give them a toggle between seeing all the asyncs and just ones they've created.
if ($admin_flag == 'y' || (is_post_request() && $_POST['admin'] == 'n')) {
    echo '        <form id="admin_toggle" method="post" action="">' . PHP_EOL;
    echo '            <table class="searchRefine">' . PHP_EOL;
    echo '                <caption>Display Results</caption>' . PHP_EOL;
    echo '                <tr><td><input type="radio" id="admin_toggle_1" name="admin" value="y" '; if ($admin_flag == 'y') { echo 'checked '; } echo '/> Admin View - All Asyncs<br /><input type="radio" id="admin_toggle_2" name="admin" value="n" '; if ($admin_flag == 'n') { echo 'checked '; } echo '    /> User View - Your Asyncs Only</td></tr>' . PHP_EOL;
    echo '                <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Select View" /></td></tr>' . PHP_EOL;
    echo '            </table>' . PHP_EOL;
    echo '        </form>' . PHP_EOL;
}
//Display asyncs that the user has created and give options for editing
$rowCounter = 0;
echo '        <table class="searchResults sortable">' . PHP_EOL;
echo '            <caption class="searchResults">Asyncs Created By ' . $_SESSION['display_name'] . '</caption>' . PHP_EOL;
if ($admin_flag == 'y') { //Admins can edit *all* submitted asyncs
    $stmt = $pdo->prepare("SELECT id FROM races WHERE raceFromRacetime = 'n'");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT id FROM races WHERE createdBy = :createdBy");
    $stmt->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
}
echo '                <tr><th>Date (UTC)</th><th>Mode</th><th>Description</th><th>Racetime Room</th><th>Seed</th><th>Participants</th><th>View Results</th><th>Edit</th>';
if ($series_column == 'y') {
    echo '<th><form method="post" action="' . $domain . '/addtoseries" id="addtoseries"><input type="submit" class="submitButton" form="addtoseries" value="Add to Series" /></form></th>';
}
echo '</tr>' . PHP_EOL;
while($row = $stmt->fetch()) {
    $rowCounter++;
    $race_id = $row['id'];
    require ('../includes/race_info.php');
    if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    echo $startOfRow . '<td>' . $race_date . '</td><td>' . $race_mode . '</td><td>' . $race_description_short . '</td><td>' . $race_slug . '</td><td><a target="_blank" href="' . $race_seed . '">Download Seed</a></td><td>' . $participant_count . '</td><td><a href="' . $domain . '/results/' . $race_id . '">View Results</a></td><td><a href="' . $domain . '/editasync/' . $race_id . '">Edit Async</a></td>';
    if ($series_column == 'y') {
        echo '<td><select form="addtoseries" id="seed_' . $race_id . '" name="seed_' . $race_id . '"><option value=""></option>';
        if ($admin_flag == 'y' || (is_post_request() && $_POST['admin'] == 'n')) {
            $stmt2 = $pdo->prepare("SELECT id, series_name FROM series");
        } elseif ($series_flag == 'y') {
            $stmt2 = $pdo->prepare("SELECT id, series_name FROM series WHERE createdBy = :createdBy");
            $stmt2->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
        }
        $stmt2->execute();
        while ($row2 = $stmt2->fetch()) {
            echo '<option value="' . $row2['id'] . '">' . $row2['series_name'] . '</option>';
        }
        echo '</select></td>';
    }
    echo '</tr>' . PHP_EOL;
}
echo '        </table><br /><hr />' . PHP_EOL;

require_once ('../src/inputAsync.php');