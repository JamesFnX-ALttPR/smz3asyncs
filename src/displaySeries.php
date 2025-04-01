<?php
$seriesID = $_GET['seriesID'];
$stmt = $pdo->prepare("SELECT series_name, series_description, series_members FROM series WHERE id = :id");
$stmt->bindValue(':id', $seriesID, PDO::PARAM_INT);
$stmt->execute();
$rslt = $stmt->fetch();
$name = $rslt['series_name'];
$desc = $rslt['series_description'];
$members = $rslt['series_members'];
?>
        <div class="asyncMiddle">Races in <?= $name ?></div><br /><div class="asyncBottom"><?= $desc ?></div>
        <table class="searchResults sortable">
            <thead>
                <tr><th>Date (UTC)</th><th>Mode</th><th>Description</th><th>Racetime Room</th><th>Seed</th><th>Hash</th><th>Participants</th><th>Async</th><th>Results</th></tr>
            </thead>
            <tbody>
<?php 
$memberArray = explode(', ', $members);
if ($memberArray[0] != null) {
    $rowCounter = 0;
    foreach($memberArray as $race_id) {
        $race_id = intval($race_id);
        $rowCounter++;
        require ('../includes/race_info.php');
        if($rowCounter % 2 == 0) {
            $start_of_row = '                <tr class="even">';
        } else {
            $start_of_row = '                <tr class="odd">';
        }
        echo $start_of_row . '<td>' . $race_date . '</td><td>' . $race_mode . '</td><td>' . $race_description_short . '</td><td>';
        if ($race_from_racetime == 'y' ) {
            echo '<a target="_blank" href="https://racetime.gg/smz3/' . $race_slug . '">';
        }
        echo $race_slug;
        if ($race_from_racetime == 'y') {
            echo '</a>';
        }
        echo '</td><td>'; if ($race_tournament_flag == 'y') { echo 'Tournament Async'; } else { echo '<a target="_blank" href="' . $race_seed . '">Download Seed</a>'; } echo '</td><td>' . hashToTable($race_hash) . '</td><td>' . $participant_count . '<td><a href="' . $domain . '/async/' . $race_id . '">Submit Async</a></td><td><a href="' . $domain . '/results/' . $race_id . '">View Results</a></td></tr>' . PHP_EOL;
    }
}
?>
            </tbody>
        </table>