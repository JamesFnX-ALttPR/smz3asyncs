<?php

require_once ('../includes/bootstrap.php');
require_once ('../includes/user_info.php');
if(!isset($_GET['raceID'])) {
    $pageTitle = 'Error Viewing Async';
    require_once ('../includes/header.php');
    echo '        <div class="error">No Race Selected - Please <a href="' . $domain . '/search">search</a> for a race</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
    die;
} else {
    $race_id = $_GET['raceID'];
    $stmt = $pdo->prepare("SELECT * FROM races WHERE id = :id");
    $stmt->bindValue(':id', $race_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if(! $row) {
        $pageTitle = 'Error Viewing Async';
        require_once ('../includes/header.php');
        echo '        <div class="error">No Race Found - Please try <a href="' . $domain . '/search">searching</a> again</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die;
    }
    require ('../includes/race_info.php');
    $pageTitle = 'View Times for ' . $race_slug;
    if (is_post_request() && ($_SESSION['userid'] == $race_created_by || $admin_flag == 'y')) {
        $fields = array("'place'", "'name'", "'team'", "'rt_seconds'", "'cr'", "'comment'", "'forfeit'", "'vod_link'");
        $delimiter = ',';
        $filename = 'smz3asyncs-' . $race_slug . date("Y-m-d H:i:s") . '.csv';
        $f = fopen('php://output', 'w');
        // In case, if php://output didn't work, uncomment below line
        // $f = fopen("php://memory", "w"); 
        fputcsv($f, $fields, $delimiter);
        $place = 0;
        $stmt = $pdo->prepare("SELECT id FROM results WHERE raceSlug = :slug AND racerForfeit = 'n' ORDER BY racerRealTime");
        $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $place++;
            $result_id = $row['id'];
            require ('../includes/result_info.php');
            $row_data = array($place, $racer_name, $racer_team, $racer_time, $racer_collection_rate, $racer_comment, $racer_forfeit, $racer_vod);
            fputcsv($f, $row_data, $delimiter);
        }
        fclose ($f);
        // If case fclose does not work, uncomment fseek() and fpassthru().
        // fseek($f, 0);
        // Telling browser to download file as CSV
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment; filename="'.$filename.'";'); 
        // fpassthru($f);
        exit();
    }
}
require_once ('../includes/header.php');
if ($race_login_flag == 'y' && ! isset($_SESSION['userid'])) {
    echo '        <div class="error">You must log in to submit or view results for this async.</div><br />' . PHP_EOL;
    include ('../src/loginForm.php');
} elseif ($race_tournament_flag == 'y' && ! isset($_SESSION['userid'])) {
    echo '        <div class="error">You must log in to submit or view results for this async.</div><br />' . PHP_EOL;
    include ('../src/loginForm.php');
} elseif ($race_tournament_flag == 'y') {
    $stmt = $pdo->prepare('SELECT COUNT(id) FROM results WHERE raceSlug = :slug AND enteredBy = :id');
    $stmt->bindParam(':slug', $race_slug, PDO::PARAM_STR);
    $stmt->bindParam(':id', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    $rslt = $stmt->fetchColumn();
    if (!$rslt && $race_created_by != $_SESSION['userid']) {
        echo '        <div class="error">Only racers who have submitted a result may view results for this async.<br />Click <a href="' . $domain . '/async/' . $raceID . '">here</a> to submit a result.</div><br />' . PHP_EOL;
    }
} else {
    require_once ('../src/displayResults.php');
}
require_once ('../includes/footer.php');