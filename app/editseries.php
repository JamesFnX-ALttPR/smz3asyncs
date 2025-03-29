<?php
require_once ('../includes/bootstrap.php');
if(!isset($_GET['seriesID'])) {
    $pageTitle = 'Error Editing Series';
    require_once ('../includes/header.php');
    echo '        <div class="error">No Series Selected - Try again from <a href="' . $domain . '/yourseries">your Series</a> page.</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
    die;
} elseif (!isset($_SESSION['userid'])) {
    $pageTitle = 'Error Editing Series';
    require_once ('../includes/header.php');
    echo '        <div class="error">You are not authorized to edit this series. Please <a href="' . $domain . '/login">login</a> and try again.</div>' . PHP_EOL;
    require_once ('../src/loginForm.php');
    require_once ('../includes/footer.php');
    die;
} else {
    $seriesID = $_GET['seriesID'];
    $stmt = $pdo->prepare("SELECT seriesName, seriesDescription, seriesMembers, createdBy FROM series WHERE id = :id");
    $stmt->bindValue(':id', $seriesID, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    $seriesName = $row['seriesName'];
    $seriesDesc = $row['seriesDescription'];
    $seriesMembers = $row['seriesMembers'];
    $createdBy = $row['createdBy'];
    // Check if we're able to edit this series.
    $stmt = $pdo->prepare("SELECT is_admin, is_seriesMaker FROM asyncusers WHERE id = :id");
    $stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    $isAdmin = $row['is_admin'];
    $isSeriesMaker = $row['is_seriesMaker'];
    if ($isAdmin == 'y' || ($isSeriesMaker == 'y' && $createdBy == $_SESSION['userid'])) {
        $pageTitle = "Editing " . $seriesName;
        require_once ('../includes/header.php');
        if (is_post_request()) {
            require_once('../src/processEditSeries.php');
        } else {
            require_once('../src/inputEditSeries.php');
        }
        require_once('../includes/footer.php');
    } else {
        $pageTitle = 'Error Editing Series';
        require_once ('../includes/header.php');
        echo '        <div class="error">You are not authorized to edit this series. Please <a href="' . $domain . '/login">login</a> and try again.</div>' . PHP_EOL;
        require_once ('../src/loginForm.php');
        require_once ('../includes/footer.php');
        die;
    }
}
