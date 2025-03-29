<?php
$stmt = $pdo->prepare('SELECT count(1) FROM results WHERE raceSlug = ?');
$stmt->execute([$raceSlug]);
$racerCount = $stmt->fetchColumn();
$url = 'https://sahasrahbotapi.synack.live/presets/api/smz3?preset=' . $raceMode;
$data = curlData($url);
$raceModeDesc = parseSynackAPI($data);
$stmt = $pdo->prepare("SELECT name, description FROM modes WHERE name = ?");
$stmt->execute([$raceMode]);
$rslt = $stmt->fetch();
if(! $rslt) {
    $sql = 'INSERT INTO modes (name, description) VALUES (?, ?)';
    $pdo->prepare($sql)->execute([$raceMode, $raceModeDesc]);
} else {
    $sql = 'UPDATE modes SET description = ? WHERE name = ?';
    $pdo->prepare($sql)->execute([$raceModeDesc, $raceMode]);
}
echo '        <div class="asyncTopRow">Submit Async for ';
if($raceFromRacetime == 'y') {
    echo '<a target="_blank" href="https://racetime.gg/smz3/' . $raceSlug . '">' . $raceSlug . '</a>';
} else {
    echo $raceSlug;
}
echo '</div><br />' . PHP_EOL;
echo '        <div class="asyncMiddle">Mode: ' . $raceMode . '<br />' . PHP_EOL;
if($raceIsTeam == 'y') {
    if ($raceDescription == '') {
        $raceDescription = 'CO-OP/TEAM';
    } else {
        $raceDescription = 'CO-OP/TEAM - ' . $raceDescription;
    }
}
if($raceIsSpoiler == 'y') {
    if($raceDescription == '') {
        $raceDescription = '<a target="_blank" href="' . $raceSpoilerLink . '">Download Spoiler Log</a>';
    } else {
        $raceDescription = $raceDescription . ' - <a target="_blank" href="' . $raceSpoilerLink . '">Download Spoiler Log</a>';
    }
}
echo '        ' . $raceDescription . '<br />' . PHP_EOL;
echo '        Seed Link - <a target="_blank" href="' . $raceSeed . '">' . $raceSeed . '</a><br />' . hashToTable($raceHash) . '<br />' . PHP_EOL;
$stmt = $pdo->prepare('SELECT racetimeName FROM racerinfo WHERE racetimeID IN (SELECT racerRacetimeID FROM results WHERE raceSlug = ?) ORDER BY racetimeName');
$stmt->execute([$raceSlug]);
$racerList = '';
while($row = $stmt->fetch()) {
    $racerList = $racerList . $row['racetimeName'] . ', ';
}
$racerList = substr($racerList, 0, -2);
echo '        Participants: ' . $racerList . '</div>' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
echo '        <form action="' . $domain . '/async/' . $raceID .'" method="post" autocomplete="off">' . PHP_EOL;
echo '        <table class="submitAsync">' . PHP_EOL;
echo '            <caption>Submit Your Time';
