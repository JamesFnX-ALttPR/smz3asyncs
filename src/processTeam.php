<?php

$racer1Name = htmlentities($_POST['racer1Name'], ENT_COMPAT, "UTF-8");
$racer2Name = htmlentities($_POST['racer2Name'], ENT_COMPAT, "UTF-8");
$teamName = htmlentities($_POST['teamName'], ENT_COMPAT, "UTF-8");
$stmt = $pdo->prepare("SELECT racerTeam FROM results WHERE raceSlug = :slug AND racerTeam = :team");
$stmt->bindParam(':slug', $raceSlug, PDO::PARAM_STR);
$stmt->bindParam(':team', $teamName, PDO::PARAM_STR);
$stmt->execute();
if($stmt->fetch()) {
    $errorCondition = 'Team Name already in use for this async<br />' . PHP_EOL;
}
if(!isset($_POST['racer1Forfeit'])) { // Check if the forfeit box was left unchecked
    $teamForfeit = 'n';
    if($_POST['racer1RTHours'] != '' && $_POST['racer1RTMinutes'] != '' && $_POST['racer1RTSeconds'] != '') { // Validate time entry is correct (all three boxes filled out)
        $racer1RealTime = ( 3600 * intval($_POST['racer1RTHours']) ) + ( 60 * intval($_POST['racer1RTMinutes']) ) + intval($_POST['racer1RTSeconds']);
    } else {
        $errorCondition .= 'Player 1 Real Time not input correctly<br />' . PHP_EOL; // Set error condition if time does not validate
    }
    if($_POST['racer2RTHours'] != '' && $_POST['racer2RTMinutes'] != '' && $_POST['racer2RTSeconds'] != '') { // Validate time entry is correct (all three boxes filled out)
        $racer2RealTime = ( 3600 * intval($_POST['racer2RTHours']) ) + ( 60 * intval($_POST['racer2RTMinutes']) ) + intval($_POST['racer2RTSeconds']);
    } else {
        $errorCondition .= 'Player 2 Real Time not input correctly<br />' . PHP_EOL; // Set error condition if time does not validate
    }
    if($_POST['racer1CR'] != '') { // Check if there's a CR and output null if not
        $racer1CR = $_POST['racer1CR'];
    } else {
        $racer1CR = null;
    }
    if($_POST['racer2CR'] != '') { // Check if there's a CR and output null if not
        $racer2CR = $_POST['racer2CR'];
    } else {
        $racer2CR = null;
    }
    if($_POST['racer1Comments'] != '') { // Check if there are comments and output null if not
        $racer1Comment = htmlentities($_POST['racer1Comments'], ENT_COMPAT, "UTF-8", false);
    } else {
        $racer1Comment = null;
    }
    if($_POST['racer2Comments'] != '') { // Check if there are comments and output null if not
        $racer2Comment = htmlentities($_POST['racer2Comments'], ENT_COMPAT, "UTF-8", false);
    } else {
        $racer2Comment = null;
    }
    if($_POST['racer1VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer1VOD'], 0, 8) == 'https://' || substr($_POST['racer1VOD'], 0, 7) == 'http://') {
            $racer1VOD = $_POST['racer1VOD'];
        } else {
            $errorCondition .= 'Player 1 VOD Link not input correctly (Did you start with http:// or https://?)<br />' . PHP_EOL; 
        }
    } else {
        $racer1VOD = null;
    }
    if($_POST['racer2VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer2VOD'], 0, 8) == 'https://' || substr($_POST['racer2VOD'], 0, 7) == 'http://') {
            $racer2VOD = $_POST['racer2VOD'];
        } else {
            $errorCondition .= 'Player 2 VOD Link not input correctly (Did you start with http:// or https://?)' . PHP_EOL; 
        }
    } else {
        $racer2VOD = null;
    }
} else { // If the forfeit box is checked, this sets the interesting boxes.
    $teamForfeit = 'y';
    $racer1RealTime = 35940;
    $racer2RealTime = 35940;
    $racer1CR = null;
    $racer2CR = null;
    if($_POST['racer1Comments'] != '') { // Check if there are comments and output null if not
        $racer1Comment = htmlentities($_POST['racer1Comments'], ENT_COMPAT, "UTF-8", false);
    } else {
        $racer1Comment = null;
    }
    if($_POST['racer2Comments'] != '') { // Check if there are comments and output null if not
        $racer2Comment = htmlentities($_POST['racer2Comments'], ENT_COMPAT, "UTF-8", false);
    } else {
        $racer2Comment = null;
    }
    if($_POST['racer1VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer1VOD'], 0, 8) == 'https://' || substr($_POST['racer1VOD'], 0, 7) == 'http://') {
            $racer1VOD = $_POST['racer1VOD'];
        } else {
            $errorCondition .= 'Player 1 VOD Link not input correctly (Did you start with http:// or https://?)' . PHP_EOL; 
        }
    } else {
        $racer1VOD = null;
    }
    if($_POST['racer2VOD'] != '') { // Check if there is a VOD and validate the link is in a proper format
        if(substr($_POST['racer2VOD'], 0, 8) == 'https://' || substr($_POST['racer2VOD'], 0, 7) == 'http://') {
            $racer2VOD = $_POST['racer2VOD'];
        } else {
            $errorCondition .= 'Player 2 VOD Link not input correctly (Did you start with http:// or https://?)' . PHP_EOL;
        }
    } else {
        $racer2VOD = null;
    }
}
if (isset($_POST['enteredBy'])) {
    $enteredBy = $_POST['enteredBy'];
}
