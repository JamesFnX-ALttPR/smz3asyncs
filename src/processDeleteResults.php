<?php

// Determine if logged in user is an admin
$stmt = $pdo->prepare("SELECT is_admin FROM asyncusers WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['userid'], PDO::PARAM_STR);
$stmt->execute();
$isAdmin = $stmt->fetchColumn();

$keys = array_keys(array_filter($_POST));
echo '        <table class="submitAsync">' . PHP_EOL;
echo '            <caption>Series Edit Results</caption>' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
foreach ($keys as $str) {
    if (substr($str, 0, 7) == 'result_') {
        $resultID = intval(str_replace('result_', '', $str));
        $stmt = $pdo->prepare("SELECT raceSlug, enteredBy FROM results WHERE id = :id");
        $stmt->bindValue(':id', $resultID, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        $raceSlug = $row['raceSlug'];
        if ($isAdmin == 'y' || $_SESSION['userid'] == $row['enteredBy']) {
            $stmt = $pdo->prepare("DELETE FROM results WHERE id = :id");
            $stmt->bindValue(':id', $resultID, PDO::PARAM_INT);
            $stmt->execute();
            echo '            <tr><td class="centerAlign">Result removed from ' . $raceSlug . '</td></tr>' . PHP_EOL;
        }
    }
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
require_once ('../src/displayUserResults.php');