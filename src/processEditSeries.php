<?php

$keys = array_keys(array_filter($_POST));
$name = strip_tags($_POST['name']);
$desc = strip_tags($_POST['desc']);
$stmt = $pdo->prepare("UPDATE series SET series_name = :name, series_description = :desc WHERE id = :id");
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':desc', $desc, PDO::PARAM_STR);
$stmt->bindValue(':id', $seriesID, PDO::PARAM_INT);
$stmt->execute();
$memberArray = explode(', ', $series_members);
echo '        <table class="submitAsync">' . PHP_EOL;
echo '            <caption>Series Edit Results</caption>' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
echo '                <tr><td class="rightAlign">Series Name: </td><td>' . $name . '</td></tr>' . PHP_EOL;
echo '                <tr><td class="rightAlign">Series Description: </td><td>' . $desc . '</td></tr>' . PHP_EOL;
foreach ($keys as $str) {
    if (substr($str, 0, 5) == 'seed_') {
        $seedID = intval(str_replace('seed_', '', $str));
        $stmt = $pdo->prepare("SELECT raceSlug FROM races WHERE id = :id");
        $stmt->bindValue(':id', $seedID, PDO::PARAM_INT);
        $stmt->execute();
        $raceSlug = $stmt->fetchColumn();
        if (($key = array_search($seedID, $memberArray)) !== false) {
            unset($memberArray[$key]);
        }
        $memberArray = array_values($memberArray);
        echo '            <tr><td class="centerAlign" colspan="2">Race ' . $raceSlug . ' removed from ' . $name . '</td></tr>' . PHP_EOL;
    }
    $members = implode(', ', $memberArray);
    $stmt = $pdo->prepare("UPDATE series SET series_members = :members WHERE id = :id");
    $stmt->bindValue(':members', $members, PDO::PARAM_STR);
    $stmt->bindValue(':id', $seriesID, PDO::PARAM_INT);
    $stmt->execute();        
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
require_once ('../src/displaySeriesList.php');