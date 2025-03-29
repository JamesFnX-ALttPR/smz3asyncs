<?php

require_once ('../includes/bootstrap.php');

if(!isset($_GET['raceID'])) {
    $pageTitle = 'Error Viewing Async';
    echo '        <div class="error">No Race Selected - Please <a href="' . $domain . '/search">search</a> for a race</div>' . PHP_EOL;
    die;
} else {
    $raceID = $_GET['raceID'];
    $sql = "SELECT * FROM races WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$raceID]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(! $row) {
        $pageTitle = 'Error Viewing Async';
        echo '        <div class="error">No Race Found - Please try <a href="' . $domain . '/search">searching</a> again</div>' . PHP_EOL;
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
    $raceLocked = $row['locked'];
    $race_tournament = $row['tournament_seed'];
    $pageTitle = 'Submit Times for ' . $raceSlug;
}
require_once ('../includes/header.php');
if(isset($_POST['racer1Name'])) {
    $submitted = 1;
} else {
    $submitted = 0;
}
if($submitted == 0) {
    if ($raceLocked == 'y') {
        echo '        <div class="error">Result submissions for this race are locked. No new submissions are allowed at this time.</div><br />';
        require_once ('../src/displayResults.php');
    } else {
        if ($raceLoginRequired == 'y' && ! isset($_SESSION['userid'])) {
            echo '        <div class="error">You must log in to submit or view results for this async.</div><br />' . PHP_EOL;
            require_once ('../src/loginForm.php');
        } elseif ($race_tournament == 'y' && ! isset($_SESSION['userid'])) {
            echo '        <div class="error">You must log in to submit or view results for this async.</div><br />' . PHP_EOL;
            require_once ('../src/loginForm.php');
        } else {
            require_once ('../src/asyncPreForm.php');
            if($raceIsTeam == 'y') {
                require_once ('../src/inputTeam.php');
            } else {
                require_once ('../src/inputSingleRunner.php');
            }
        }
    }    
} else {
    $errorCondition = null;
    if($raceIsTeam == 'y') {
        if(! isset($_POST['approved'])) {
            require_once ('../src/processTeam.php');
            require_once ('../src/verifyTeamAsync.php');
        } elseif ($_POST['approved'] == 'y') {
            require_once ('../src/addTeamAsyncToDB.php');
        }
    } else {
        if(! isset($_POST['approved'])) {
            require_once ('../src/processSingleRunner.php');
            require_once ('../src/verifySingleAsync.php');
        } elseif($_POST['approved'] == 'y') {
            require_once ('../src/addSingleAsyncToDB.php');
        }
    }
}
require ('../includes/footer.php');