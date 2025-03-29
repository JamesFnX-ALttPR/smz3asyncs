<?php

require_once ('../includes/bootstrap_tourney.php');

function check_if_paired($slug, $p1, $p2, $conn) {
    $check = "SELECT id FROM " . $slug . "_matches WHERE (player_1 = :player_1a OR player_2 = :player_1b) AND (player_1 = :player_2a OR player_2 = :player_2b)";
    $state = $conn->prepare($check);
    $state->bindValue(':player_1a', $p1, PDO::PARAM_INT);
    $state->bindValue(':player_1b', $p1, PDO::PARAM_INT);
    $state->bindValue(':player_2a', $p2, PDO::PARAM_INT);
    $state->bindValue(':player_2b', $p2, PDO::PARAM_INT);
    $state->execute();
    return $state->fetch();
}

function check_if_bye($slug, $p, $conn) {
    $check = "SELECT id FROM " . $slug . "_matches WHERE player_1 = :player_1 AND player_2 = NULL";
    $state = $conn->prepare($check);
    $state->bindValue(':player_1', $p, PDO::PARAM_INT);
    $state->execute();
    return $state->fetchColumn();
}

function pair_array($a) {
    $pair = array();
    $count = count($a);
    while ($count > 1) {
        while (1 == 1) {
            $key_a = random_int(0, $count - 1);
            $key_b = random_int(0, $count - 1);
            if ($key_a != $key_b) {
                break;
            }
        }
        $p1 = $a[$key_a];
        $p2 = $a[$key_b];
        $pair[] = array($p1, $p2);
        if (($key = array_search($p1, $a)) !== false) {
            unset($a[$key]);
        }
        if (($key = array_search($p2, $a)) !== false) {
            unset($a[$key]);
        }
        $a = array_values($a);
        $count = count($a);
    }
    if ($count == 0) {
        return array($pair, '');
    } else {
        return array($pair, $a);
    }
}

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
    $last_round = $stmt->fetchColumn();
    if (! $last_round) {
        $this_round = 1;
    } else {
        $this_round = $last_round + 1;
    }
    $pageTitle = $tourneyID . ' - Pairing Round ' . $this_round;
    //require_once ('../includes/header.php');
    // Get active player count and list
    $active_players = array();
    $sql = "SELECT player_id FROM " . $tourneyID . "_players WHERE active_player = 'y'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $active_players[] = $row['player_id'];
    }
    $player_count = count($active_players);
    // Count wins for each player
    for ($i=0; $i<$player_count; $i++) {
        $sql = "SELECT COUNT(id) FROM " . $tourneyID . "_matches WHERE winner = :player_1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':player_1', $active_players[$i], PDO::PARAM_INT);
        $stmt->execute();
        $player_wins = $stmt->fetchColumn();
        $sql = "INSERT INTO " . $tourneyID . "_temp (player, wins) VALUES (:player, :wins)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':player', $active_players[$i], PDO::PARAM_INT);
        $stmt->bindValue(':wins', $player_wins, PDO::PARAM_INT);
        $stmt->execute();
        unset ($player_wins);
    }
    // If the player count is odd, someone's getting a bye. Find the lowest win total, find a player without a bye and make them $bye_player, remove them from pairings
    if ($player_count % 2 == 1) {
        $sql = "SELECT DISTINCT wins FROM " . $tourneyID . "_temp ORDER BY wins LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $low_win_total = $stmt->fetchColumn();
        $sql = "SELECT COUNT(wins) FROM " . $tourneyID . "_temp WHERE wins = :wins";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':wins', $low_win_total, PDO::PARAM_INT);
        $stmt->execute();
        $bye_pool_count = $stmt->fetchColumn();
        while (1 == 1) {
            $sql = "SELECT player FROM " . $tourneyID . "_temp WHERE wins = :wins LIMIT :random ,1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':wins', $low_win_total, PDO::PARAM_INT);
            $stmt->bindValue(':random', random_int(0,$bye_pool_count-1), PDO::PARAM_INT);
            $stmt->execute();
            $bye_player = $stmt->fetchColumn();
            if (! check_if_bye($tourneyID, $bye_player, $pdo)) {
                break;
            }
        }
        $sql = "DELETE FROM " . $tourneyID . "_temp WHERE player = :player";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':player', $bye_player, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Create pairing pools
    $pairing_pools = array();
    $sql = "SELECT DISTINCT wins FROM " . $tourneyID . "_temp ORDER BY wins DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $players_in_pool = array();
        $win_total = $row['wins'];
        $sql2 = "SELECT player FROM " . $tourneyID . "_temp WHERE wins = :wins ORDER BY RAND()";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindValue(':wins', $win_total, PDO::PARAM_INT);
        $stmt2->execute();
        while ($row2 = $stmt2->fetch()) {
            $players_in_pool[] = $row2['player'];
        }
        $pairing_pools[] = $players_in_pool;
    }
    //print_r($pairing_pools);
    $pairings = array();
    $leftovers = array();
    foreach($pairing_pools as $array) {
        // If there's leftovers, try to pair them first
        $pool_count = count($array);
        if (count($leftovers) > 0) {
            foreach ($leftovers as $pl) {
                while (1 == 1) {
                    $key_pl = random_int(0, $pool_count - 1);
                    $opp = $array[$key_pl];
                    if (! check_if_paired($tourneyID, $pl, $opp, $pdo)) {
                        $pairings[] = array($pl, $opp);
                        if (($key = array_search($pl, $leftovers)) !== false) {
                            unset($leftovers[$key]);
                        }
                        if (($key = array_search($opp, $array)) !== false) {
                            unset($array[$key]);
                        }
                        $leftovers = array_values($leftovers);
                        $array = array_values($array);
                        break;
                    }
                }
            }
        }
        if ($pool_count == 1) { // If there's only 1 player in this pool, add them to leftovers to get picked up in the next pool
            $leftovers[] = $array[0];
        } elseif ($pool_count == 2)  { // If there's only 2 players in this pool, see if they can play and if not, add them to leftovers
            if (! check_if_paired($tourneyID, $array[0], $array[1], $pdo)) {
                $pairings[] = array($array[0], $array[1]);
            } else {
                $leftovers[] = $array[0];
                $leftovers[] = $array[1];
            }
        } elseif ($pool_count == 3) { // If there's 3 plays in the pool, make special effort to find a valid pairing
            $odd_key_out = random_int(0,2);
            $odd_player_out = $array[$odd_key_out];
            if (($key = array_search($odd_player_out, $array)) !== false) {
                unset($array[$key]);
            }
            $array = array_values($array);
            if (! check_if_paired($tourneyID, $array[0], $array[1], $pdo)) {
                $pairings[] = array($array[0], $array[1]);
                $leftovers[] = $odd_player_out;
            } elseif (! check_if_paired($tourneyID, $array[0], $odd_player_out, $pdo)) {
                $pairings[] = array($array[0], $odd_player_out);
                $leftovers[] = $array[1];
            } elseif (! check_if_paired($tourneyID, $array[1], $odd_player_out, $pdo)) {
                $pairings[] = array($array[1], $odd_player_out);
                $leftovers[] = $array[0];
            } else { // If none of the pairings work, add them all to leftovers
                $leftovers[] = $array[0];
                $leftovers[] = $array[1];
                $leftovers[] = $odd_player_out;
            }
        } else { // For 4 or more players, we are more hands off (though our function gets more hands on with 4 players)
            while (1 == 1) {
                $duplicate = 'n';
                list($temp_pair, $temp_leftover) = pair_array($array);
                foreach($temp_pair as $pair) {
                    if (check_if_paired($tourneyID, $pair[0], $pair[1], $pdo)) {
                        $duplicate = 'y';
                    }
                }
                if ($duplicate == 'n') {
                    $pairings = array_merge(array_values($pairings), array_values($temp_pair));
                    if ($temp_leftover != '') {
                        $leftovers = array_merge(array_values($leftovers), array_values($temp_leftover));
                    }
                    break;
                }
            }
        }
    }
    //print_r ($pairings);
    echo '        <table class="pairings">' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    echo '                <caption>Pairings for Round ' . $this_round . '</caption>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    foreach ($pairings as $pa) {
        if (check_if_paired($tourneyID, $pa[0], $pa[1], $pdo)) {
            echo '                <tr><td class="centerAlign" style="font-weight: bold;">THIS IS A DUPLICATE PAIRING</td></tr>' . PHP_EOL;
        }
        $sql = "INSERT INTO " . $tourneyID . "_matches (round, player_1, player_2) VALUES (:round, :player_1, :player_2)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':round', $this_round, PDO::PARAM_INT);
        $stmt->bindValue(':player_1', $pa[0], PDO::PARAM_INT);
        $stmt->bindValue(':player_2', $pa[1], PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $pdo->prepare("SELECT display_name FROM players WHERE id = :id");
        $stmt->bindValue(':id', $pa[0], PDO::PARAM_INT);
        $stmt->execute();
        $p1_name = $stmt->fetchColumn();
        $stmt = $pdo->prepare("SELECT display_name FROM players WHERE id = :id");
        $stmt->bindValue(':id', $pa[1], PDO::PARAM_INT);
        $stmt->execute();
        $p2_name = $stmt->fetchColumn();
        $sql = "SELECT wins FROM " . $tourneyID . "_temp WHERE player = :player";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':player', $pa[0], PDO::PARAM_INT);
        $stmt->execute();
        $p1_wins = $stmt->fetchColumn();
        $sql = "SELECT wins FROM " . $tourneyID . "_temp WHERE player = :player";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':player', $pa[1], PDO::PARAM_INT);
        $stmt->execute();
        $p2_wins = $stmt->fetchColumn();
        echo '                <tr><td class="centerAlign">' . $p1_name . ' (' . $p1_wins . ') vs. ' . $p2_name . ' (' . $p2_wins . ')</td><tr>' . PHP_EOL;
    }
    if (isset($bye_player)) {
        $sql = "INSERT INTO " . $tourneyID . "_matches (round, player_1, winner) VALUES (:round, :player_1, :winner)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':round', $this_round, PDO::PARAM_INT);
        $stmt->bindValue(':player_1', $bye_player, PDO::PARAM_INT);
        $stmt->bindValue(':winner', $bye_player, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $pdo->prepare("SELECT display_name FROM players WHERE id = :id");
        $stmt->bindValue(':id', $bye_player, PDO::PARAM_INT);
        $stmt->execute();
        $bye_name = $stmt->fetchColumn();
        echo '                <tr><td class="centerAlign">' . $bye_name . ' (' . $low_win_total . ') has a BYE</td></tr>' . PHP_EOL;
    }
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
    require_once ('../includes/footer.php');
} else {
    $pageTitle = 'Error Pairing Round';
    require_once ('../includes/header.php');
    echo '        <div class="error">No tournament with that name was found.</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
}