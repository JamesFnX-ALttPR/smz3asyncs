<?php
require_once ('../includes/bootstrap.php');

// Check if logged in user is an admin
$stmt = $pdo->prepare("SELECT is_admin FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$isAdmin = $stmt->fetchColumn();

// Make sure we got a race ID from the GET request and that it exists
if (! isset($_GET['raceID'])) {
    $pageTitle = 'Error Editing Async';
    require_once ('../includes/header.php');
    echo '        <div class="error">No race selected - Go <a href="' . $domain . '/yourasyncs">back</a> and try again.</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
    die;
}
$raceID = $_GET['raceID'];
if (is_post_request()) {
    $pageTitle = 'Edit Async';
    require_once ('../includes/header.php');
    require_once ('../src/editAsyncInDatabase.php');
    require_once ('../includes/footer.php');
} else {
        $stmt = $pdo->prepare("SELECT * FROM races WHERE id = :id");
    $stmt->bindValue(':id', $raceID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if (! $row) {
        $pageTitle = 'Error Editing Async';
        require_once ('../includes/header.php');
        echo '        <div class="error">Race not found - Go <a href="' . $domain . '/yourasyncs">back</a> and try again.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    // Get all information from DB for this race
    $raceSlug = $row['raceSlug'];
    $raceMode = $row['raceMode'];
    $raceSeed = $row['raceSeed'];
    $raceHash = $row['raceHash'];
    // Break down the hash into its components
    [$hash1, $hash2, $hash3, $hash4, $hash5] = unparseHash($raceHash);
    $raceDescription = $row['raceDescription'];
    $raceIsTeam = $row['raceIsTeam'];
    $raceIsSpoiler = $row['raceIsSpoiler'];
    $raceSpoilerLink = $row['raceSpoilerLink'];
    $raceFromRacetime = $row['raceFromRacetime'];
    $vodRequired = $row['vodRequired'];
    $loginRequired = $row['loginRequired'];
    $allowResultEdits = $row['allowResultEdits'];
    $locked = $row['locked'];
    $tournament = $row['tournament_seed'];
    $createdBy = $row['createdBy'];
    // Make sure this user can edit this race
    if ($isAdmin == 'n' && $createdBy != $_SESSION['userid']) {
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