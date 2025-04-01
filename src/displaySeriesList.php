<?php

echo '        <table class="displaySeries">' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
$stmt = $pdo->prepare("SELECT id, series_name, series_description FROM series");
$stmt->execute();
$recordCount = 0;
while ($row = $stmt->fetch()) {
    $recordCount++;
    $id = $row['id'];
    $name = $row['series_name'];
    $desc = $row['series_description'];
    if ($recordCount % 2 == 1) {
        echo '                <tr>';
    }
    echo '<td class="displaySeriesLinks"><div><a href="' . $domain . '/series/' . $id . '">' . $name . '</a></div><br /><div class="displaySeriesDesc">' . $desc . '</td>';
    if ($recordCount % 2 == 0) {
        echo '</tr>' . PHP_EOL;
    }
}
if ($recordCount % 2 == 1) {
    echo '</tr>' . PHP_EOL;
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
