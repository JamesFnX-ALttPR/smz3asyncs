<?php
$url = 'https://sahasrahbotapi.synack.live/presets/api/alttpr?preset=' . $race_mode;
$data = curlData($url);
$mode_description = parseSynackAPI($data);
$stmt = $pdo->prepare("SELECT name, description FROM modes WHERE name = :mode");
$stmt->bindValue(':mode', $race_mode, PDO::PARAM_STR);
$stmt->execute();
$rslt = $stmt->fetch();
if(! $rslt) {
    $stmt2 = $pdo->prepare("INSERT INTO modes (name, description) VALUES (:mode, :desc)");
    $stmt2->bindValue(':mode', $race_mode, PDO::PARAM_STR);
    $stmt2->bindValue(':desc', $mode_description, PDO::PARAM_STR);
    $stmt2->execute();
} else {
    $stmt2 = $pdo->prepare("UPDATE modes SET description = :desc WHERE name = :mode");
    $stmt2->bindValue(':desc', $mode_description, PDO::PARAM_STR);
    $stmt2->bindValue(':mode', $race_mode, PDO::PARAM_STR);
    $stmt2->execute();
}
?>
        <div class="asyncTopRow">Submit Async for <?php if($race_from_racetime == 'y') { echo '<a target="_blank" href="https://racetime.gg/smz3/' . $race_slug . '">' . $race_slug . '</a>'; } else { echo $race_slug; } ?></div><br />
        <div class="asyncMiddle">Mode: <?= $race_mode ?><br /><?php if ($race_description != '') { echo $race_description . '<br />'; } ?>Seed Link - <a target="_blank" href="<?= $race_seed ?>"><?= $race_seed ?></a> - Hash: <?php echo hashToTable($race_hash); ?><br />
<?php
if ($race_tournament_flag == 'y' && $race_created_by != $user_id) {
    require_once('../src/tournament_async_log.php');
    $stmt = $pdo->prepare('SELECT COUNT(rtgg_name) FROM racerinfo WHERE racetimeID in (SELECT racerRacetimeID FROM results WHERE raceslug = :slug)');
    $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $racerList = $stmt->fetchColumn();
    if (!$racerList) {
        $racerList = 0;
    }
} else {
    $stmt = $pdo->prepare('SELECT rtgg_name FROM racerinfo WHERE racetimeID IN (SELECT racerRacetimeID FROM results WHERE raceSlug = :slug) ORDER BY rtgg_name');
    $stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
    $stmt->execute();
    $racerList = '';
    while($row = $stmt->fetch()) {
        $racerList = $racerList . $row['rtgg_name'] . ', ';
    }
    $racerList = substr($racerList, 0, -2);
}
?>
        Participants: <?= $racerList ?><?php if ($race_from_racetime == 'n') { echo '<br />' . PHP_EOL . '        <span class="new">Created By ' . $race_creator . '</span>'; } ?></div>
        <hr /><?php if ($race_tournament_flag == 'y' && $race_created_by == $user_id) { echo '<div class="asyncBottom">'; $stmt = $pdo->prepare("SELECT asyncusers_id, access_time FROM tourney_async_log WHERE races_id = :id"); $stmt->bindValue(':id', $race_id, PDO::PARAM_INT); $stmt->execute(); while ($row = $stmt->fetch()) { $asyncusers_id = $row['asyncusers_id']; $access_time = strtotime($row['access_time']); $stmt2 = $pdo->prepare("SELECT display_name FROM asyncusers WHERE id = :id2"); $stmt2->bindValue(':id2', $asyncusers_id, PDO::PARAM_INT); $stmt2->execute(); $async_display_name = $stmt2->fetchColumn(); echo PHP_EOL . '        Racer ' . $async_display_name . ' accessed the async at ' . gmdate('F j, Y, h:i:s A', $access_time) . '<br />'; } echo '</div>' . PHP_EOL; } ?>
        <form action="<?= $domain ?>/async/<?= $race_id ?>" method="post" autocomplete="off">
            <table class="submitAsync">
                <caption>Submit Your <?php if ($race_team_flag == 'y') { echo 'Times'; } else { echo 'Time'; } ?></caption>
