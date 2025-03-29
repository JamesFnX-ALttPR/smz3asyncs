<?php

// Variables from inputEditAsync - seed, mode, hash1-5, description, spoiler (spoilerLog), team, loginRequired, vodRequired, allowResultEdits

$raceSeed = filter_var($_POST['seed'], FILTER_SANITIZE_URL);
$raceMode = strip_tags($_POST['mode']);
$hash1 = $_POST['hash1'];
$hash2 = $_POST['hash2'];
$hash3 = $_POST['hash3'];
$hash4 = $_POST['hash4'];
$raceHash = '(' . $hash1 . ' ' . $hash2 . ' ' . $hash3 . ' ' . $hash4 . ')';

if (isset($_POST['description'])) {
    $raceDescription = strip_tags($_POST['description']);
} else {
    $raceDescription = null;
}
if (isset($_POST['spoiler'])) {
    $raceSpoiler = $_POST['spoiler'];
} else {
    $raceSpoiler = 'n';
}
if (isset($_POST['spoilerLog'])) {
    $raceSpoilerLog = filter_var($_POST['spoilerLog'], FILTER_SANITIZE_URL);
} else {
    $raceSpoilerLog = null;
}
if (isset($_POST['team'])) {
    $raceTeam = $_POST['team'];
} else {
    $raceTeam = 'n';
}
if (isset($_POST['loginRequired'])) {
    $raceLoginRequired = $_POST['loginRequired'];
} else {
    $raceLoginRequired = 'n';
}
if (isset($_POST['vodRequired'])) {
    $raceVODRequired = $_POST['vodRequired'];
} else {
    $raceVODRequired = 'n';
}
if (isset($_POST['allowResultEdits'])) {
    $raceAllowResultEdits = 'n';
} else {
    $raceAllowResultEdits = 'y';
}
if (isset($_POST['locked'])) {
    $raceLocked = 'y';
} else {
    $raceLocked = 'n';
}

$errors = null; //Set error variable to empty, check for errors at the end
if (! filter_var($raceSeed, FILTER_VALIDATE_URL)) { //Check if seed URL is a valid URL
    $errors .= 'Seed is not a valid URL.<br />';
}
if (! filter_var($raceSpoilerLog, FILTER_VALIDATE_URL) && $raceSpoiler == 'y') { //Check if seed URL is a valid URL
    $errors .= 'Spoiler log is not a valid URL.<br />';
}

if ($errors != '') {
    echo '        <div class="error">' . $errors . '</div>' . PHP_EOL;
} else {
    $stmt = $pdo->prepare("UPDATE races SET raceMode = :raceMode, raceSeed = :raceSeed, raceHash = :raceHash, raceDescription = :raceDescription, raceIsTeam = :raceIsTeam, raceIsSpoiler = :raceIsSpoiler, raceSpoilerLink = :raceSpoilerLink, vodRequired = :vodRequired, loginRequired = :loginRequired, allowResultEdits = :allowResultEdits, locked = :locked WHERE id = :id");
    $stmt->bindValue(':raceMode', $raceMode, PDO::PARAM_STR);
    $stmt->bindValue(':raceSeed', $raceSeed, PDO::PARAM_STR);
    $stmt->bindValue(':raceHash', $raceHash, PDO::PARAM_STR);
    $stmt->bindValue(':raceDescription', $raceDescription, PDO::PARAM_STR);
    $stmt->bindValue(':raceIsTeam', $raceTeam, PDO::PARAM_STR);
    $stmt->bindValue(':raceIsSpoiler', $raceSpoiler, PDO::PARAM_STR);
    $stmt->bindValue(':raceSpoilerLink', $raceSpoilerLog, PDO::PARAM_STR);
    $stmt->bindValue(':vodRequired', $raceVODRequired, PDO::PARAM_STR);
    $stmt->bindValue(':loginRequired', $raceLoginRequired, PDO::PARAM_STR);
    $stmt->bindValue(':allowResultEdits', $raceAllowResultEdits, PDO::PARAM_STR);
    $stmt->bindValue(':locked', $raceLocked, PDO::PARAM_STR);
    $stmt->bindValue(':id', $raceID, PDO::PARAM_INT);
    $stmt->execute();

    $notes = 'Async Accepted! View your async here:<br /><a href="' . $domain . '/async/' . $raceID . '">' . $domain . '/async/' . $raceID . '</a>';
    echo '        <table class="submitAsync">' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    echo '                <tr><td colspan="2">' . $notes . '</td><tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Link to Seed:</th><td>' . $raceSeed . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Mode:</th><td>' . $raceMode . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Hash:</th><td>' . hashToTable($raceHash) . '</td></tr>' . PHP_EOL;
    if ($raceDescription != '') {
        echo '                <tr><th class="rightAlign">Description:</th><td>' . $raceDescription . '</td></tr>' . PHP_EOL;
    }
    $toggleModes = '';
    if ($raceSpoiler == 'y') {
        $toggleModes .= 'Spoiler';
    }
    if ($raceTeam == 'y') {
        if ($toggleModes != '') {
            $toggleModes .= ' - Team/Co-op';
        } else {
            $toggleModes .= 'Team/Co-op';
        }
    }
    if ($raceLoginRequired == 'y') {
        if ($toggleModes != '') {
            $toggleModes .= ' - Login Required';
        } else {
            $toggleModes .= 'Login Required';
        }
    }
    if ($raceVODRequired == 'y') {
        if ($toggleModes != '') {
            $toggleModes .= ' - VOD Required';
        } else {
            $toggleModes .= 'VOD Required';
        }
    }
    if ($raceAllowResultEdits == 'n') {
        if ($toggleModes != '') {
            $toggleModes .= ' - Edits Disallowed';
        } else {
            $toggleModes .= 'Edits Disallowed';
        }
    }
    if ($raceLocked == 'y') {
        if ($toggleModes != '') {
            $toggleModes .= ' - Locked - No New Results Allowed';
        } else {
            $toggleModes .= 'Locked - No New Results Allowed';
        }
    }
    if ($toggleModes != '') {
        echo '                <tr><td colspan="2" class="centerAlign">' . $toggleModes . '</td></tr>' . PHP_EOL;
    }
    if ($raceSpoiler == 'y') {
        echo '                <tr><td>Link to Spoiler Log: </td><td>' . $raceSpoilerLog . '</td></tr>' . PHP_EOL;
    }
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
}