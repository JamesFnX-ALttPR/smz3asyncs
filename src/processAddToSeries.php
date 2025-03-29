<?php

$raceAdded = 'n';
foreach ($_POST as $s) {
    if ($s != '') {
        $raceAdded = 'y';
    }
}
if ($raceAdded == 'y') {
    $keys = array_keys(array_filter($_POST));
    echo '        <table class="submitAsync">' . PHP_EOL;
    echo '            <caption>Races Added to Series</caption>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    foreach ($keys as $str) {
        $seedID = intval(str_replace('seed_', '', $str));
        $seriesID = $_POST[$str];
        if ($seriesID != '') {
            $stmt = $pdo->prepare("SELECT seriesName, seriesMembers FROM series WHERE id = :id");
            $stmt->bindValue(':id', $seriesID, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            $name = $row['seriesName'];
            $members = $row['seriesMembers'];
            if ($members == null) {
                $members = strval($seedID);
            } else {
                $members = $members . ', ' . $seedID;
            }
            $stmt = $pdo->prepare("UPDATE series SET seriesMembers = :members WHERE id = :id");
            $stmt->bindValue(':members', $members, PDO::PARAM_STR);
            $stmt->bindValue(':id', $seriesID, PDO::PARAM_INT);
            $stmt->execute();
            echo '            <tr><td class="centerAlign">Race ' . $seedID . ' added to ' . $name . '</td></tr>' . PHP_EOL;
        }
    }
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
    echo '        <hr />' . PHP_EOL;
    require_once ('../src/displaySeriesList.php');
} else {
    echo '        <div class="error">No races selected. Please try again.</div>' . PHP_EOL;
    echo '        <hr />' . PHP_EOL;
    require_once ('../src/inputSearch.php');
}
