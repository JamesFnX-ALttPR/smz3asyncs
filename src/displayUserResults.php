<?php

//Determine if logged in account is an admin or a series maker
$user_id = $_SESSION['userid'];
require_once ('../includes/user_info.php');

// If the user's an admin and has toggled User View, let's change their flags here
if (is_post_request() && $_POST['admin'] == 'n') {
    $admin_flag = 'n';
}

// If user is an admin, let's give them a toggle between seeing all the asyncs and just ones they've created.
if ($admin_flag == 'y' || (is_post_request() && $_POST['admin'] == 'n')) {
    echo '        <form id="admin_toggle" method="post" action="">' . PHP_EOL;
    echo '            <table class="searchRefine">' . PHP_EOL;
    echo '                <caption>Display Results</caption>' . PHP_EOL;
    echo '                <tr><td><input type="radio" id="admin_toggle_1" name="admin" value="y" '; if ($admin_flag == 'y') { echo 'checked '; } echo '/> Admin View - All Results<br /><input type="radio" id="admin_toggle_2" name="admin" value="n" '; if ($admin_flag == 'n') { echo 'checked '; } echo '/> User View - Your Results Only</td></tr>' . PHP_EOL;
    echo '                <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Select View" /></td></tr>' . PHP_EOL;
    echo '            </table>' . PHP_EOL;
    echo '        </form>' . PHP_EOL;
}
//Display asyncs that the user has created and give options for editing
$rowCounter = 0;
echo '        <table class="searchResults sortable">' . PHP_EOL;
echo '            <caption class="searchResults">Results Submitted By ' . $_SESSION['display_name'] . '</caption>' . PHP_EOL;
if ($admin_flag == 'y') { //Admins can edit *all* submitted asyncs
    $stmt = $pdo->prepare("SELECT id FROM results WHERE racerFromRacetime = 'n'");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT id FROM results WHERE enteredBy = :enteredBy");
    $stmt->bindValue(':enteredBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
}
echo '                <tr><th>Race Room</th><th>Share Async</th><th>Name</th><th>Team</th><th>Real Time</th><th><span title="Collection Rate">CR</span></th><th>Comments</th><th>Link to VOD</th><th>Edit</th><th><form method="post" action="' . $domain . '/deleteresult" id="deleteresult"><input type="submit" class="submitButton" form="deleteresult" value="Delete Results" /></form></th></tr>' . PHP_EOL;
while($row = $stmt->fetch()) {
    $rowCounter++;
    $result_id = $row['id'];
    include ('../includes/result_info.php');
        if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    $stmt2 = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
    $stmt2->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
    $stmt2->execute();
    $race_id = $stmt2->fetchColumn();
    echo $startOfRow . '<td><a href="' . $domain . '/results/' . $race_id . '">' . $race_slug . '</a></td><td><a href="' . $domain . '/async/' . $race_id . '">Share Async</a></td><td>' . $racer_name . '</td><td>' . $racer_team . '</td><td>';
    if ($racer_forfeit == 'y') {
        echo 'FF</td>';
    } else {
        echo gmdate('G:i:s', $racer_time) . '</td>';
    }
    echo '<td>' . $racer_collection_rate . '<td>' . htmlentities($racer_comment, ENT_QUOTES, "UTF-8", false) . '</td><td>' . $racer_vod . '</td><td><a href="' . $domain . '/editresult/' . $result_id .'">Edit Result</a></td>';
    echo '<td><input type="checkbox" form="deleteresult" id="result_' . $result_id . '" name="result_' . $result_id . '" /><label for="result_' . $result_id . '"> Check To Delete</label></td></tr>' . PHP_EOL;
}
echo '        </table>' . PHP_EOL;