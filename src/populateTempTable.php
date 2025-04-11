<?php // Generate team information in temp table - getting $temp_table_hash from page

// Create table for results
require_once ('../includes/functions.php');
require_once ('../config/settings.php');
$sql = "CREATE TABLE IF NOT EXISTS temp_" . $temp_table_hash . " (teamName varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, averageTime mediumint DEFAULT NULL, averageIGT mediumint DEFAULT NULL, averageCR smallint DEFAULT NULL, teamForfeit varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
$stmt = $pdo->prepare($sql);
$stmt->execute();


// Get list of teams in a race
$stmt = $pdo->prepare("SELECT DISTINCT racerTeam FROM results WHERE raceSlug = :slug");
$stmt->bindValue(':slug', $race_slug, PDO::PARAM_STR);
$stmt->execute();

// Determine if anyone on the team forfeitted
while($row = $stmt->fetch()) {
    $racer_team = $row['racerTeam'];
    $team_forfeit = 'n';
    $stmt2 = $pdo->prepare("SELECT racerForfeit FROM results WHERE raceSlug = :slug AND racerTeam = :team");
    $stmt2->bindValue(':slug', $race_slug, PDO::PARAM_STR);
    $stmt2->bindValue(':team', $racer_team, PDO::PARAM_STR);
    $stmt2->execute();
    while($row2 = $stmt2->fetch()) {
        if($row2['racerForfeit'] == 'y') {
            $team_forfeit = 'y';
        }
    }
    if($team_forfeit == 'y') { // Mark team as forfeitted if any player did
        $sql3 = "INSERT INTO temp_" . $temp_table_hash . " (teamName, teamForfeit) VALUES (:team, 'y')";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->bindValue(':team', $racer_team, PDO::PARAM_STR);
        $stmt3->execute();
    } else { // Get last times and collection rate if no player forfeitted
        $stmt3 = $pdo->prepare("SELECT racerRealTime FROM results WHERE raceSlug = :slug AND racerTeam = :team ORDER BY racerRealTime DESC LIMIT 1");
        $stmt3->bindParam(':slug', $race_slug, PDO::PARAM_STR);
        $stmt3->bindParam(':team', $racer_team, PDO::PARAM_STR);
        $stmt3->execute();
        $team_average = $stmt3->fetchColumn();
        $sqlTemp = "INSERT INTO temp_" . $temp_table_hash . " (teamForfeit, teamName, averageTime";
        $variableCount = 2;
        $crGather = 'n';
        if($check_count > 0) {
            $stmt3 = $pdo->prepare("SELECT AVG(racerCheckCount) FROM results WHERE raceSlug = :slug AND racerTeam = :team AND racerCheckCount IS NOT NULL");
            $stmt3->bindValue(':slug', $race_slug, PDO::PARAM_STR);
            $stmt3->bindValue(':team', $racer_team, PDO::PARAM_STR);
            $stmt3->execute();
            $team_cr_average = $stmt3->fetchColumn();
            $sqlTemp = $sqlTemp . ", averageCR";
            $variableCount++;
            $crGather = 'y';
        }
        $sqlTemp = $sqlTemp . ") VALUES ('n', :team, :time";
        if($variableCount == 2) {
            $sqlTemp = $sqlTemp . ")";
            $stmt3 = $pdo->prepare($sqlTemp);
            $stmt3->bindValue(':team', $racer_team, PDO::PARAM_STR);
            $stmt3->bindValue(':time', $team_average, PDO::PARAM_INT);
            $stmt3->execute();
        }
        else {
            $sqlTemp = $sqlTemp . ", :cr)";
            $stmt3 = $pdo->prepare($sqlTemp);
            $stmt3->bindValue(':team', $racer_team, PDO::PARAM_STR);
            $stmt3->bindValue(':time', $team_average, PDO::PARAM_INT);
            $stmt3->bindValue(':cr', $team_cr_average, PDO::PARAM_INT);
            $stmt3->execute();
        }
    }
 }
