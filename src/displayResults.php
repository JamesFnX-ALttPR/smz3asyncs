<?php
echo '        <div class="asyncTopRow">Results for ';
if($race_from_racetime == 'y') {
    echo '<a target="_blank" href="https://racetime.gg/smz3/' . $race_slug . '">' . $race_slug . '</a></div>' . PHP_EOL;
} else {
    echo $race_slug . '</div>' . PHP_EOL;
}
echo '        <div class="asyncMiddle">Mode: ' . $race_mode . '<br />' . PHP_EOL;
echo '        ' . $race_description . '<br />' . PHP_EOL;
echo '        Seed Link - <a target="_blank" href="' . $race_seed . '">' . $race_seed . '</a> - Hash: ' . hashToTable($race_hash) . '<br />' . PHP_EOL;
if ($race_from_racetime == 'n') {
    echo '        <span class="new">Created By ' . $race_creator . '</span><br />' . PHP_EOL;
}
if($race_team_flag == 'n') {
    $stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = :raceSlug');
    $stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $racer_count = $stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = :raceSlug AND racerForfeit = "n"');
    $stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $finisher_count = $stmt->fetchColumn();
    echo '        Participants: ';
} else {
    $stmt = $pdo->prepare('SELECT count(distinct(racerTeam)) FROM results WHERE raceSlug = :raceSlug');
    $stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $racer_count = $stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT count(distinct(racerTeam)) FROM results WHERE raceSlug = :raceSlug AND racerForfeit = "y"');
    $stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $forfeit_count = $stmt->fetchColumn();
    $finisher_count = $racer_count - $forfeit_count;
    echo '        Teams: ';
}
$stmt = $pdo->prepare("SELECT count(1) FROM results WHERE raceSlug = :raceSlug AND racerCheckCount IS NOT NULL");
$stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
$stmt->execute();
$check_count = $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = :raceSlug AND racerComment IS NOT NULL');
$stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
$stmt->execute();
$comment_count = $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = :raceSlug AND racerVODLink IS NOT NULL');
$stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
$stmt->execute();
$vod_count = $stmt->fetchColumn();
echo $racer_count . ' - Finishers: ' . $finisher_count . '<br />';
if($race_team_flag == 'n') {
    $stmt = $pdo->prepare("SELECT AVG(racerRealTime) FROM results WHERE raceSlug = :raceSlug AND racerForfeit = 'n'");
    $stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $race_average = $stmt->fetchColumn();
    if ($race_average) {
        $race_average = round($race_average);
        echo '        Average Finish: ' . gmdate('G:i:s', $race_average);
    }
    if($check_count > 0) {
        $stmt = $pdo->prepare("SELECT AVG(racerCheckCount) FROM results WHERE raceSlug = :raceSlug AND racerCheckCount IS NOT NULL AND racerCheckCount != 0 AND racerForfeit = 'n'");
        $stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
        $stmt->execute();
        $cr_average = round($stmt->fetchColumn());
        echo ' - Average Collection Rate: ' . $cr_average;
    }
    echo '</div><br />' . PHP_EOL;
    echo '        <hr />' . PHP_EOL;
    echo '        <table class="raceResults">' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    echo '                <tr><th>Place</th><th>Name</th><th>Finish Time</th>';
    if($check_count > 0) {
        echo '<th>Collection Rate</th>';
    }
    if($vod_count > 0) {
        echo '<th>Link to VOD</th>';
    }
    echo '</tr>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerForfeit = 'n' ORDER BY racerRealTime");
    $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $rowCount = 0;
    while($row = $stmt->fetch()) {
        $result_id = $row['id'];
        require ('../includes/result_info.php');
        $rowCount++;
        if($rowCount % 2 == 0) {
            if($result_from_racetime == 'n' && $race_from_racetime == 'y') {
                echo '                <tr class="even new">';
            } else {
                echo '                <tr class="even">';
            }
        } else {
            if($result_from_racetime == 'n' && $race_from_racetime == 'y') {
                echo '                <tr class="odd new">';
            } else {
                echo '                <tr class="odd">';
            }
        }
        echo '<td class="place' . $rowCount . '">' . $rowCount . '</td><td>' . htmlentities($racer_name, ENT_QUOTES, "UTF-8", false);
        if($comment_count > 0 && $racer_comment != null) {
            echo ' <span class="comment" title = "' . htmlentities($racer_comment, ENT_QUOTES, "UTF-8", false) . '">[Comment]</span>';
        }
        echo '</td><td>' . gmdate('G:i:s', $racer_time) . '</td>';
        if($check_count > 0) {
            if($racer_collection_rate != null) {
                echo '<td>' . $racer_collection_rate . '</td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        if($vod_count > 0) {
            if($racer_vod != null) {
                echo '<td><a target="_blank" href="' . $racer_vod . '">Link to VOD</a></td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        echo '</tr>' . PHP_EOL;
    }
    $sql = "SELECT id FROM results WHERE raceSlug = :slug AND racerForfeit = 'y'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    while($row = $stmt->fetch()) {
        $result_id = $row['id'];
        require ('../includes/result_info.php');
        $rowCount++;
        if($rowCount % 2 == 0) {
            if($result_from_racetime == 'n' && $race_from_racetime == 'y') {
                echo '                <tr class="even new">';
            } else {
                echo '                <tr class="even">';
            }
        } else {
            if($result_from_racetime == 'n' && $race_from_racetime == 'y') {
                echo '                <tr class="odd new">';
            } else {
                echo '                <tr class="odd">';
            }
        }
        echo '<td class="ff">FF</td><td>' . htmlentities($racer_name, ENT_QUOTES, "UTF-8", false);
        if($comment_count > 0 && $racer_comment != null) {
            echo ' <span class="comment" title = "' . htmlentities($racer_comment, ENT_QUOTES, "UTF-8", false) . '">[Comment]</span>';
        }
        echo '</td><td>Forfeit</td>';
        if($check_count > 0) {
            echo '<td>FF</td>';
        }
        if($vod_count > 0) {
            echo '<td>Forfeit</td>';
        }
        echo '</tr>' . PHP_EOL;
    }
} else {
    $temp_table_hash = createCallbackLink();
    require_once ('../src/populateTempTable.php');
    $average_sql = "SELECT AVG(averageTime) FROM temp_" . $temp_table_hash . " WHERE teamForfeit = 'n'";
    $stmt = $pdo->prepare($average_sql);
    $stmt->execute();
    $race_average = $stmt->fetchColumn();
    if ($race_average) {
        $race_average = round($race_average);
        echo '        Average Finish: ' . gmdate('G:i:s', $race_average);
    }
    if($check_count > 0) {
        $stmt = $pdo->prepare("SELECT AVG(racerCheckCount) FROM results WHERE raceSlug = :raceSlug AND racerCheckCount IS NOT NULL AND racerCheckCount != 0 AND racerForfeit = 'n'");
        $stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
        $stmt->execute();
        $cr_average = round($stmt->fetchColumn());
        echo ' - Average Collection Rate: ' . $cr_average;
    }
    echo '</div><br />' . PHP_EOL;
    echo '        <hr />' . PHP_EOL;
    echo '        <table class="raceResults">' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    echo '                <tr><th>Place</th><th>Name</th><th>Real Time</th>';
    if($check_count > 0) {
        echo '<th>Collection Rate</th>';
    }
    if($vod_count > 0) {
        echo '<th>Link to VOD</th>';
    }
    echo '</tr>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $rowCount = 0;
    $stmt2 = "SELECT teamName, averageTime, averageCR FROM temp_" . $temp_table_hash . " WHERE teamForfeit = 'n' ORDER BY averageTime";
    $sql2 = $pdo->prepare($stmt2);
    $sql2->execute();
    while($teamRow = $sql2->fetch()) {
        $rowCount++;
        $team_name = $teamRow['teamName'];
        $teamAverageTime = round($teamRow['averageTime'], 0);
        if($check_count > 0 && $teamRow['averageCR'] != null) {
            $teamAverageCR = round($teamRow['averageCR'], 0);
        }
        if($rowCount % 2 == 0) {
            echo '                <tr class="team even">';
        } else {
            echo '                <tr class="team odd">';
        }
        echo '<td class="place' . $rowCount . '">' . $rowCount . '</td><td>' . $team_name . '</td><td>' . gmdate('G:i:s', $teamAverageTime) . '</td>';
        if($check_count > 0 && $teamRow['averageCR'] != null) {
            echo '<td>' . $teamAverageCR . '</td>';
        } elseif ($check_count > 0 && $teamRow['averageCR'] == null) {
            echo '<td>N/A</td>';
        }
        if($vod_count > 0) {
            echo '<td></td>';
        }
        echo '</tr>' . PHP_EOL;
        $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerTeam = :team ORDER BY racerRealTime");
        $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
        $stmt->bindValue(':team', $team_name, PDO::PARAM_STR);
        $stmt->execute();
        while($row = $stmt->fetch()) {
            $result_id = $row['id'];
            require ('../includes/result_info.php');
            if($rowCount % 2 == 0) {
                if($result_from_racetime == 'n' && $race_from_racetime == 'y') {
                    echo '                <tr class="even new">';
                } else {
                    echo '                <tr class="even">';
                }
            } else {
                if($result_from_racetime == 'n' && $race_from_racetime == 'y') {
                    echo '                <tr class="odd new">';
                } else {
                    echo '                <tr class="odd">';
                }
            }
            echo '<td class="place' . $rowCount . '"></td><td class="teamRacerName">' . htmlentities($racer_name, ENT_QUOTES, "UTF-8", false);
            if($comment_count > 0 && $racer_comment != null) {
                echo ' <span class="comment" title = "' . htmlentities($racer_comment, ENT_QUOTES, "UTF-8", false) . '">[Comment]</span>';
            }
            echo '</td><td class="teamRacerData">' . gmdate('G:i:s', $racer_time) . '</td>';
            if($check_count > 0) {
                if($racer_collection_rate != null) {
                    echo '<td class="teamRacerData">' . $racer_collection_rate . '</td>';
                } else {
                    echo '<td class="teamRacerData">N/A</td>';
                }
            }
            if($vod_count > 0) {
                if($racer_vod != null) {
                    echo '<td><a target=_blank" href="' . $racer_vod . '">Link to VOD</a></td>';
                } else {
                    echo '<td>N/A</td>';
                }
            }
            echo '</tr>' . PHP_EOL;
        }
    }
    $stmt2 = "SELECT teamName FROM temp_" . $temp_table_hash . " WHERE teamForfeit = 'y' ORDER BY teamName";
    $sql2 = $pdo->prepare($stmt2);
    $sql2->execute();
    while($teamRow = $sql2->fetch()) {
        $rowCount++;
        $team_name = $teamRow['teamName'];
        if($rowCount % 2 == 0) {
            echo '                <tr class="team even">';
        } else {
            echo '                <tr class="team odd">';
        }
        echo '<td class="ff">FF</td><td>' . $team_name . '</td><td>Forfeit</td>';
        if($check_count > 0) {
            echo '<td>FF</td>';
        }
        if($vod_count > 0) {
            echo '<td>Forfeit</td>';
        }
        echo '</tr>' . PHP_EOL;
        $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerTeam = :team ORDER BY racerRealTime");
        $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
        $stmt->bindValue(':team', $team_name, PDO::PARAM_STR);
        $stmt->execute();
        while($row = $stmt->fetch()) {
            if($rowCount % 2 == 0) {
                if($result_from_racetime == 'n' && $race_from_racetime == 'y') {
                    echo '                <tr class="even new">';
                } else {
                    echo '                <tr class="even">';
                }
            } else {
                if($result_from_racetime == 'n' && $race_from_racetime == 'y') {
                    echo '                <tr class="odd new">';
                } else {
                    echo '                <tr class="odd">';
                }
            }
            echo '<td class="ff"></td><td class="teamRacerName">' . htmlentities($racer_name, ENT_QUOTES, "UTF-8", false);
            if($comment_count > 0 && $racer_comment != null) {
                echo ' <span class="comment" title = "' . htmlentities($racer_comment, ENT_QUOTES, "UTF-8", false) . '">[Comment]</span>';
            }
            echo '</td><td class="teamRacerData">Forfeit</td>';
            if($check_count > 0) {
                echo '<td class="teamRacerData">FF</td>';
            }
            if($vod_count > 0) {
                echo '<td>Forfeit</td>';
            }
            echo PHP_EOL;
        }
    }
    $sql = "DROP TABLE temp_" . $temp_table_hash;
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
if (isset($_SESSION['userid']) && isset($race_created_by)) {
    if($_SESSION['userid'] == $race_created_by || $admin_flag == 'y') {
        echo '                <tr><td colspan="'; if ($check_count > 0 && $vod_count > 0) { echo '5'; } elseif ($check_count > 0 || $vod_count > 0 ) { echo '4'; } else { echo '3'; } echo '" class="submitButton"><form method="post" action=""><input type="Submit" class="submitButton" value="Download Results as .CSV" /></form></td></tr>' . PHP_EOL;
    }
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
