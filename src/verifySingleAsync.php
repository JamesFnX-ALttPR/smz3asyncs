<?php

if($errorCondition != null) {
    echo '        <div class="error">' . $errorCondition . ' - Please Try Again</div>' . PHP_EOL;
    require_once ('../src/asyncPreForm.php');
    require_once ('../src/inputSingleRunner.php');
} else {
    echo '        <table class="submitAsync">' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    echo '                <caption>Confirm Results Submission</caption>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $stmt = $pdo->prepare("SELECT * FROM racerinfo WHERE racetimeName = :name");
    $stmt->bindParam(':name', $racerName, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch();
    if(! $row) {
        echo '                <tr><td colspan="2">We did not find an existing runner with your name.<br />Please make sure all data is accurate.</td></tr>' . PHP_EOL;
        $racerDiscrim = null;
    } else {
        $racerDiscrim = $row['racetimeDiscriminator'];
        echo '                <tr><td colspan="2">We found an existing runner with your name!<br />Please make sure all data is accurate.</td></tr>' . PHP_EOL;
    }
    echo '                <tr><td>Your Name: </td><td>' . $racerName;
    if($racerDiscrim != null) {
        echo '#' . $racerDiscrim;
    }
    echo '</td>' . PHP_EOL;
    echo '                <tr><td>Your Real Time: </td><td>' . gmdate('G:i:s', $racerRealTime) . '</td>' . PHP_EOL;
    if($racerCR != null) {
        echo '                <tr><td>Your Collection Rate: </td><td>' . $racerCR . '</td>' . PHP_EOL;
    }
    if($racerComment != null) {
        echo '                <tr><td>Your Comments: </td><td>' . $racerComment . '</td>' . PHP_EOL;
    }
    if($racerVOD != null) {
        echo '                <tr><td>Your VOD Link: </td><td>' . $racerVOD . '</td>' . PHP_EOL;
    }
    echo '                <tr><td class="submitButton"><form method="post" action="' . $domain . '/async/' . $raceID . '"><input type="hidden" id="approved" name="approved" value="y" />';
    echo '<input type="hidden" id="racer1Name" name="racer1Name" value="' . $racerName . '" />';
    echo '<input type="hidden" id="racer1RealTime" name="racer1RealTime" value="' . $racerRealTime . '" />';
    echo '<input type="hidden" id="racer1Forfeit" name="racer1Forfeit" value="' . $racerForfeit . '" />';
    if($racerCR != null) {
        echo '<input type="hidden" id="racer1CR" name="racer1CR" value="' . $racerCR . '" />';
    }
    if($racerComment != null) {
        echo '<input type="hidden" id="racer1Comment" name="racer1Comment" value="' . $racerComment . '" />';
    }
    if($racerVOD != null) {
        echo '<input type="hidden" id="racer1VOD" name="racer1VOD" value="' . $racerVOD . '" />';
    }
    echo '<input type="Submit" class="submitButton" value="This is correct!" /></td><td class="submitAsync"><a href="' . $domain . '/async/' . $raceID . '" class="fakeButton">Take me back!</a></td></tr>' . PHP_EOL;
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
    if (isset($enteredBy)) {
        echo '        <input type="hidden" id="enteredBy" name="enteredBy" value="' . $enteredBy . '" />' . PHP_EOL;
    }
}
