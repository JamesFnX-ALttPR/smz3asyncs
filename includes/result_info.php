<?php

// Input - $result_id
// Gather information about a race result

$result_data = $pdo->prepare("SELECT * FROM results WHERE id = :id");
$result_data->bindValue(':id', $result_id, PDO::PARAM_INT);
$result_data->execute();
$result_data_row = $result_data->fetch();
$race_slug = $result_data_row['raceSlug'];
$racer_id = $result_data_row['racerRacetimeID'];
$racer_team = $result_data_row['racerTeam'];
$racer_time = $result_data_row['racerRealTime'];
$racer_comment = $result_data_row['racerComment'];
$racer_forfeit = $result_data_row['racerForfeit'];
$result_from_racetime = $result_data_row['racerFromRacetime'];
$racer_collection_rate = $result_data_row['racerCheckCount'];
$racer_vod = $result_data_row['racerVODLink'];
$racer_user_id = $result_data_row['enteredBy'];

// Get the Racetime name of the racer, returns $racer_name and $racer_fullname
require ('../includes/racer_info.php');