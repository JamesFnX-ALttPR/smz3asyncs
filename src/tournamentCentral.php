<?php

// Display active tournaments first

echo '        <table class="tournamentCentral">' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
echo '                <tr><td colspan="2" class="tcHeader">Active Tournaments<br /></td></tr>' . PHP_EOL;
$row_count = 0;
$stmt = $pdo->prepare("SELECT slug, name, description, start_time FROM tournaments WHERE complete = 'n'");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $row_count++;
    if ($row_count % 2 == 1) {
        echo '                <tr>';
    }
    echo '<td><div class="tcLinks"><a href="' . $domain . '/tourney/' . $row['slug'] . '">' . $row['name'] . '</a></div><br /><div class="tcDesc">';
    if (isset($_SESSION['userid'])) {
        $sql = "SELECT id FROM " . $row['slug'] . "_players WHERE player_id = :id";
        $stmt2 = $pdo->prepare($sql);
        $stmt2->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
        $stmt2->execute();
        if ($stmt2->fetchColumn()) {
            echo '<span class="italics">You are enrolled in this tournament!</span><br />';
        }
    }
    echo $row['description'] . '<br />Starts ' . $row['start_time'] . ' (';
    if (date('I') == 1) {
        echo 'EDT';
    } else {
        echo 'EST';
    }
    echo ')</div></td>';
    if ($row_count % 2 == 0) {
        echo '</tr>' . PHP_EOL;
    }
}
if ($row_count % 2 == 1) {
    echo '</tr>' . PHP_EOL;
}
echo '                <tr><td colspan="2" class="tcHeader"><br />Completed Tournaments<br /></td></tr>' . PHP_EOL;
$row_count = 0;
$stmt = $pdo->prepare("SELECT slug, name, description FROM tournaments WHERE complete = 'y'");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $row_count++;
    if ($row_count % 2 == 1) {
        echo '                <tr>';
    }
    echo '<td><div class="tcLinks"><a href="' . $domain . '/tourney/' . $row['slug'] . '">' . $row['name'] . '</a></div><br /><div class="tcDesc">' . $row['description'] . '</div></td>';
    if ($row_count % 2 == 0) {
        echo '</tr>' . PHP_EOL;
    }
}
if ($row_count % 2 == 1) {
    echo '</tr>' . PHP_EOL;
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;