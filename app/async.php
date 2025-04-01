<?php

require_once ('../includes/bootstrap.php');
if (isset ($_SESSION['userid'])) {
    $user_id = $_SESSION['userid'];
}
if(!isset($_GET['raceID'])) {
    $pageTitle = 'Error Viewing Async';
    echo '        <div class="error">No Race Selected - Please <a href="' . $domain . '/search">search</a> for a race</div>' . PHP_EOL;
    die;
} else {
    $race_id = $_GET['raceID'];
    require ('../includes/race_info.php');
    if($race_exists == 'n') {
        $pageTitle = 'Error Viewing Async';
        echo '        <div class="error">No Race Found - Please try <a href="' . $domain . '/search">searching</a> again</div>' . PHP_EOL;
        die;
    }
    $pageTitle = 'Submit Times for ' . $race_slug;
}
require_once ('../includes/header.php');
if(isset($_POST['racer1Name'])) {
    $submitted = 1;
} else {
    $submitted = 0;
}
if($submitted == 0) {
    if ($race_locked_flag == 'y') {
        echo '        <div class="error">Result submissions for this race are locked. No new submissions are allowed at this time.</div><br />';
        require_once ('../src/displayResults.php');
    } else {
        if ($race_login_flag == 'y' && !$user_id) {
            echo '        <div class="error">You must log in to submit or view results for this async.</div><br />' . PHP_EOL;
            require_once ('../src/loginForm.php');
        } elseif ($race_tournament_flag == 'y' && !$user_id) {
            echo '        <div class="error">You must log in to submit or view results for this async.</div><br />' . PHP_EOL;
            require_once ('../src/loginForm.php');
        } else {
            require_once ('../src/asyncPreForm.php');
            if($race_team_flag == 'y') {
                require_once ('../src/inputTeam.php');
            } else {
                require_once ('../src/inputSingleRunner.php');
            }
        }
    }    
} else {
    $errorCondition = null;
    if($race_team_flag == 'y') {
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