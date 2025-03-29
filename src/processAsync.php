<?php

//Variables passed from form: seed (required), mode (required). hash1-4 (required), description, spoiler, spoilerLog, team, loginRequired, vodRequired, editDisallowed
//Process form input and output errors or confirmation screen

$errors = null; //Set error variable to empty, check for errors at the end
$notes = null; //Things that don't break the submission, but should be presented on the confirmation screen

$newSeed = filter_var($_POST['seed'], FILTER_SANITIZE_URL);
if (! filter_var($newSeed, FILTER_VALIDATE_URL)) { //Check if seed URL is a valid URL
    $errors .= 'Seed is not a valid URL.<br />';
}

$newMode = strip_tags($_POST['mode']);
//Check if mode exists in the database already
$stmt = $pdo->prepare("SELECT id FROM modes WHERE name = :name");
$stmt->bindValue(':name', $newMode, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetchColumn();
if (! $row) {
    $notes .= 'The mode for this seed was not found in our database. Please confirm your input for the mode.<br />';
}

if ($_POST['description'] != '') {
    $newDescription = strip_tags($_POST['description']);
} else {
    $newDescription = '';
}

$newHash = '(' . $_POST['hash1'] . ' ' . $_POST['hash2'] . ' ' . $_POST['hash3'] . ' ' . $_POST['hash4'] . ')';

if (isset($_POST['spoiler'])) { //Check if this is a spoiler seed and validate spoiler log URL
    $newSpoiler = 'y';
    $newSpoilerLog = filter_var($_POST['spoilerLog'], FILTER_SANITIZE_URL);
    if (! filter_var($newSpoilerLog, FILTER_VALIDATE_URL)) { 
        $errors .= 'Spoiler log is not a valid URL.<br />';
    }
} else {
    $newSpoiler = 'n';
}

if (isset($_POST['team'])) {
    $newTeam = 'y';
} else {
    $newTeam = 'n';
}

if (isset($_POST['loginRequired'])) {
    $newLoginRequired = 'y';
} else {
    $newLoginRequired = 'n';
}

if (isset($_POST['vodRequired'])) {
    $newVODRequired = 'y';
} else {
    $newVODRequired = 'n';
}

if (isset($_POST['editDisallowed'])) {
    $newAllowResultEdits = 'n';
} else {
    $newAllowResultEdits = 'y';
}
if (isset($_POST['tournament_seed'])) {
    $newtournament_seed = 'y';
} else {
    $newtournament_seed = 'n';
}

// Check if seed exists in DB, stop and write errors if so
$stmt = $pdo->prepare("SELECT id FROM races WHERE raceSeed = :raceSeed");
$stmt->bindValue(':raceSeed', $newSeed, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetchColumn();
if ($row) {
    $errors .= 'There is an <a href="' . $domain . '/async/' . $row . '">async</a> for this seed already!<br />';
}

if ($errors != null) {
    echo '        <div class="error">' . $errors . 'Please Try Again</div>' . PHP_EOL;
    include('../src/inputAsync.php');
} else {
    echo '        <table class="submitAsync">' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    echo '                <caption>Confirm New Async Submission</caption>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    if ($notes != null) {
        echo '                <tr><td colspan="2">' . $notes . '</td><tr>' . PHP_EOL;
    }
    echo '                <tr><th class="rightAlign">Link to Seed:</th><td>' . $newSeed . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Mode:</th><td>' . $newMode . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Hash:</th><td>' . hashToTable($newHash) . '</td></tr>' . PHP_EOL;
    if ($newDescription != '') {
        echo '                <tr><th class="rightAlign">Description:</th><td>' . $newDescription . '</td></tr>' . PHP_EOL;
    }
    $toggleModes = '';
    if ($newSpoiler == 'y') {
        $toggleModes .= 'Spoiler';
    }
    if ($newTeam == 'y') {
        if ($toggleModes) {
            $toggleModes .= ' - Team/Co-op';
        } else {
            $toggleModes .= 'Team/Co-op';
        }
    }
    if ($newLoginRequired == 'y') {
        if ($toggleModes) {
            $toggleModes .= ' - Login Required';
        } else {
            $toggleModes .= 'Login Required';
        }
    }
    if ($newVODRequired == 'y') {
        if ($toggleModes) {
            $toggleModes .= ' - VOD Required';
        } else {
            $toggleModes .= 'VOD Required';
        }
    }
    if ($newAllowResultEdits == 'n') {
        if ($toggleModes) {
            $toggleModes .= ' - Edits Disallowed';
        } else {
            $toggleModes .= 'Edits Disallowed';
        }
    }
    if ($newtournament_seed == 'y') {
        if ($toggleModes) {
            $toggleModes .= ' - Tournament Seed';
        } else {
            $toggleModes .= 'Tournament Seed';
        }
    }
        if ($toggleModes != '') {
        echo '                <tr><td colspan="2" class="centerAlign">' . $toggleModes . '</td></tr>' . PHP_EOL;
    }
    if ($newSpoiler == 'y') {
        echo '                <tr><th class="rightAlign">Link to Spoiler Log: </th><td>' . $newSpoilerLog . '</td></tr>' . PHP_EOL;
    }
    echo '                <tr><td class="centerAlign" colspan="2" class="submitButton"><form method="post" action="' . $domain . '/createasync"><input type="submit" class="submitButton" value="This is correct!" /> <a href="' . $domain . '/createasync" class="fakeButton">Take me back!</a><input type="hidden" id="approved" name="approved" value="y" /><input type="hidden" id="newSeed" name="newSeed" value="' . $newSeed . '" /><input type="hidden" id="newMode" name="newMode" value="' . $newMode . '" /><input type="hidden" id="newHash" name="newHash" value="' . $newHash . '" />';
    if ($newDescription != '') {
        echo '<input type="hidden" id="newDescription" name="newDescription" value="' . $newDescription . '" />';
    }
    if ($newSpoiler == 'y') {
        echo '<input type="hidden" id="newSpoiler" name="newSpoiler" value="' . $newSpoiler . '" /><input type="hidden" id="newSpoilerLog" name="newSpoilerLog" value="' . $newSpoilerLog . '" />';
    }
    if ($newTeam == 'y') {
        echo '<input type="hidden" id="newTeam" name="newTeam" value="' . $newTeam . '" />';
    }
    if ($newLoginRequired == 'y') {
        echo '<input type="hidden" id="newLoginRequired" name="newLoginRequired" value="' . $newLoginRequired . '" />';
    }
    if ($newVODRequired == 'y') {
        echo '<input type="hidden" id="newVODRequired" name="newVODRequired" value="' . $newVODRequired . '" />';
    }
    if ($newAllowResultEdits == 'n') {
        echo '<input type="hidden" id="newVODRequired" name="newAllowResultEdits" value="' . $newAllowResultEdits . '" />';
    }
    if ($newtournament_seed == 'y') {
        echo '<input type="hidden" id="newtournament_seed" name="newtournament_seed" value="' . $newtournament_seed . '" />';
    }
    echo '</td></tr>' . PHP_EOL;
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
}