<?php

// Check if we're logged in; if so, get the default offset from the database instead of assuming 90 days
if (isset($_SESSION['userid'])) {
    $stmt = $pdo->prepare("SELECT searchRange FROM asyncusers WHERE id = :id");
    $stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
    $stmt->execute();
    $offset = $stmt->fetchColumn();
} else {
    $offset = 90;
}

echo '        <form action="' . $domain . '/search" method="post">' . PHP_EOL;
echo '        <table class="search">' . PHP_EOL;
echo '            <tr><th><label for="searchBox" title="Mode and names will autocomplete.">Search For:</label></th><th><label for="startDate">From:</label></th><th><label for="endDate"> To:</label></th></tr>' . PHP_EOL;
echo '            <tr><td class="centerAlign"><input list="searchElements" id="searchBox" name="searchBox" />' . PHP_EOL;
echo '                <datalist id="searchElements">' . PHP_EOL;
$stmt = $pdo->query('SELECT DISTINCT raceMode FROM races ORDER BY raceMode');
foreach($stmt as $row) {
    echo '                    <option value="' . $row['raceMode'] . '"></option>' . PHP_EOL;
}
$stmt = $pdo->query('SELECT DISTINCT racetimeName FROM racerinfo ORDER BY racetimeName');
foreach($stmt as $row) {
    echo '                    <option value="' . $row['racetimeName'] . '"></option>' . PHP_EOL;
}
echo '                </datalist>' . PHP_EOL;
echo '            </td><td class="centerAlign"><input type="date" id="startDate" name="startDate" min="2022-02-21" max="' . date("Y-m-d") . '"';

if ($offset != 0) {
    $offsetString = '-' . $offset . ' days';
    echo ' value="' . date("Y-m-d", strtotime($offsetString)) . '"';
}
echo ' /> </td><td class="centerAlign"><input type="date" id="endDate" name="endDate" min="2022-02-21" max="' . date("Y-m-d") . '" /></td></tr>' . PHP_EOL;
echo '            <tr><th colspan="3"><label for="hash1">OPTIONAL - Search by in-ROM Hash</label></th></tr>' . PHP_EOL;
echo '            <tr><td colspan="3" class="hashSearch">' . PHP_EOL;
echo '                <select id="hash1" name="hash1" class="js-example-basic-single">' . PHP_EOL;
createHashDropdown();
echo '                </select>' . PHP_EOL;
echo '                <select id="hash2" name="hash2" class="js-example-basic-single">' . PHP_EOL;
createHashDropdown();
echo '                </select><br />' . PHP_EOL;
echo '                <select id="hash3" name="hash3" class="js-example-basic-single">' . PHP_EOL;
createHashDropdown();
echo '                </select>' . PHP_EOL;
echo '                <select id="hash4" name="hash4" class="js-example-basic-single">' . PHP_EOL;
createHashDropdown();
echo '                </select>' . PHP_EOL;
echo '            </td></tr>' . PHP_EOL;
echo '            <tr><td colspan="3" class="submitButton"><input class="submitButton" type="submit" value="Go!" /></td></tr>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
echo '        </form>' . PHP_EOL;
