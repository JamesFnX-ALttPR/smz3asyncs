<?php

// See if current user is enrolled in this tourney
if (isset($_SESSION['userid'])) {
    $sql = "SELECT id FROM " . $slug . "_players WHERE player_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->fetch()) {
        $enrolled = 'y';
    } else {
        $enrolled = 'n';
    }
} else {
    $enrolled = 'n';
}

// Display tourney details
echo '        <div class="asyncMiddle">' . $name . '</div><br />' . PHP_EOL;
echo '        <div class="asyncBottom">' . $description . '<br />' . PHP_EOL;
if (strtotime("now") < strtotime($startTime)) {
    echo '        Starts ' . gmdate("m-d-Y", strtotime($startTime));
    if ($maxPlayers != 0) {
        echo ' - Capped at ' . $maxPlayers . ' players';
    }
} else {
    echo '        Started ' . gmdate("m-d-Y", strtotime($startTime));
}
echo '<br />' . PHP_EOL;
echo '        <a target="_blank" href="' . $discord . '">Tournament Discord</a> - <a target="_blank" href="' . $rulesDoc . '">Tournament Rules</a><br />' . PHP_EOL;
echo '        ';
if ($opening == 'swiss') {
    echo $swissRounds . ' rounds of Swiss - Cut to Top ' . $bracketSize . ' - ';
} elseif ($opening == 'groups') {
    echo 'Groups of ' . $groupSize . ' - Cut to Top ' . $bracketSize . ' - ';
}
if ($bracketStyle == 1) {
    echo 'Single Elimination';
} elseif ($bracketStyle == 2) {
    echo 'Double Elimination';
}
echo '</div><hr />';
// List of enrolled players - if the tournament has started, post standings too
echo '        <table class="standings">' . PHP_EOL;
if (strtotime("now") < strtotime($startTime)) {
    echo '            <caption>Enrolled Players</caption>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $sql = "SELECT displayName FROM asyncusers WHERE id IN (SELECT player_id FROM " . $slug . "_players WHERE active_player = 'y') ORDER BY displayName";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        echo '                <tr><td>' . $row['displayName'] . '</td></tr>' . PHP_EOL;
    }
    echo '            </tbody>' . PHP_EOL;
} else {
    //Placeholder, standings code will go here
}
echo '        </table>' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
// If enrolled, check what matches are available, otherwise if there's time to sign up, give a signup form.
if ($enrolled == 'y' && strtotime("now") > strtotime($startTime)) {
    echo '        <table class="standings">' . PHP_EOL;
    echo '            <caption>Outstanding Matches for ' . $_SESSION['displayName'] . '</caption>' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    $sql = "SELECT round, group FROM " . $slug . "_matches WHERE (player_1 = :playerA OR player_2 = :playerB) AND winner IS NULL LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':playerA', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->bindParam(':playerB', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row['round'] == null) {
        echo '                <tr><th>Group</th><th>Opponent</th><th>Submit Results</th></tr>' . PHP_EOL;
        echo '            </thead>' . PHP_EOL;
        echo '            <tbody>' . PHP_EOL;
        $sqlInsert = "group";
    } else {
        echo '                <tr><th>Round</th><th>Opponent</th><th>Submit Results</th></tr>' . PHP_EOL;
        echo '            </thead>' . PHP_EOL;
        echo '            <tbody>' . PHP_EOL;
        $sqlInsert = "round";
    }
    $sql = "SELECT id, " . $sqlInsert . ", player_1, player_2 FROM " . $slug . "_matches WHERE (player_1 = :playerA OR player_2 = :playerB) AND winner IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':playerA', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->bindParam(':playerB', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    while ($row = $stmt->fetch())  {
        echo '                <tr><td>';
        if ($row['round'] != null) {
            echo $row['round'];
        } else {
            echo $row['group'];
        }
        echo '</td><td>';
        if ($row['player_1'] == $_SESSION['userid']) {
            $stmt2 = $pdo->prepare("SELECT displayName FROM asyncusers WHERE id = :id");
            $stmt2->bindParam(':id', $row['player_2'], PDO::PARAM_INT);
            $stmt2->execute();
            $oppName = $stmt2->fetchColumn();
            echo $oppName;
        } else {
            $stmt2 = $pdo->prepare("SELECT displayName FROM asyncusers WHERE id = :id");
            $stmt2->bindParam(':id', $row['player_1'], PDO::PARAM_INT);
            $stmt2->execute();
            $oppName = $stmt2->fetchColumn();
            echo $oppName;
        }
        echo '</td><td><a target="_blank" href="' . $domain . '/submitresult/' . $row['id'] . '">Submit Result</a></td></tr>' . PHP_EOL;
    }
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
} elseif ($enrolled == 'n' && strtotime("now") < strtotime($startTime)) {
    // Determine if there's still room in the tourney for new players
    $sql = "SELECT COUNT(id) FROM " . $slug . "_players WHERE active_player = 'y'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $playerCount = $stmt->fetchColumn();
    if ($maxPlayers == 0 || $maxPlayers > $playerCount) {
        echo '        <div class="asyncBottom">Registration is open for this tournament!<br />' . PHP_EOL;
        echo '        <form method="post" action="' . $domain . '/tourneyreg"><input type="hidden" id="slug" name="slug" value="' . $slug . '" /><input type="checkbox" id="register" name="register" required /> <label for="register">By checking this box, you consent to communications from ALttPR Asyncs and the tournament organizers, you agree to join the tournament Discord, and you agree to follow all rules provided in the tournament Rules Document.</label><br /><br />'. PHP_EOL;
        echo '        <input type="submit" class="submitButton" value="Register for Tournament" /></form></div>' . PHP_EOL;
    } elseif ( $maxPlayers < $playerCount) {
        echo '        <div class="asyncBottom">This tournament has reached capacity. You can sign up for the waitlist.<br />' . PHP_EOL;
        echo '        <form method="post" action="' . $domain . '/tourneyreg"><input type="hidden" id="waitlist" name="waitlist" value="y" /><input type="hidden" id="slug" name="slug" value="' . $slug . '" /><input type="checkbox" id="register" name="register" required /> <label for="register">By checking this box, you consent to communications from ALttPR Asyncs and the tournament organizers, you agree to join the tournament Discord, and you agree to follow all rules provided in the tournament Rules Document.</label><br /><br />'. PHP_EOL;
        echo '        <input type="submit" class="submitButton" value="Register for Tournament" /></form></div>' . PHP_EOL;
    }
}