<?php
require_once ('../includes/bootstrap.php');

if (! isset($_GET['seriesID'])) {
    $pageTitle = 'Race Series';
    require_once ('../includes/header.php');
    require_once ('../src/displaySeriesList.php');
    require_once ('../includes/footer.php');
} else {
    $seriesID = $_GET['seriesID'];
    $stmt = $pdo->prepare("SELECT series_name FROM series WHERE id = :id");
    $stmt->bindValue(':id', $seriesID, PDO::PARAM_INT);
    $stmt->execute();
    $check = $stmt->fetchColumn();
    if (! $check) {
        $pageTitle = 'Error Displaying Series';
        require_once ('../includes/header.php');
        echo '        <div class="error">Unable to find series. Please try again.</div>' . PHP_EOL;
        require_once ('../src/displaySeriesList.php');
        require_once ('../includes/footer.php');
    } else {
        $pageTitle = 'Series - ' . $check;
        require_once ('../includes/header.php');
        require_once ('../src/displaySeries.php');
        require_once ('../includes/footer.php');
    }
}