<?php
echo '        <div class="asyncTopRow">Results for ';
if($raceFromRacetime == 'y') {
    echo '<a target="_blank" href="https://racetime.gg/smz3/' . $raceSlug . '">' . $raceSlug . '</a></div>' . PHP_EOL;
} else {
    echo $raceSlug . '</div>' . PHP_EOL;
}
echo '        <div class="asyncMiddle">Mode: ' . $raceMode . '<br />' . PHP_EOL;
if($raceIsTeam == 'y') {
    if($raceDescription == '') {
        $raceDescription = 'CO-OP/TEAM';
    } else {
        $raceDescription = 'CO-OP/TEAM - ' . $raceDescription;
    }
}
if($raceIsSpoiler == 'y') {
    if($raceDescription == '') {
        $raceDescription = '<a target="_blank" href="' . $raceSpoilerLink . '">Download Spoiler Log</a>';
    } else {
        $raceDescription = $raceDescription . ' - <a target="_blank" href="' . $raceSpoilerLink . '">Download Spoiler Log</a>';
    }
}
echo '        ' . $raceDescription . '<br />' . PHP_EOL;
echo '        Seed Link - <a target="_blank" href="' . $raceSeed . '">' . $raceSeed . '</a> - Hash: ' . hashToTable($raceHash) . '<br />' . PHP_EOL;
if($raceIsTeam == 'n') {
    $stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = :raceSlug');
    $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt->execute();
    $racerCount = $stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = :raceSlug AND racerForfeit = "n"');
    $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt->execute();
    $finisherCount = $stmt->fetchColumn();
    echo '        Participants: ';
} else {
    $stmt = $pdo->prepare('SELECT count(distinct(racerTeam)) FROM results WHERE raceSlug = :raceSlug');
    $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt->execute();
    $racerCount = $stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT count(distinct(racerTeam)) FROM results WHERE raceSlug = :raceSlug AND racerForfeit = "y"');
    $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt->execute();
    $forfeitCount = $stmt->fetchColumn();
    $finisherCount = $racerCount - $forfeitCount;
    echo '        Teams: ';
}
$stmt = $pdo->prepare("SELECT count(1) FROM results WHERE raceSlug = :raceSlug AND racerCheckCount IS NOT NULL");
$stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
$stmt->execute();
$checkCount = $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = :raceSlug AND racerComment IS NOT NULL');
$stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
$stmt->execute();
$commentCount = $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = :raceSlug AND racerVODLink IS NOT NULL');
$stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
$stmt->execute();
$vodCount = $stmt->fetchColumn();
echo $racerCount . ' - Finishers: ' . $finisherCount . '<br />';
$stmt = $pdo->prepare("SELECT AVG(racerRealTime) FROM results WHERE raceSlug = :raceSlug AND racerForfeit = 'n'");
$stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
$stmt->execute();
$raceAverage = $stmt->fetchColumn();
if ($raceAverage) {
    $raceAverage = round($raceAverage);
    echo '        Average Finish: ' . gmdate('G:i:s', $raceAverage);
}
if($checkCount > 0) {
    $stmt = $pdo->prepare("SELECT AVG(racerCheckCount) FROM results WHERE raceSlug = :raceSlug AND racerCheckCount IS NOT NULL AND racerCheckCount != 0 AND racerForfeit = 'n'");
    $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt->execute();
    $crAverage = round($stmt->fetchColumn());
    echo ' - Average Collection Rate: ' . $crAverage;
}
echo '</div><br />' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
echo '        <table class="raceResults">' . PHP_EOL;
if($raceIsTeam == 'n') {
    $sql = 'SELECT racerRacetimeID, racerRealTime, racerFromRacetime';
    echo '            <thead>' . PHP_EOL;
    echo '                <tr><th>Place</th><th>Name</th><th>Finish Time</th>';
    if($commentCount > 0) {
        $sql = $sql . ', racerComment';
    }
    if($checkCount > 0) {
        echo '<th>Collection Rate</th>';
        $sql = $sql . ', racerCheckCount';
    }
    if($vodCount > 0) {
        echo '<th>Link to VOD</th>';
        $sql = $sql . ', racerVODLink';
    }
    echo '</tr>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $sql = $sql . " FROM results WHERE raceSlug = :raceSlug AND racerForfeit = 'n' ORDER BY racerRealTime";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt->execute();
    $rowCount = 0;
    while($row = $stmt->fetch()) {
        $stmt2 = $pdo->prepare('SELECT racetimeName FROM racerinfo WHERE racetimeID = :racetimeID');
        $stmt2->bindValue(':racetimeID', $row['racerRacetimeID'], PDO::PARAM_STR);
        $stmt2->execute();
        $racerName = htmlentities($stmt2->fetchColumn(), ENT_COMPAT, "UTF-8", false);
        $rowCount++;
        if($rowCount % 2 == 0) {
            if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                echo '                <tr class="even new">';
            } else {
                echo '                <tr class="even">';
            }
        } else {
            if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                echo '                <tr class="odd new">';
            } else {
                echo '                <tr class="odd">';
            }
        }
        echo '<td class="place' . $rowCount . '">' . $rowCount . '</td><td>' . $racerName;
        if($commentCount > 0) {
            if($row['racerComment'] != null) {
                echo ' <span class="comment" title = "' . htmlentities($row['racerComment'], ENT_QUOTES, "UTF-8", false) . '">[Comment]</span>';
            }
        }
        echo '</td><td>' . gmdate('G:i:s', $row['racerRealTime']) . '</td>';
        if($checkCount > 0) {
            if($row['racerCheckCount'] != null) {
                echo '<td>' . $row['racerCheckCount'] . '</td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        if($vodCount > 0) {
            if($row['racerVODLink'] != null) {
                echo '<td><a target="_blank" href="' . $row['racerVODLink'] . '">Link to VOD</a></td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        echo '</tr>' . PHP_EOL;
    }
    $sql = "SELECT racerRacetimeID, racerComment, racerFromRacetime FROM results WHERE raceSlug = :raceSlug AND racerForfeit = 'y'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt->execute();
    while($row = $stmt->fetch()) {
        $stmt2 = $pdo->prepare('SELECT racetimeName FROM racerinfo WHERE racetimeID = :racetimeID');
        $stmt2->bindValue(':racetimeID', $row['racerRacetimeID'], PDO::PARAM_STR);
        $stmt2->execute();
        $racerName = htmlentities($stmt2->fetchColumn(), ENT_COMPAT, "UTF-8", false);
        $rowCount++;
        if($rowCount % 2 == 0) {
            if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                echo '                <tr class="even new">';
            } else {
                echo '                <tr class="even">';
            }
        } else {
            if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                echo '                <tr class="odd new">';
            } else {
                echo '                <tr class="odd">';
            }
        }
        echo '<td class="ff">FF</td><td>' . $racerName;
        if($commentCount > 0) {
            if($row['racerComment'] != null) {
                echo ' <span class="comment" title = "' . htmlentities($row['racerComment'], ENT_QUOTES, "UTF-8", false) . '">[Comment]</span>';
            }
        }
        echo '</td><td>Forfeit</td>';
        if($checkCount > 0) {
            echo '<td>FF</td>';
        }
        if($vodCount > 0) {
            echo '<td>Forfeit</td>';
        }
        echo '</tr>' . PHP_EOL;
    }
} else {
    $tempTableHash = createCallbackLink();
    require_once ('../src/populateTempTable.php');
    echo '            <thead>' . PHP_EOL;
    echo '                <tr><th>Place</th><th>Name</th><th>Real Time</th>';
    $sql = 'SELECT racerRacetimeID, racerRealTime, racerFromRacetime';
    if($commentCount > 0) {
        $sql = $sql . ', racerComment';
    }
    if($checkCount > 0) {
        echo '<th>Collection Rate</th>';
        $sql = $sql . ', racerCheckCount';
    }
    if($vodCount > 0) {
        echo '<th>Link to VOD</th>';
        $sql = $sql . ', racerVODLink';
    }
    echo '</tr>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $sql = $sql . " FROM results WHERE raceSlug = :raceSlug AND racerTeam = :racerTeam ORDER BY racerRealTime";
    $rowCount = 0;
    $stmt2 = "SELECT teamName, averageTime, averageCR FROM temp_" . $tempTableHash . " WHERE teamForfeit = 'n' ORDER BY averageTime";
    $sql2 = $pdo->prepare($stmt2);
    $sql2->execute();
    while($teamRow = $sql2->fetch()) {
        $rowCount++;
        $teamName = $teamRow['teamName'];
        $teamAverageTime = round($teamRow['averageTime'], 0);
        if($checkCount > 0 && $teamRow['averageCR'] != null) {
            $teamAverageCR = round($teamRow['averageCR'], 0);
        }
        if($rowCount % 2 == 0) {
            echo '                <tr class="team even">';
        } else {
            echo '                <tr class="team odd">';
        }
        echo '<td class="place' . $rowCount . '">' . $rowCount . '</td><td>' . $teamName . '</td><td>' . gmdate('G:i:s', $teamAverageTime) . '</td>';
        if($checkCount > 0 && $teamRow['averageCR'] != null) {
            echo '<td>' . $teamAverageCR . '</td>';
        } elseif ($checkCount > 0 && $teamRow['averageCR'] == null) {
            echo '<td>N/A</td>';
        }
        if($vodCount > 0) {
            echo '<td></td>';
        }
        echo '</tr>' . PHP_EOL;
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
        $stmt->bindValue(':racerTeam', $teamName, PDO::PARAM_STR);
        $stmt->execute();
        while($row = $stmt->fetch()) {
            $racerSQL = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :racetimeID");
            $racerSQL->bindValue(':racetimeID', $row['racerRacetimeID'], PDO::PARAM_STR);
            $racerSQL->execute();
            $racerName = htmlentities($racerSQL->fetchColumn(), ENT_COMPAT, "UTF-8", false);
            if($rowCount % 2 == 0) {
                if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                    echo '                <tr class="even new">';
                } else {
                    echo '                <tr class="even">';
                }
            } else {
                if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                    echo '                <tr class="odd new">';
                } else {
                    echo '                <tr class="odd">';
                }
            }
            echo '<td class="place' . $rowCount . '"></td><td class="teamRacerName">' . $racerName;
            if($commentCount > 0) {
                if($row['racerComment'] != null) {
                    echo ' <span class="comment" title = "' . htmlentities($row['racerComment'], ENT_QUOTES, "UTF-8", false) . '">[Comment]</span>';
                }
            }
            echo '</td><td class="teamRacerData">' . gmdate('G:i:s', $row['racerRealTime']) . '</td>';
            if($checkCount > 0) {
                if($row['racerCheckCount'] != null) {
                    echo '<td class="teamRacerData">' . $row['racerCheckCount'] . '</td>';
                } else {
                    echo '<td class="teamRacerData">N/A</td>';
                }
            }
            if($vodCount > 0) {
                if($row['racerVODLink'] != null) {
                    echo '<td><a target=_blank" href="' . $row['racerVODLink'] . '">Link to VOD</a></td>';
                } else {
                    echo '<td>N/A</td>';
                }
            }
            echo '</tr>' . PHP_EOL;
        }
    }
    $stmt2 = "SELECT teamName FROM temp_" . $tempTableHash . " WHERE teamForfeit = 'y' ORDER BY teamName";
    $sql2 = $pdo->prepare($stmt2);
    $sql2->execute();
    while($teamRow = $sql2->fetch()) {
        $rowCount++;
        $teamName = $teamRow['teamName'];
        if($rowCount % 2 == 0) {
            echo '                <tr class="team even">';
        } else {
            echo '                <tr class="team odd">';
        }
        echo '<td class="ff">FF</td><td>' . $teamName . '</td><td>Forfeit</td>';
        if($checkCount > 0) {
            echo '<td>FF</td>';
        }
        if($vodCount > 0) {
            echo '<td>Forfeit</td>';
        }
        echo '</tr>' . PHP_EOL;
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
        $stmt->bindValue(':racerTeam', $teamName, PDO::PARAM_STR);
        $stmt->execute();
        while($row = $stmt->fetch()) {
            $racerSQL = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :racetimeID");
            $racerSQL->bindValue(':racetimeID', $row['racerRacetimeID'], PDO::PARAM_STR);
            $racerSQL->execute();
            $racerName = htmlentities($racerSQL->fetchColumn(), ENT_COMPAT, "UTF-8", false);
            if($rowCount % 2 == 0) {
                if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                    echo '                <tr class="even new">';
                } else {
                    echo '                <tr class="even">';
                }
            } else {
                if($row['racerFromRacetime'] == 'n' && $raceFromRacetime == 'y') {
                    echo '                <tr class="odd new">';
                } else {
                    echo '                <tr class="odd">';
                }
            }
            echo '<td class="ff"></td><td class="teamRacerName">' . $racerName;
            if($commentCount > 0) {
                if($row['racerComment'] != null) {
                    echo ' <span class="comment" title = "' . htmlentities($row['racerComment'], ENT_QUOTES, "UTF-8", false) . '">[Comment]</span>';
                }
            }
            echo '</td><td class="teamRacerData">Forfeit</td>';
            if($checkCount > 0) {
                echo '<td class="teamRacerData">FF</td>';
            }
            if($vodCount > 0) {
                echo '<td>Forfeit</td>';
            }
            echo PHP_EOL;
        }
    }
    $sql = "DROP TABLE temp_" . $tempTableHash;
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
if (isset($_SESSION['userid']) && isset($raceCreatedBy)) {
    if($_SESSION['userid'] == $raceCreatedBy) {
        echo '                <tr><td colspan="3" class="submitButton"><form method="post" action=""><input type="Submit" class="submitButton" value="Download Results as .CSV" /></form></td></tr>' . PHP_EOL;
    }
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
