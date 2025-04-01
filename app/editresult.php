<?php
require_once ('../includes/bootstrap.php');

// Check if logged in user is an admin
require_once ('../includes/user_info.php');

// Make sure we got a race ID from the GET request and that it exists
if (! isset($_GET['resultID'])) {
    $pageTitle = 'Error Editing Result';
    require_once ('../includes/header.php');
    echo '        <div class="error">No result selected - Go <a href="' . $domain . '/yourresults">back</a> and try again.</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
    die;
}
$result_id = $_GET['resultID'];
if (is_post_request()) {
    $pageTitle = 'Edit Result';
    require_once ('../includes/header.php');
    require_once ('../src/editResultInDatabase.php');
    require_once ('../includes/footer.php');
} else {
    // Get all information from DB for this result
    require_once ('../includes/result_info.php');
    if ($result_not_found) { // If the result's not found, throw an error
        $pageTitle = 'Error Editing Result';
        require_once ('../includes/header.php');
        echo '        <div class="error">Result not found - Go <a href="' . $domain . '/yourresults">back</a> and try again.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    if ($racer_forfeit == 'n') {
        $racerRTSeconds = $racer_time % 60;
        $racerRTMinutes = intval(($racer_time - $racerRTSeconds) / 60) % 60;
        $racerRTHours = intval(intval(($racer_time - $racerRTSeconds) / 60) - $racerRTMinutes) / 60;
    }
    // Make sure this user can edit this result
    if ($admin_flag == 'n' && $racer_user_id != $_SESSION['userid']) {
        $pageTitle = 'Error Editing Result';
        require_once ('../includes/header.php');
        echo '        <div class="error">You are not authorized to edit this result - Go <a href="' . $domain . '/yourasyncs">back</a> and try again.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    $stmt = $pdo->prepare("SELECT id FROM races WHERE raceSlug = :raceSlug");
    $stmt->bindValue(':raceSlug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $race_id = $stmt->fetchColumn();
    require_once ('../includes/race_info.php');
    if ($admin_flag == 'n' && $race_edit_flag == 'n') {
        $pageTitle = 'Error Editing Result';
        require_once ('../includes/header.php');
        echo '        <div class="error">The async creator does not allow edit to results - Go <a href="' . $domain . '/yourasyncs">back</a> and try again.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    $pageTitle = 'Edit Result for' . $race_slug;
    require_once ('../includes/header.php');
    // Build out the form to edit
    require_once ('../src/inputEditResult.php');
    require_once ('../includes/footer.php');
}