<?php

require_once ('../includes/bootstrap.php');

if(!isset($_GET['raceID'])) {
    $pageTitle = 'Error Viewing Async';
    require_once ('../includes/header.php');
    echo '        <div class="error">No Race Selected - Please <a href="' . $domain . '/search">search</a> for a race</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
    die;
} else {
    $raceID = $_GET['raceID'];
    $stmt = $pdo->prepare("SELECT * FROM races WHERE id = :id");
    $stmt->bindParam(':id', $raceID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if(! $row) {
        $pageTitle = 'Error Viewing Async';
        require_once ('../includes/header.php');
        echo '        <div class="error">No Race Found - Please try <a href="' . $domain . '/search">searching</a> again</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    $raceSlug = $row['raceSlug'];
    $raceMode = $row['raceMode'];
    $raceSeed = $row['raceSeed'];
    $raceHash = $row['raceHash'];
    $raceDescription = $row['raceDescription'];
    $raceIsTeam = $row['raceIsTeam'];
    $raceIsSpoiler = $row['raceIsSpoiler'];
    $raceSpoilerLink = $row['raceSpoilerLink'];
    $raceFromRacetime = $row['raceFromRacetime'];
    $raceVODRequired = $row['vodRequired'];
    $raceLoginRequired = $row['loginRequired'];
    $tourney_seed = $row['tournament_seed'];
    $raceCreatedBy = $row['createdBy'];
    $pageTitle = 'View Times for ' . $raceSlug;
    if (is_post_request() && $_SESSION['userid'] == $raceCreatedBy) {
        $fields = array("'place'", "'name'", "'team'", "'rt_seconds'", "'cr'", "'comment'", "'forfeit'", "'vod_link'");
        $delimiter = ',';
        $filename = 'alttprasyncs-' . $raceSlug . date("Y-m-d H:i:s") . '.csv';
        $f = fopen('php://output', 'w');
        // In case, if php://output didn't work, uncomment below line
        // $f = fopen("php://memory", "w"); 
        fputcsv($f, $fields, $delimiter);
        $place = 0;
        $stmt = $pdo->prepare("SELECT * FROM results WHERE raceSlug = :slug AND racerForfeit = 'n' ORDER BY racerRealTime");
        $stmt->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $place++;
            $racerID = $row['racerRacetimeID'];
            if ($row['racerTeam'] == null) {
                $racerTeam = '';
            } else {
                $racerTeam = $row['racerTeam'];
            }
            $racerForfeit = $row['racerForfeit'];
            if ($racerForfeit == 'y') {
                $racerRT = '';
                $racerCR = '';
            } else {
                $racerRT = $row['racerRealTime'];
                if ($row['racerCheckCount' == null]) {
                    $racerCR = '';
                } else {
                    $racerCR = $row['racerCheckCount'];
                }
            }
            if ($row['racerComment'] == null) {
                $racerComment = '';
            } else {
                $racerComment = $row['racerComment'];
            }
            if ($row['racerVODLink'] == null) {
                $racerVOD = '';
            } else {
                $racerVOD = $row['racerVODLink'];
            }
            $stmt2 = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :id");
            $stmt2->bindParam(':id', $racerID, PDO::PARAM_STR);
            $stmt2->execute();
            $racerName = $stmt2->fetchColumn();
            $row_data = array($place, $racerName, $racerTeam, $racerRT, $racerCR, $racerComment, $racerForfeit, $racerVOD);
            fputcsv($f, $row_data, $delimiter);
        }
        fclose ($f);
        // If case fclose does not work, uncomment fseek() and fpassthru().
        // fseek($f, 0);
        // Telling browser to download file as CSV
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment; filename="'.$filename.'";'); 
        // fpassthru($f);
        exit();
    }
}
require_once ('../includes/header.php');
if ($raceLoginRequired == 'y' && ! isset($_SESSION['userid'])) {
    echo '        <div class="error">You must log in to submit or view results for this async.</div><br />' . PHP_EOL;
    include ('../src/loginForm.php');
} elseif ($tourney_seed == 'y' && ! isset($_SESSION['userid'])) {
    echo '        <div class="error">You must log in to submit or view results for this async.</div><br />' . PHP_EOL;
    include ('../src/loginForm.php');
} elseif ($tourney_seed == 'y' && $raceCreatedBy != $_SESSION['userid']) {
    $stmt = $pdo->prepare('SELECT COUNT(id) FROM results WHERE raceSlug = :slug AND enteredBy = :id');
    $stmt->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
    $stmt->bindParam(':id', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    $rslt = $stmt->fetchColumn();
    if (!$rslt) {
        echo '        <div class="error">Only racers who have submitted a result may view results for this async.<br />Click <a href="' . $domain . '/async/' . $raceID . '">here</a> to submit a result.</div><br />' . PHP_EOL;
    }
} else {
    require_once ('../src/displayResults.php');
}
require_once ('../includes/footer.php');