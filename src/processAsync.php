<?php

//Variables passed from form: seed (required), mode (required). hash1-4 (required), description, spoiler, spoiler_log, team, login_required, vod_required, edits_allowed, tournament_seed
//Process form input and output errors or confirmation screen

$errors = null; //Set error variable to empty, check for errors at the end
$notes = null; //Things that don't break the submission, but should be presented on the confirmation screen

$seed = filter_var($_POST['seed'], FILTER_SANITIZE_URL);
if (! filter_var($seed, FILTER_VALIDATE_URL)) { //Check if seed URL is a valid URL
    $errors .= 'Seed is not a valid URL.<br />';
}

$mode = strip_tags($_POST['mode']);
//Check if mode exists in the database already
$stmt = $pdo->prepare("SELECT id FROM modes WHERE name = :name");
$stmt->bindValue(':name', $mode, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetchColumn();
if (! $row) {
    $notes .= 'The mode for this seed was not found in our database. Please confirm your input for the mode.<br />';
}

if ($_POST['description'] != '') {
    $description = strip_tags($_POST['description']);
} else {
    $description = '';
}

$hash = '(' . $_POST['hash1'] . ' ' . $_POST['hash2'] . ' ' . $_POST['hash3'] . ' ' . $_POST['hash4'] . ')';

if (isset($_POST['spoiler'])) { //Check if this is a spoiler seed and validate spoiler log URL
    $spoiler = 'y';
    $spoiler_log = filter_var($_POST['spoiler_log'], FILTER_SANITIZE_URL);
    if (! filter_var($spoiler_log, FILTER_VALIDATE_URL)) { 
        $errors .= 'Spoiler log is not a valid URL.<br />';
    }
} else {
    $spoiler = 'n';
}

if (isset($_POST['team'])) {
    $team = 'y';
} else {
    $team = 'n';
}

if (isset($_POST['login_required'])) {
    $login_required = 'y';
} else {
    $login_required = 'n';
}

if (isset($_POST['vod_required'])) {
    $vod_required = 'y';
} else {
    $vod_required = 'n';
}

if (isset($_POST['edits_allowed'])) {
    $edits_allowed = 'n';
} else {
    $edits_allowed = 'y';
}
if (isset($_POST['tournament_seed'])) {
    $tournament_seed = 'y';
} else {
    $tournament_seed = 'n';
}

// Check if seed exists in DB, stop and write errors if so
$stmt = $pdo->prepare("SELECT id FROM races WHERE raceSeed = :raceSeed");
$stmt->bindValue(':raceSeed', $seed, PDO::PARAM_STR);
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
    echo '                <tr><th class="rightAlign">Link to Seed:</th><td>' . $seed . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Mode:</th><td>' . $mode . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Hash:</th><td>' . hashToTable($hash) . '</td></tr>' . PHP_EOL;
    if ($description != '') {
        echo '                <tr><th class="rightAlign">Description:</th><td>' . $description . '</td></tr>' . PHP_EOL;
    }
    $toggle_modes = '';
    if ($spoiler == 'y') {
        $toggle_modes .= 'Spoiler';
    }
    if ($team == 'y') {
        if ($toggle_modes) {
            $toggle_modes .= ' - Team/Co-op';
        } else {
            $toggle_modes .= 'Team/Co-op';
        }
    }
    if ($login_required == 'y') {
        if ($toggle_modes) {
            $toggle_modes .= ' - Login Required';
        } else {
            $toggle_modes .= 'Login Required';
        }
    }
    if ($vod_required == 'y') {
        if ($toggle_modes) {
            $toggle_modes .= ' - VOD Required';
        } else {
            $toggle_modes .= 'VOD Required';
        }
    }
    if ($edits_allowed == 'n') {
        if ($toggle_modes) {
            $toggle_modes .= ' - Edits Disallowed';
        } else {
            $toggle_modes .= 'Edits Disallowed';
        }
    }
    if ($tournament_seed == 'y') {
        if ($toggle_modes) {
            $toggle_modes .= ' - Tournament Seed';
        } else {
            $toggle_modes .= 'Tournament Seed';
        }
    }
        if ($toggle_modes != '') {
        echo '                <tr><td colspan="2" class="centerAlign">' . $toggle_modes . '</td></tr>' . PHP_EOL;
    }
    if ($spoiler == 'y') {
        echo '                <tr><th class="rightAlign">Link to Spoiler Log: </th><td>' . $spoiler_log . '</td></tr>' . PHP_EOL;
    }
    echo '                <tr><td class="centerAlign" colspan="2" class="submitButton"><form method="post" action="' . $domain . '/createasync"><input type="submit" class="submitButton" value="This is correct!" /> <a href="' . $domain . '/createasync" class="fakeButton">Take me back!</a><input type="hidden" id="approved" name="approved" value="y" /><input type="hidden" id="seed" name="seed" value="' . $seed . '" /><input type="hidden" id="mode" name="mode" value="' . $mode . '" /><input type="hidden" id="hash" name="hash" value="' . $hash . '" />';
    if ($description != '') {
        echo '<input type="hidden" id="description" name="description" value="' . $description . '" />';
    }
    if ($spoiler == 'y') {
        echo '<input type="hidden" id="spoiler" name="spoiler" value="' . $spoiler . '" /><input type="hidden" id="spoiler_log" name="spoiler_log" value="' . $spoiler_log . '" />';
    }
    if ($team == 'y') {
        echo '<input type="hidden" id="team" name="team" value="' . $team . '" />';
    }
    if ($login_required == 'y') {
        echo '<input type="hidden" id="login_required" name="login_required" value="' . $login_required . '" />';
    }
    if ($vod_required == 'y') {
        echo '<input type="hidden" id="vod_required" name="vod_required" value="' . $vod_required . '" />';
    }
    if ($edits_allowed == 'n') {
        echo '<input type="hidden" id="vod_required" name="edits_allowed" value="' . $edits_allowed . '" />';
    }
    if ($tournament_seed == 'y') {
        echo '<input type="hidden" id="tournament_seed" name="tournament_seed" value="' . $tournament_seed . '" />';
    }
    echo '</td></tr>' . PHP_EOL;
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
}