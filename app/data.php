<?php

require_once ('../includes/bootstrap.php');
$httpStatus = 200;
ob_start();
ob_clean();
header_remove();
header('Content-Type: application/json; charset=utf-8');
http_response_code($httpStatus);
$jsonArray = array();
if (isset($_GET['raceID'])) {
    $race_id = $_GET['raceID'];
    $jsonArray['id'] = $race_id;
    require ('../includes/race_info.php');
    $jsonArray['slug'] = $race_slug;
    $jsonArray['mode'] = $race_mode;
    $jsonArray['seed'] = $race_seed;
    $jsonArray['hash'] = $race_hash;
    $jsonArray['team_race'] = $race_team_flag;
    $jsonArray['spoiler_race'] = $race_spoiler_flag;
    if ($race_spoiler_flag == 'y') {
        $jsonArray['spoiler'] = $race_spoiler_link;
    }
    if ($race_team_flag == 'n') { //Sort the list of racers by time with forfeits on the bottom and form the rest of the JSON
        $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerForfeit = 'n' ORDER BY racerRealTime");
        $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $result_id = $row['id'];
            require ('../includes/result_info.php');
            if (! $racer_name) {
                echo json_encode($jsonArray);
                $dieString = 'Racer ' . $racer_id . ' not found';
                die ($dieString);
            }
            $jsonArray['participants'][] = [ 'racer_name' => $racer_name , 'time' => $racer_time , 'collection_rate' => $racer_collection_rate , 'vod_link' => $racer_vod , 'comments' => $racer_comment ];
        }
        $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerForfeit = 'y'");
        $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $result_id = $row['id'];
            require ('../includes/result_info.php');
            if (! $racer_name) {
                echo json_encode($jsonArray);
                $dieString = 'Racer ' . $racer_id . ' not found';
                die ($dieString);
            }
            $racer_time = 'Forfeit';
            $jsonArray['participants'][] = [ 'racer_name' => $racer_name , 'time' => $racer_time , 'vod_link' => $racer_vod , 'comments' => $racer_comment ];
        }
    } else { //For team races, there's a few extra steps
        $temp_table_hash = createCallbackLink();
        $check_count = 1;
        require ('../src/populateTempTable.php');
        //Now that the temp table is complete, let's gather individual results and form the JSON
        $rowCount = 0;
        $sql = "SELECT teamName, averageTime FROM temp_" . $temp_table_hash . " WHERE teamForfeit = 'n' ORDER BY averageTime";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $team = $row['teamName'];
            $avgTime = $row['averageTime'];
            $jsonArray['teams'][$rowCount] = [ 'team_name' => $team , 'average_time' => $avgTime, 'forfeit' => 'n' ];
            $stmt2 = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerTeam = :team ORDER BY racerRealTime");
            $stmt2->bindValue(':slug', $race_slug, PDO::PARAM_STR);
            $stmt2->bindValue(':team', $team, PDO::PARAM_STR);
            $stmt2->execute();
            while ($row2 = $stmt2->fetch()) {
                $result_id = $row2['id'];
                require ('../includes/result_info.php');
                if (! $racer_name) {
                    echo json_encode($jsonArray);
                    $dieString = 'Racer ' . $racer_id . ' not found';
                    die ($dieString);
                }
                $jsonArray['teams'][$rowCount]['members'][] = [ 'racer_name' => $racer_name , 'time' => $racer_time , 'collection_rate' => $racer_collection_rate , 'vod_link' => $racer_vod , 'comments' => $racer_comment ];
            }
            $rowCount++;
        }
        $sql = "SELECT teamName, averageTime FROM temp_" . $temp_table_hash . " WHERE teamForfeit = 'y'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $team = $row['teamName'];
            $jsonArray['teams'][$rowCount] = [ 'team_name' => $team, 'forfeit' => 'y' ];
            $stmt2 = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerTeam = :team");
            $stmt2->bindValue(':slug', $race_slug, PDO::PARAM_STR);
            $stmt2->bindValue(':team', $team, PDO::PARAM_STR);
            $stmt2->execute();
            while ($row2 = $stmt2->fetch()) {
                $result_id = $row2['id'];
                require ('../includes/result_info.php');
                if (! $racer_name) {
                    echo json_encode($jsonArray);
                    $dieString = 'Racer ' . $racer_id . ' not found';
                    die ($dieString);
                }
                $racer_time = "Forfeit";
                $jsonArray['teams'][$rowCount]['members'][] = [ 'racer_name' => $racer_name , 'time' => $racer_time , 'vod_link' => $racer_vod , 'comments' => $racer_comment ];
            }
            $rowCount++;
        }
        $sql = "DROP TABLE temp_" . $temp_table_hash;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    echo json_encode ($jsonArray);
    exit();
} else {
    die ('No race found');
}
