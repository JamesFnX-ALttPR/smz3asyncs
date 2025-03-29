<?php
$errorCondition = null;
$stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerRacetimeID in (SELECT racetimeID FROM racerinfo WHERE racetimeName = :name)");
$stmt->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
$stmt->bindParam(':name', $_POST['racer1Name'], PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetchColumn();
if(! $row) {
    $stmt2 = $pdo->prepare("SELECT racetimeID FROM racerinfo WHERE racetimeName = :name");
    $stmt2->bindParam(':name', $_POST['racer1Name'], PDO::PARAM_STR);
    $stmt2->execute();
    $row2 = $stmt2->fetchColumn();
    if(! $row2) {
        while (1 == 1) {
            $racerID = generateRacerID();
            $stmt3 = $pdo->prepare("SELECT id FROM racerinfo WHERE racetimeID = :id");
            $stmt3->bindParam(':id', $racerID, PDO::PARAM_STR);
            $stmt3->execute();
            $row3 = $stmt3->fetchColumn();
            if (! $row3) {
                break;
            }
        }
        $stmt3 = $pdo->prepare("INSERT INTO racerinfo (racetimeID, racetimeName) VALUES (:id, :name)");
        $stmt3->bindParam(':id', $racerID, PDO::PARAM_STR);
        $stmt3->bindParam(':name', $_POST['racer1Name'], PDO::PARAM_STR);
        $stmt3->execute();
    } else {
        $racerID = $row2;
    }
    if($_POST['racer1Forfeit'] == 'n') {
        $racerRT = $_POST['racer1RealTime'];
        if(isset($_POST['racer1Comment'])) {
            $racerComment = $_POST['racer1Comment'];
        } else {
            $racerComment = null;
        }
        $racerForfeit = $_POST['racer1Forfeit'];
        if(isset($_POST['racer1CR'])) {
            $racerCR = $_POST['racer1CR'];
        } else {
            $racerCR = null;
        }
        if(isset($_POST['racer1VOD'])) {
            $racerVOD = $_POST['racer1VOD'];
        } else {
            $racerVOD = null;
        }
        if (isset($_POST['enteredBy'])) {
            $enteredBy = $_POST['enteredBy'];
        } else {
            $enteredBy = null;
        }
        $insertSQL = $pdo->prepare("INSERT INTO results (raceSlug, racerRacetimeID, racerTeam, racerRealTime, racerComment, racerForfeit, racerFromRacetime, racerCheckCount, racerVODLink, enteredBy) VALUES (:slug, :id, '', :rt, :comment, :forfeit, 'n', :cr, :vod, :enteredBy)");
        $insertSQL->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
        $insertSQL->bindParam(':id', $racerID, PDO::PARAM_STR);
        $insertSQL->bindParam(':rt', $racerRT, PDO::PARAM_INT);
        $insertSQL->bindParam(':comment', $racerComment, PDO::PARAM_STR);
        $insertSQL->bindParam(':forfeit', $racerForfeit, PDO::PARAM_STR);
        $insertSQL->bindParam(':cr', $racerCR, PDO::PARAM_INT);
        $insertSQL->bindParam(':vod', $racerVOD, PDO::PARAM_STR);
        $insertSQL->bindParam(':enteredBy', $enteredBy, PDO::PARAM_INT);
        $insertSQL->execute();
    } else {
        if(isset($_POST['racer1Comment'])) {
            $racerComment = $_POST['racer1Comment'];
        } else {
            $racerComment = null;
        }
        $racerForfeit = $_POST['racer1Forfeit'];
        if(isset($_POST['racer1VOD'])) {
            $racerVOD = $_POST['racer1VOD'];
        } else {
            $racerVOD = null;
        }if (isset($_POST['enteredBy'])) {
            $enteredBy = $_POST['enteredBy'];
        } else {
            $enteredBy = null;
        }
        $insertSQL = $pdo->prepare("INSERT INTO results (raceSlug, racerRacetimeID, racerTeam, racerRealTime, racerComment, racerForfeit, racerFromRacetime, racerVODLink, enteredBy) VALUES (:slug, :id, '', 20000, :comment, :forfeit, 'n', :vod, :enteredBy)");
        $insertSQL->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
        $insertSQL->bindParam(':id', $racerID, PDO::PARAM_STR);
        $insertSQL->bindParam(':comment', $racerComment, PDO::PARAM_STR);
        $insertSQL->bindParam(':forfeit', $racerForfeit, PDO::PARAM_STR);
        $insertSQL->bindParam(':vod', $racerVOD, PDO::PARAM_STR);
        $insertSQL->bindParam(':enteredBy', $enteredBy, PDO::PARAM_INT);
        $insertSQL->execute();
    }
    require_once ('../src/displayResults.php');
} else {
    $errorCondition = 'A runner with your name already has a time for this race. Please check the <a target="_blank" href="' . $domain . '/results/' . $raceID . '">results</a>.';
    echo '        <div class="error">' . $errorCondition . ' - Please Try Again</div>' . PHP_EOL;
    require_once ('../src/asyncPreForm.php');
    require_once ('../src/inputSingleRunner.php');
}
