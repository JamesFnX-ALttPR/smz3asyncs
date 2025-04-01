<?php
require_once ('../includes/bootstrap.php');
// Check if user is logged in - if not, present the login page instead
require_once ('../includes/user_info.php');
if (!isset ($user_id)) {
    $pageTitle = 'Error Editing Async';
    require_once ('../includes/header.php');
    echo '        <div class="error">You must be logged in to create asyncs. Please log in.</div>' . PHP_EOL;
    require_once ('../src/loginForm.php');
    die;
}

// Make sure we got a race ID from the GET request and that it exists
if (! isset($_GET['raceID'])) {
    $pageTitle = 'Error Editing Async';
    require_once ('../includes/header.php');
    echo '        <div class="error">No race selected - Go <a href="' . $domain . '/yourasyncs">back</a> and try again.</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
    die;
}
$race_id = $_GET['raceID'];
if (is_post_request()) {
    $pageTitle = 'Edit Async';
    require_once ('../includes/header.php');
    require_once ('../src/editAsyncInDatabase.php');
    require_once ('../includes/footer.php');
} else {
    require_once ('../includes/race_info.php');
    [$hash1, $hash2, $hash3, $hash4] = unparseHash($race_hash);
    // Make sure this user can edit this race
    if ($admin_flag == 'n' && $race_created_by != $_SESSION['userid']) {
        $pageTitle = 'Error Editing Async';
        require_once ('../includes/header.php');
        echo '        <div class="error">You are not authorized to edit this async - Go <a href="' . $domain . '/yourasyncs">back</a> and try again.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    $pageTitle = 'Edit Async for' . $raceSlug;
    require_once ('../includes/header.php');
    // Build out the form to edit
    require_once ('../src/inputEditAsync.php');
    require_once ('../includes/footer.php');
}