<?php

// Variables from inputEditAsync - (racerTeam), racerForfeit, racerRTHours, racerRTMinutes, racerRTSeconds, racerCR, racerComment, racerVOD, raceSlug

$errors = null; //Set error variable to empty, check for errors at the end
if (isset($_POST['racerTeam'])) {
    $racerTeam = strip_tags($_POST['racerTeam']);
} else {
    $racerTeam = null;
}
if (isset($_POST['racerForfeit'])) {
    $racerForfeit = 'y';
    $racerRealTime = 20000;
    $racerCR = null;
} else {
    $racerForfeit = 'n';
    $racerRTHours = $_POST['racerRTHours'];
    $racerRTMinutes = $_POST['racerRTMinutes'];
    $racerRTSeconds = $_POST['racerRTSeconds'];
    if ($racerRTHours != '' && $racerRTMinutes != '' && $racerRTSeconds != '') {
        $racerRealTime = 3600 * $racerRTHours + 60 * $racerRTMinutes + $racerRTSeconds;
    } else {
        $errors .= 'Real Time not entered properly, please fill in all three fields. If Real Time is less than 1 hour, input 0 for hours.<br />';
    }
    if ($_POST['racerCR'] == '') {
        $racerCR = null;
    } else {
        $racerCR = $_POST['racerCR'];
    }
}
if ($_POST['racerComments'] == '') {
    $racerComment = null;
} else {
    $racerComment = strip_tags($_POST['racerComments']);
}
if ($_POST['racerVOD'] == '') {
    $racerVOD = null;
} else {
    $racerVOD = filter_var($_POST['racerVOD'], FILTER_SANITIZE_URL);
    if (! filter_var($racerVOD, FILTER_VALIDATE_URL)) { //Check if seed URL is a valid URL
        $errors .= 'VOD Link is not a valid URL.<br />';
    }
}
$raceSlug = $_POST['raceSlug'];
$racerName = $_POST['racerName'];

if ($errors != '') {
    echo '        <div class="error">' . $errors . '</div>' . PHP_EOL;
} else {
    if (isset($_POST['raceIsTeam'])) {
        $stmt = $pdo->prepare("UPDATE results SET racerRealTime = :racerRealTime, racerComment = :racerComment, racerForfeit = :racerForfeit, racerCheckCount = :racerCR, racerVODLink = :racerVOD WHERE id = :id");
        $stmt->bindValue(':racerRealTime', $racerRealTime, PDO::PARAM_INT);
        $stmt->bindValue(':racerComment', $racerComment, PDO::PARAM_STR);
        $stmt->bindValue(':racerForfeit', $racerForfeit, PDO::PARAM_STR);
        $stmt->bindValue(':racerCR', $racerCR, PDO::PARAM_INT);
        $stmt->bindValue(':racerVOD', $racerVOD, PDO::PARAM_STR);
        $stmt->bindValue(':id', $result_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare("SELECT racerTeam FROM results WHERE id = :id");
        $stmt->bindValue(':id', $result_id, PDO::PARAM_INT);
        $stmt->execute();
        $oldTeam = $stmt->fetchColumn();
        if ($oldTeam != $racerTeam) {
            $stmt = $pdo->prepare("UPDATE results SET racerTeam = :racerTeam WHERE raceSlug = :raceSlug AND racerTeam = :oldTeam");
            $stmt->bindValue(':racerTeam', $racerTeam, PDO::PARAM_STR);
            $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
            $stmt->bindValue(':oldTeam', $oldTeam, PDO::PARAM_STR);
            $stmt->execute();
        }

        $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
        $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
        $stmt->execute();
        $race_id = $stmt->fetchColumn();

        $notes = 'Result Edit! View your result here:<br /><a href="' . $domain . '/results/' . $race_id . '">' . $domain . '/results/' . $race_id . '</a>';
        echo '        <table class="submitAsync">' . PHP_EOL;
        echo '            <tbody>' . PHP_EOL;
        echo '                <tr><td colspan="2">' . $notes . '</td><tr>' . PHP_EOL;
        echo '                <tr><th class="rightAlign">Your Name:</th><td>' . $racerName . '</td></tr>' . PHP_EOL;
        echo '                <tr><th class="rightAlign">Your Team Name:</th><td>' . $racerTeam . '</td></tr>' . PHP_EOL;
        if ($racerForfeit == 'y') {
            echo '                <tr><th class="rightAlign">Forfeit:</th><td>Yes</td></tr>' . PHP_EOL;
            if ($racerComment != null) {
                echo '                <tr><th class="rightAlign">Your Comment:</th><td>' . $racerComment . '</td></tr>' . PHP_EOL;
            }
            if ($racerVOD != null) {
                echo '                <tr><th class="rightAlign">Your VOD Link:</th><td>' . $racerVOD . '</td></tr>' . PHP_EOL;
            }
        } else {
            echo '                <tr><th class="rightAlign">Forfeit:</th><td>No</td></tr>' . PHP_EOL;
            echo '                <tr><th class="rightAlign">Your Time:</th><td>' . gmdate('G:i:s', $racerRealTime) . '</td></tr>' . PHP_EOL;
            if ($racerCR != null) {
                echo '                <tr><th class="rightAlign">Your Collection Rate:</th><td>' . $racerCR . '</td></tr>' . PHP_EOL;
            }
            if ($racerComment != null) {
                echo '                <tr><th class="rightAlign">Your Comment:</th><td>' . $racerComment . '</td></tr>' . PHP_EOL;
            }
            if ($racerVOD != null) {
                echo '                <tr><th class="rightAlign">Your VOD Link:</th><td>' . $racerVOD . '</td></tr>' . PHP_EOL;
            }
        }
        echo '            </tbody>' . PHP_EOL;
        echo '        </table>' . PHP_EOL;
    } else {
        $stmt = $pdo->prepare("UPDATE results SET racerRealTime = :racerRealTime, racerComment = :racerComment, racerForfeit = :racerForfeit, racerCheckCount = :racerCR, racerVODLink = :racerVOD WHERE id = :id");
        $stmt->bindValue(':racerRealTime', $racerRealTime, PDO::PARAM_INT);
        $stmt->bindValue(':racerComment', $racerComment, PDO::PARAM_STR);
        $stmt->bindValue(':racerForfeit', $racerForfeit, PDO::PARAM_STR);
        $stmt->bindValue(':racerCR', $racerCR, PDO::PARAM_INT);
        $stmt->bindValue(':racerVOD', $racerVOD, PDO::PARAM_STR);
        $stmt->bindValue(':id', $result_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
        $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
        $stmt->execute();
        $race_id = $stmt->fetchColumn();

        $notes = 'Result Edit! View your result here:<br /><a href="' . $domain . '/results/' . $race_id . '">' . $domain . '/results/' . $race_id . '</a>';
        echo '        <table class="submitAsync">' . PHP_EOL;
        echo '            <tbody>' . PHP_EOL;
        echo '                <tr><td colspan="2">' . $notes . '</td><tr>' . PHP_EOL;
        echo '                <tr><th class="rightAlign">Your Name:</th><td>' . $racerName . '</td></tr>' . PHP_EOL;
        if ($racerForfeit == 'y') {
            echo '                <tr><th class="rightAlign">Forfeit:</th><td>Yes</td></tr>' . PHP_EOL;
            if ($racerComment != null) {
                echo '                <tr><th class="rightAlign">Your Comment:</th><td>' . $racerComment . '</td></tr>' . PHP_EOL;
            }
            if ($racerVOD != null) {
                echo '                <tr><th class="rightAlign">Your VOD Link:</th><td>' . $racerVOD . '</td></tr>' . PHP_EOL;
            }
        } else {
            echo '                <tr><th class="rightAlign">Forfeit:</th><td>No</td></tr>' . PHP_EOL;
            echo '                <tr><th class="rightAlign">Your Time:</th><td>' . gmdate('G:i:s', $racerRealTime) . '</td></tr>' . PHP_EOL;
            if ($racerCR != null) {
                echo '                <tr><th class="rightAlign">Your Collection Rate:</th><td>' . $racerCR . '</td></tr>' . PHP_EOL;
            }
            if ($racerComment != null) {
                echo '                <tr><th class="rightAlign">Your Comment:</th><td>' . $racerComment . '</td></tr>' . PHP_EOL;
            }
            if ($racerVOD != null) {
                echo '                <tr><th class="rightAlign">Your VOD Link:</th><td>' . $racerVOD . '</td></tr>' . PHP_EOL;
            }
        }
        echo '            </tbody>' . PHP_EOL;
        echo '        </table>' . PHP_EOL;
    }
}