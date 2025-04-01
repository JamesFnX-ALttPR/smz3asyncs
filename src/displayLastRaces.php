<?php
$rowCounter = 0;
echo '        <table class="searchResults">' . PHP_EOL;
echo '            <caption class="searchResults">Latest Races from Racetime</caption>' . PHP_EOL;
echo '            <thead>' . PHP_EOL;
echo '                <tr><th>Date (UTC)</th><th>Mode</th><th>Description</th><th>Racetime Room</th><th>Seed</th><th>Hash</th><th>Participants</th><th>Async</th><th>Results</th></tr>' . PHP_EOL;
echo '            </thead>' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
$stmt = $pdo->prepare("SELECT id FROM races WHERE raceFromRacetime = 'y' ORDER BY raceStart DESC LIMIT 15");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $rowCounter++;
    if($rowCounter % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    $race_id = $row['id'];
    require ('../includes/race_info.php');
    echo $startOfRow . '<td>' . $race_date . '</td><td>' . $race_mode . '</td><td>' . $race_description . '</td><td><a target="_blank" href="https://racetime.gg/alttpr/' . $race_slug . '">' . $race_slug . '</a></td><td><a target="_blank" href="' . $race_seed . '">Download Seed</a></td><td>' . hashToTable($race_hash) . '</td><td>' . $participant_count . '<td><a href="' . $domain . '/async/' . $race_id . '">Submit Async</a></td><td><a href="' . $domain . '/results/' . $race_id . '">View Results</a></td></tr>' . PHP_EOL;
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;