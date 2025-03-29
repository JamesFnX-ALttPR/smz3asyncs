<?php
require_once ('../includes/bootstrap.php');

// Check if logged in user is an admin
$stmt = $pdo->prepare("SELECT is_admin FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$isAdmin = $stmt->fetchColumn();

// Make sure we got a race ID from the GET request and that it exists
if (! isset($_GET['resultID'])) {
    $pageTitle = 'Error Editing Result';
    require_once ('../includes/header.php');
    echo '        <div class="error">No result selected - Go <a href="' . $domain . '/yourresults">back</a> and try again.</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
    die;
}
$resultID = $_GET['resultID'];
if (is_post_request()) {
    $pageTitle = 'Edit Result';
    require_once ('../includes/header.php');
    require_once ('../src/editResultInDatabase.php');
    require_once ('../includes/footer.php');
} else {
    $stmt = $pdo->prepare("SELECT * FROM results WHERE id = :id");
    $stmt->bindValue(':id', $resultID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if (! $row) {
        $pageTitle = 'Error Editing Result';
        require_once ('../includes/header.php');
        echo '        <div class="error">Result not found - Go <a href="' . $domain . '/yourresults">back</a> and try again.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    // Get all information from DB for this result
    $raceSlug = $row['raceSlug'];
    $racetimeID = $row['racerRacetimeID'];
    $racerTeam = $row['racerTeam'];
    $racerRealTime = $row['racerRealTime'];
    $racerComment = $row['racerComment'];
    $racerForfeit = $row['racerForfeit'];
    $racerCR = $row['racerCheckCount'];
    $racerVODLink = $row['racerVODLink'];
    $enteredBy = $row['enteredBy'];
    $stmt = $pdo->prepare("SELECT racetimeName FROM racerinfo WHERE racetimeID = :racetimeID");
    $stmt->bindValue(':racetimeID', $racetimeID, PDO::PARAM_STR);
    $stmt->execute();
    $racerName = $stmt->fetchColumn();
    // Calculate hours, minutes, seconds of real time to populate edit form
    if ($racerForfeit == 'n') {
        $racerRTSeconds = $racerRealTime % 60;
        $racerRTMinutes = intval(($racerRealTime - $racerRTSeconds) / 60) % 60;
        $racerRTHours = intval(intval(($racerRealTime - $racerRTSeconds) / 60) - $racerRTMinutes) / 60;
    }
    // Make sure this user can edit this result
    if ($isAdmin == 'n' && $enteredBy != $_SESSION['userid']) {
        $pageTitle = 'Error Editing Result';
        require_once ('../includes/header.php');
        echo '        <div class="error">You are not authorized to edit this result - Go <a href="' . $domain . '/yourasyncs">back</a> and try again.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    $stmt = $pdo->prepare("SELECT raceIsTeam, vodRequired, allowResultEdits FROM races WHERE raceSlug = :raceSlug");
    $stmt->bindValue(':raceSlug', $raceSlug, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch();
    $raceIsTeam = $row['raceIsTeam'];
    $vodRequired = $row['vodRequired'];
    $allowEdits = $row['allowResultEdits'];
    if ($isAdmin == 'n' && $allowEdits == 'n') {
        $pageTitle = 'Error Editing Result';
        require_once ('../includes/header.php');
        echo '        <div class="error">The async creator does not allow edit to results - Go <a href="' . $domain . '/yourasyncs">back</a> and try again.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    $pageTitle = 'Edit Result for' . $raceSlug;
    require_once ('../includes/header.php');
    // Build out the form to edit 
    require_once ('../src/inputEditResult.php');
    require_once ('../includes/footer.php');
}