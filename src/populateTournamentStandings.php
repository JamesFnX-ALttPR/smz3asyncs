<?php
require_once ('../includes/bootstrap_tourney.php');
if (isset ($_GET['tourneyID'])) {
    $tourneyID = strip_tags($_GET['tourneyID']);
    // Clear existing temp table data
    $sql = "TRUNCATE TABLE " . $tourneyID . "_temp";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    // Determine current round
    $sql = "SELECT DISTINCT round FROM " . $tourneyID . "_matches ORDER BY round DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $currentRound = $stmt->fetchColumn();
    $pageTitle = $tourneyID . ' - Standings after Round ' . $currentRound;
    // Get player count and list
    $players = array();
    $active = array();
    $sql = "SELECT player_id, active_player FROM " . $tourneyID . "_players";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $players[] = $row['player_id'];
        $active[] = $row['active_player'];
    }
    $player_count = count($players);
    // Count wins for each player
    for ($i=0; $i<$player_count; $i++) {
        $sql = "SELECT COUNT(id) FROM " . $tourneyID . "_matches WHERE winner = :player_1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':player_1', $players[$i], PDO::PARAM_INT);
        $stmt->execute();
        $player_wins = $stmt->fetchColumn();
        $sql = "INSERT INTO " . $tourneyID . "_temp (player, wins, active) VALUES (:player, :wins, :active)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':player', $players[$i], PDO::PARAM_INT);
        $stmt->bindValue(':wins', $player_wins, PDO::PARAM_INT);
        $stmt->bindValue(':active', $active[$i], PDO::PARAM_STR);
        $stmt->execute();
        unset ($player_wins);
        // Find each player's opponents
        $opponents = array();
        $sql = "SELECT player_1, player_2 FROM " . $tourneyID . "_matches WHERE (player_1 = :player_1 OR player_2 = :player_2)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':player_1', $players[$i], PDO::PARAM_INT);
        $stmt->bindValue(':player_2', $players[$i], PDO::PARAM_INT);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            if ($row['player_1'] != $players[$i] && $row['player_1'] != null) {
                $opponents[] = $row['player_1'];
            }
            if ($row['player_2'] != $players[$i] && $row['player_2'] != null) {
                $opponents[] = $row['player_2'];
            }
        }
        $oppString = implode(', ', $opponents);
        $sql = "UPDATE " . $tourneyID . "_temp SET opponents = :opp WHERE player = :player";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':opp', $oppString, PDO::PARAM_STR);
        $stmt->bindValue(':player', $players[$i], PDO::PARAM_INT);
        $stmt->execute();
    }
    // calculate Tiebreaker 1
    $sql = "SELECT player, opponents FROM " . $tourneyID . "_temp";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $player = $row['player'];
        $oppString = $row['opponents'];
        $opponents = explode(', ', $oppString);
        $tb1 = 0;
        foreach ($opponents as $opp) {
            $sql2 = "SELECT wins FROM " . $tourneyID . "_temp WHERE player = :opp";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindValue(':opp', $opp, PDO::PARAM_STR);
            $stmt2->execute();
            $wins = $stmt2->fetchColumn();
            $losses = $currentRound - $wins;
            $tb1 = $tb1 + $wins;
            $tb1 = $tb1 - $losses;
        }
        $sql2 = "UPDATE " . $tourneyID . "_temp SET tiebreak_1 = :tb1 WHERE player = :player";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindValue(':tb1', $tb1, PDO::PARAM_INT);
        $stmt2->bindValue(':player', $player, PDO::PARAM_INT);
        $stmt2->execute();
    }
    // calculate Tiebreaker 2
    $sql = "SELECT player, opponents FROM " . $tourneyID . "_temp";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $player = $row['player'];
        $oppString = $row['opponents'];
        $opponents = explode(', ', $oppString);
        $tb2 = 0;
        foreach ($opponents as $opp) {
            $sql2 = "SELECT tiebreak_1 FROM " . $tourneyID . "_temp WHERE player = :opp";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindValue(':opp', $opp, PDO::PARAM_STR);
            $stmt2->execute();
            $tb1 = $stmt2->fetchColumn();
            $tb2 = $tb2 + $tb1;
        }
        $sql2 = "UPDATE " . $tourneyID . "_temp SET tiebreak_2 = :tb2 WHERE player = :player";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindValue(':tb2', $tb2, PDO::PARAM_INT);
        $stmt2->bindValue(':player', $player, PDO::PARAM_INT);
        $stmt2->execute();
    }
    // calculate Tiebreaker 3
    $sql = "SELECT player FROM " . $tourneyID . "_temp";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $player = $row['player'];
        $tb3 = 0;
        $sql2 = "SELECT round FROM " . $tourneyID . "_matches WHERE loser = :player";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindValue(':player', $player, PDO::PARAM_STR);
        $stmt2->execute();
        while ($row2 = $stmt2->fetch()) {
            $round = $row2['round'];
            $tb3 = $tb3 + ($round * $round);
        }
        $sql2 = "UPDATE " . $tourneyID . "_temp SET tiebreak_3 = :tb3 WHERE player = :player";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindValue(':tb3', $tb3, PDO::PARAM_INT);
        $stmt2->bindValue(':player', $player, PDO::PARAM_INT);
        $stmt2->execute();
    }
    echo '        <table class="standings">' . PHP_EOL;
    echo '            <caption>Standings After Round' . $currentRound . '</caption>' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    echo '                <tr><th>Place</th><th>Player</th><th>Wins</th><th>Losses</th><th><div title="Sum of opponents\' wins minus sum of opponents\' losses" style="text-decoration: underline;">Tiebreak 1</div></th><th><div title="Sum of opponents\' first tiebreaker" style="text-decoration: underline;">Tiebreak 2</div></th><th><div title="Sum of the squares of rounds player lost in" style="text-decoration: underline;">Tiebreak 3</div></th></tr>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    $place = 0;
    $sql = "SELECT player, wins, tiebreak_1, tiebreak_2, tiebreak_3 FROM " . $tourneyID . "_temp ORDER BY wins DESC, tiebreak_1 DESC, tiebreak_2 DESC, tiebreak_3 DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $place++;
        $playerID = $row['player'];
        $wins = $row['wins'];
        $losses = $currentRound - $wins;
        $tb1 = $row['tiebreak_1'];
        $tb2 = $row['tiebreak_2'];
        $tb3 = $row['tiebreak_3'];
        $sql2 = $pdo->prepare("SELECT display_name FROM players WHERE id = :id");
        $sql2->bindParam(':id', $playerID, PDO::PARAM_INT);
        $sql2->execute();
        $displayName = $sql2->fetchColumn();
        if($place % 2 == 0) {
            $startOfRow = '                <tr class="even">';
        } else {
            $startOfRow = '                <tr class="odd">';
        }
        echo '<td class="place' . $place . '">' . $place . '</td><td>' . $displayName . '</td><td>' . $wins . '</td><td>' . $losses . '</td><td>' . $tb1 . '</td><td>' . $tb2 . '</td><td>' . $tb3 . '</td></tr>' . PHP_EOL;
    }
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
}