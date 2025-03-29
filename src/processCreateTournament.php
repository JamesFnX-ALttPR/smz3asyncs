<?php
$errors = null;
// Get information from form submission
if (! isset($_POST['t_name'])) { // If our required field didn't get filled out, something's very wrong
    $errors .= 'Form not submitted properly.<br />';
} else {
    $t_name = strip_tags($_POST['t_name']);
}
$t_desc = strip_tags($_POST['t_desc']);
$t_start = strtotime($_POST['t_start']);
if ($_POST['t_discord'] != '') { // Validate Discord URL is in a valid format
    $t_discord = filter_var($_POST['t_discord'], FILTER_SANITIZE_URL);
    if (! filter_var($t_discord, FILTER_VALIDATE_URL)) {
        $errors .= 'Discord URL not in valid format. Please include https://<br />';
    }
} else {
    $t_discord = '';
}
if ($_POST['t_rulesdoc'] != '') { // Validate rules doc URL is in a valid format
    $t_rulesdoc = filter_var($_POST['t_rulesdoc'], FILTER_SANITIZE_URL);
    if (! filter_var($t_rulesdoc, FILTER_VALIDATE_URL)) {
        $errors .= 'Rules document URL not in valid format. Please include https://<br />';
    }
} else {
    $t_rulesdoc = '';
}
$t_maxplayers = $_POST['t_maxplayers'];
$t_openingrounds = $_POST['t_openingrounds'];
if ($t_openingrounds == 'swiss') {
    if (isset($_POST['t_swissrounds'])) {
        $t_swissrounds = $_POST['t_swissrounds'];
    }
} else {
    $t_swissrounds = null;
}
if ($t_openingrounds == 'groups') {
    if (isset($_POST['t_groupsize'])) {
        $t_groupsize = $_POST['t_groupsize'];
    }
    if (isset($_POST['t_grouprr'])) {
        $t_grouprr = $_POST['t_grouprr'];
    }
} else {
    $t_groupsize = null;
    $t_grouprr = null;
}
if (isset($_POST['t_bracketsize'])) {
    $t_bracketsize = $_POST['t_bracketsize'];
}
$t_losses = $_POST['t_losses'];
// Create unique slug for this tourney
$t_slug = generateTourneySlug();
while (1 == 1) {
    $stmt = $pdo->prepare("SELECT id FROM tournaments WHERE slug = :slug");
    $stmt->bindValue(':slug', $t_slug, PDO::PARAM_STR);
    $stmt->execute();
    if (! $stmt->fetchColumn()) {
        break;
    }
}

if ($errors != null) {
    echo '        <div class="error">' . $errors . 'Please try again.</div>'. PHP_EOL;
    require_once ('../src/inputTournament.php');
} else {
    $stmt = $pdo->prepare("INSERT INTO tournaments (slug, name, description, discord_link, rules_doc, max_players, opening_rounds, swiss_rounds, group_size, group_rr, bracket_size, losses_to_eliminate, start_time, createdBy) VALUES (:t_slug, :t_name, :t_desc, :t_discord, :t_rules, :t_maxplayers, :t_openingrounds, :t_swissrounds, :t_groupsize, :t_grouprr,  :t_bracketsize, :t_losses, :t_start, :createdBy)");
    $stmt->bindvalue(':t_slug', $t_slug, PDO::PARAM_STR);
    $stmt->bindvalue(':t_name', $t_name, PDO::PARAM_STR);
    $stmt->bindvalue(':t_desc', $t_desc, PDO::PARAM_STR);
    $stmt->bindvalue(':t_discord', $t_discord, PDO::PARAM_STR);
    $stmt->bindvalue(':t_rules', $t_rulesdoc, PDO::PARAM_STR);
    $stmt->bindvalue(':t_maxplayers', $t_maxplayers, PDO::PARAM_INT);
    $stmt->bindvalue(':t_openingrounds', $t_openingrounds, PDO::PARAM_STR);
    $stmt->bindvalue(':t_swissrounds', $t_swissrounds, PDO::PARAM_INT);
    $stmt->bindvalue(':t_groupsize', $t_groupsize, PDO::PARAM_INT);
    $stmt->bindvalue(':t_grouprr', $t_grouprr, PDO::PARAM_INT);
    $stmt->bindvalue(':t_bracketsize', $t_bracketsize, PDO::PARAM_INT);
    $stmt->bindvalue(':t_losses', $t_losses, PDO::PARAM_INT);
    $stmt->bindvalue(':t_start', date("Y-m-d H:i:s", $t_start), PDO::PARAM_STR);
    $stmt->bindvalue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    echo '        <table class="input">' . PHP_EOL;
    echo '            <caption>Tournament <span class="italics">' . $t_slug . '</span> Created</caption>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    echo '                <tr><th class="tourneyFormLabel">Tournament Name: </th><td>' . $t_name . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="tourneyFormLabel">Tournament Description: </th><td>' . $t_desc . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="tourneyFormLabel">Start Date: </th><td>' . date("Y-m-d H:i:s", $t_start) . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="tourneyFormLabel">Link to Discord: </th><td><a target="_blank" href="' . $t_discord . '">' . $t_discord . '</a></td></tr>' . PHP_EOL;
    echo '                <tr><th class="tourneyFormLabel">Link to Rules Doc: </th><td><a target="_blank" href="' . $t_rulesdoc . '">' . $t_rulesdoc . '</a></td></tr>' . PHP_EOL;
    echo '                <tr><th class="tourneyFormLabel">Max Players: </th><td>';
    if ($t_maxplayers == 0) {
        echo 'Unlimited';
    } else {
        echo $t_maxplayers;
    }
    echo '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="tourneyFormLabel">First Stage: </th><td>';
    if ($t_openingrounds == 'none') {
        echo 'None - Brackets Only';
    } else {
        echo '<span class="capitalize">' . $t_openingrounds . '</span>';
    }
    echo '</td></tr>' . PHP_EOL;
    if ($t_openingrounds == 'swiss') {
        echo '                <tr><th class="tourneyFormLabel">Rounds of Swiss: </th><td>' . $t_swissrounds . '</td></tr>' . PHP_EOL;
    } elseif ($t_openingrounds == 'groups') {
        echo '                <tr><th class="tourneyFormLabel">Players per Group: </th><td>' . $t_groupsize . '</td></tr>' . PHP_EOL;
        echo '                <tr><th class="tourneyFormLabel">Round Robins per Group: </th><td>' . $t_grouprr . '</td></tr>' . PHP_EOL;
    }
    echo '                <tr><th class="tourneyFormLabel">Players in Bracket Stage: </th><td>' . $t_bracketsize . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="tourneyFormLabel">Bracket Format: </th><td>';
    if ($t_losses == 1) {
        echo 'Single Elimination';
    } elseif ($t_losses == 2) {
        echo 'Double Elimination';
    }
    echo '</td></tr>' . PHP_EOL;
    echo '                <tr><td colspan="2" class="centerAlign">Tourney details and enrolled players here - <a target="_blank" href="' . $domain . '/tournament/' . $t_slug . '">' 
    . $domain . '/tournament/' . $t_slug . '</a></td></tr>' . PHP_EOL;
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
    require_once ('../src/displayTOPortal.php');
}