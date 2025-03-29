<?php 
if($errorCondition != null) {
    echo '        <div class="error">' . $errorCondition . 'Please Try Again</div>' . PHP_EOL;
    require_once ('../src/asyncPreForm.php');
    require_once ('../src/inputTeam.php');
} else {
    echo '        <table class="submitAsync">' . PHP_EOL;
    echo '            <caption>Confirm Results Submission</caption>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $stmt = $pdo->prepare("SELECT racetimeID, racetimeDiscriminator FROM racerinfo WHERE racetimeName = :name");
    $stmt->bindValue(':name', $racer1Name, PDO::PARAM_STR);
    $stmt->execute();
    $racer1Row = $stmt->fetch();
    if ($racer1Row) {
        $racer1Discrim = $racer1Row['racetimeDiscriminator'];
        $racer1Exists = 'y';
    } else {
        $racer1Discrim = null;
        $racer1Exists = 'n';
    }
    $stmt = $pdo->prepare("SELECT racetimeID, racetimeDiscriminator FROM racerinfo WHERE racetimeName = :name");
    $stmt->bindValue(':name', $racer2Name, PDO::PARAM_STR);
    $stmt->execute();
    $racer2Row = $stmt->fetch();
    if ($racer2Row) {
        $racer2Discrim = $racer2Row['racetimeDiscriminator'];
        $racer2Exists = 'y';
    } else {
        $racer2Discrim = null;
        $racer2Exists = 'n';
    }
    if($racer1Exists == 'n' && $racer2Exists == 'n') {
        echo '                <tr><td colspan="3">We did not find existing runners with your names.<br />Please make sure all data is accurate.</td></tr>' . PHP_EOL;
    } elseif($racer1Exists == 'y' && $racer2Exists == 'y') {
        echo '                <tr><td colspan="3">We found existing runners with your names!<br />Please make sure all data is accurate.</td></tr>' . PHP_EOL;
    } else {
        echo '                <tr><td colspan="3">We found one existing runner with your names!<br />Please make sure all data is accurate.</td></tr>' . PHP_EOL;
    }
    echo '                <tr><td>Team Name: </td><td colspan="2">' . $teamName . '</td></tr>' . PHP_EOL;
    echo '                <tr><td>Your Names: </td><td>' . $racer1Name;
    if($racer1Discrim != null) {
        echo '#' . $racer1Discrim;
    }
    echo '</td><td>' . $racer2Name;
    if($racer2Discrim != null) {
        echo '#' . $racer2Discrim;
    }
    echo '</td></tr>' . PHP_EOL;
    echo '                <tr><td>Real Times: </td><td>' . gmdate('G:i:s', $racer1RealTime) . '</td><td>' . gmdate('G:i:s', $racer2RealTime) . '</td></tr>' . PHP_EOL;
    if($racer1CR != null || $racer2CR != null ) {
        echo '                <tr><td>Collection Rates: </td><td>';
        if($racer1CR != null) {
            echo $racer1CR;
        }
        echo '</td><td>';
        if($racer2CR != null) {
            echo $racer2CR;
        }
        echo '</td></tr>' . PHP_EOL;
    }
    if($racer1Comment != null || $racer2Comment != null ) {
        echo '                <tr><td>Comments: </td><td>';
        if($racer1Comment != null) {
            echo $racer1Comment;
        }
        echo '</td><td>';
        if($racer2Comment != null) {
            echo $racer2Comment;
        }
        echo '</td></tr>' . PHP_EOL;
    }
    if($racer1VOD != null || $racer2VOD != null ) {
        echo '                <tr><td>Links to VODs: </td><td>';
        if($racer1VOD != null) {
            echo $racer1VOD;
        }
        echo '</td><td>';
        if($racer2VOD != null) {
            echo $racer2VOD;
        }
        echo '</td></tr>' . PHP_EOL;
    }
    echo '                <tr><td class="submitButton"><form method="post" action="' . $domain . '/async/' . $raceID . '"><input type="hidden" id="approved" name="approved" value="y" />';
    echo '<input type="hidden" id="teamName" name="teamName" value="' . $teamName . '" />';
    echo '<input type="hidden" id="teamForfeit" name="teamForfeit" value="' . $teamForfeit . '" />';
    echo '<input type="hidden" id="racer1Name" name="racer1Name" value="' . $racer1Name . '" />';
    echo '<input type="hidden" id="racer1RealTime" name="racer1RealTime" value="' . $racer1RealTime . '" />';
    if($racer1CR != null) {
        echo '<input type="hidden" id="racer1CR" name="racer1CR" value="' . $racer1CR . '" />';
    }
    if($racer1Comment != null) {
        echo '<input type="hidden" id="racer1Comment" name="racer1Comment" value="' . $racer1Comment . '" />';
    }
    if($racer1VOD != null) {
        echo '<input type="hidden" id="racer1VOD" name="racer1VOD" value="' . $racer1VOD . '" />';
    }
    echo '<input type="hidden" id="racer2Name" name="racer2Name" value="' . $racer2Name . '" />';
    echo '<input type="hidden" id="racer2RealTime" name="racer2RealTime" value="' . $racer2RealTime . '" />';
    if($racer2CR != null) {
        echo '<input type="hidden" id="racer2CR" name="racer2CR" value="' . $racer2CR . '" />';
    }
    if($racer2Comment != null) {
        echo '<input type="hidden" id="racer2Comment" name="racer2Comment" value="' . $racer2Comment . '" />';
    }
    if($racer2VOD != null) {
        echo '<input type="hidden" id="racer2VOD" name="racer2VOD" value="' . $racer2VOD . '" />';
    }
    echo '<input type="Submit" class="submitButton" value="This is correct!" /></td><td></td><td class="submitAsync"><a href="' . $domain . '/async/' . $raceID . '" class="fakeButton">Take me back!</a></td></tr>' . PHP_EOL;
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
    if (isset($enteredBy)) {
        echo '        <input type="hidden" id="enteredBy" name="enteredBy" value="' . $enteredBy . '" />' . PHP_EOL;
    }
}
