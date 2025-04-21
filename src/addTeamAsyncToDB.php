<?php

$errorCondition = null;
$stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = ? AND racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = ? OR rtgg_name = ?)");
$stmt->execute([$race_slug, $_POST['racer1Name'], $_POST['racer2Name']]);
$row = $stmt->fetchColumn();
if(! $row) { // Check for existing runners
    $check1 = $pdo->prepare("SELECT racetimeID FROM racerinfo WHERE rtgg_name = :name");
    $check1->bindParam(':name', $_POST['racer1Name'], PDO::PARAM_STR);
    $check1->execute();
    $checkRow1 = $check1->fetchColumn();
    if(! $checkRow1) {
        $racer1ID = generateRacerID();
        while (1 == 1) {
            $checkNewID1 = $pdo->prepare("SELECT id FROM racerinfo WHERE racetimeID = :id");
            $checkNewID1->bindParam(':id', $racer1ID, PDO::PARAM_STR);
            $checkNewID1->execute();
            $checkNewIDRow1 = $checkNewID1->fetchColumn();
            if (! $checkNewIDRow1) {
                break;
            }
        }
        $addRacer1 = $pdo->prepare("INSERT INTO racerinfo (racetimeID, rtgg_name) VALUES (:id, :name)");
        $addRacer1->bindParam(':id', $racer1ID, PDO::PARAM_STR);
        $addRacer1->bindParam(':name', $_POST['racer1Name'], PDO::PARAM_STR);
        $addRacer1->execute();
    } else {
        $racer1ID = $checkRow1;
    }
    $check2 = $pdo->prepare("SELECT racetimeID FROM racerinfo WHERE rtgg_name = :name");
    $check2->bindParam(':name', $_POST['racer2Name'], PDO::PARAM_STR);
    $check2->execute();
    $checkRow2 = $check2->fetchColumn();
    if(! $checkRow2) {
        $racer2ID = generateRacerID();
        while (1 == 1) {
            $checkNewID2 = $pdo->prepare("SELECT id FROM racerinfo WHERE racetimeID = :id");
            $checkNewID2->bindParam(':id', $racer2ID, PDO::PARAM_STR);
            $checkNewID2->execute();
            $checkNewIDRow2 = $checkNewID2->fetchColumn();
            if (! $checkNewIDRow2) {
                break;
            }
        }
        $addRacer2 = $pdo->prepare("INSERT INTO racerinfo (racetimeID, rtgg_name) VALUES (:id, :name)");
        $addRacer2->bindParam(':id', $racer2ID, PDO::PARAM_STR);
        $addRacer2->bindParam(':name', $_POST['racer2Name'], PDO::PARAM_STR);
        $addRacer2->execute();
    } else {
        $racer2ID = $checkRow2;
    }
    if($_POST['teamForfeit'] == 'n') {
        $insertSQL = $pdo->prepare("INSERT INTO results (raceSlug, racerRacetimeID, racerTeam, racerRealTime, racerComment, racerForfeit, racerFromRacetime, racerCheckCount, racerVODLink, enteredBy) VALUES (:slug1, :id1, :team1, :rt1, :comment1, :forfeit1, 'n', :cr1, :vod1, :enteredBy1), (:slug2, :id2, :team2, :rt2, :comment2, :forfeit2, 'n', :cr2, :vod2, :enteredBy2)");
        $teamName = $_POST['teamName'];
        $racer1RT = $_POST['racer1RealTime'];
        if(isset($_POST['racer1Comment'])) {
            $racer1Comment = $_POST['racer1Comment'];
        } else {
            $racer1Comment = null;
        }
        $racer1Forfeit = $_POST['teamForfeit'];
        if(isset($_POST['racer1CR'])) {
            $racer1CR = $_POST['racer1CR'];
        } else {
            $racer1CR = null;
        }
        if(isset($_POST['racer1VOD'])) {
            $racer1VOD = $_POST['racer1VOD'];
        } else {
            $racer1VOD = null;
        }
        $racer2RT = $_POST['racer2RealTime'];
        if(isset($_POST['racer2Comment'])) {
            $racer2Comment = $_POST['racer2Comment'];
        } else {
            $racer2Comment = null;
        }
        $racer2Forfeit = $_POST['teamForfeit'];
        if(isset($_POST['racer2CR'])) {
            $racer2CR = $_POST['racer2CR'];
        } else {
            $racer2CR = null;
        }
        if(isset($_POST['racer2VOD'])) {
            $racer2VOD = $_POST['racer2VOD'];
        } else {
            $racer2VOD = null;
        }
        if (isset($_POST['enteredBy'])) {
            $enteredBy = $_POST['enteredBy'];
        } else {
            $enteredBy = null;
        }
        $insertSQL->bindParam(':slug1', $race_slug, PDO::PARAM_STR);
        $insertSQL->bindParam(':id1', $racer1ID, PDO::PARAM_STR);
        $insertSQL->bindParam(':team1', $teamName, PDO::PARAM_STR);
        $insertSQL->bindParam(':rt1', $racer1RT, PDO::PARAM_INT);
        $insertSQL->bindParam(':comment1', $racer1Comment, PDO::PARAM_STR);
        $insertSQL->bindParam(':forfeit1', $racer1Forfeit, PDO::PARAM_STR);
        $insertSQL->bindParam(':cr1', $racer1CR, PDO::PARAM_INT);
        $insertSQL->bindParam(':vod1', $racer1VOD, PDO::PARAM_STR);
        $insertSQL->bindParam(':enteredBy1', $enteredBy, PDO::PARAM_INT);
        $insertSQL->bindParam(':slug2', $race_slug, PDO::PARAM_STR);
        $insertSQL->bindParam(':id2', $racer2ID, PDO::PARAM_STR);
        $insertSQL->bindParam(':team2', $teamName, PDO::PARAM_STR);
        $insertSQL->bindParam(':rt2', $racer2RT, PDO::PARAM_INT);
        $insertSQL->bindParam(':comment2', $racer2Comment, PDO::PARAM_STR);
        $insertSQL->bindParam(':forfeit2', $racer2Forfeit, PDO::PARAM_STR);
        $insertSQL->bindParam(':cr2', $racer2CR, PDO::PARAM_INT);
        $insertSQL->bindParam(':vod2', $racer2VOD, PDO::PARAM_STR);
        $insertSQL->bindParam(':enteredBy2', $enteredBy, PDO::PARAM_INT);
        $insertSQL->execute();
    } else {
        $insertSQL = $pdo->prepare("INSERT INTO results (raceSlug, racerRacetimeID, racerTeam, racerRealTime, racerComment, racerForfeit, racerFromRacetime, racerVODLink, enteredBy) VALUES (:slug1, :id1, :team1, 20000, :comment1, :forfeit1, 'n', :vod1, :enteredBy1), (:slug2, :id2, :team2, 20000, :comment2, :forfeit2, 'n', :vod2, :enteredBy2)");
        $teamName = $_POST['teamName'];
        if(isset($_POST['racer1Comment'])) {
            $racer1Comment = $_POST['racer1Comment'];
        } else {
            $racer1Comment = null;
        }
        $racer1Forfeit = $_POST['teamForfeit'];
        if(isset($_POST['racer1VOD'])) {
            $racer1VOD = $_POST['racer1VOD'];
        } else {
            $racer1VOD = null;
        }
        if(isset($_POST['racer2Comment'])) {
            $racer2Comment = $_POST['racer2Comment'];
        } else {
            $racer2Comment = null;
        }
        $racer2Forfeit = $_POST['teamForfeit'];
        if(isset($_POST['racer2VOD'])) {
            $racer2VOD = $_POST['racer2VOD'];
        } else {
            $racer2VOD = null;
        }
        if (isset($_POST['enteredBy'])) {
            $enteredBy = $_POST['enteredBy'];
        } else {
            $enteredBy = null;
        }
        $insertSQL->bindParam(':slug1', $race_slug, PDO::PARAM_STR);
        $insertSQL->bindParam(':id1', $racer1ID, PDO::PARAM_STR);
        $insertSQL->bindParam(':team1', $teamName, PDO::PARAM_STR);
        $insertSQL->bindParam(':comment1', $racer1Comment, PDO::PARAM_STR);
        $insertSQL->bindParam(':forfeit1', $racer1Forfeit, PDO::PARAM_STR);
        $insertSQL->bindParam(':vod1', $racer1VOD, PDO::PARAM_STR);
        $insertSQL->bindParam(':enteredBy1', $enteredBy, PDO::PARAM_INT);
        $insertSQL->bindParam(':slug2', $race_slug, PDO::PARAM_STR);
        $insertSQL->bindParam(':id2', $racer2ID, PDO::PARAM_STR);
        $insertSQL->bindParam(':team2', $teamName, PDO::PARAM_STR);
        $insertSQL->bindParam(':comment2', $racer2Comment, PDO::PARAM_STR);
        $insertSQL->bindParam(':forfeit2', $racer2Forfeit, PDO::PARAM_STR);
        $insertSQL->bindParam(':vod2', $racer2VOD, PDO::PARAM_STR);
        $insertSQL->bindParam(':enteredBy2', $enteredBy, PDO::PARAM_INT);
        $insertSQL->execute();
    }
    require_once ('../src/displayResults.php');
} else {
    $errorCondition = 'One or both of the runners already has a time for this race. Please check the <a target="_blank" href="' . $domain . '/results/' . $race_id . '">results</a>.';
    echo '        <div class="error">' . $errorCondition . ' - Please Try Again</div>' . PHP_EOL;
    require_once ('../src/asyncPreForm.php');
    require_once ('../src/inputTeam.php');
}
