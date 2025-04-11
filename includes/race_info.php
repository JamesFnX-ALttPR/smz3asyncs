<?php

// Input - $race_id
// Gather all data on a race and output to variables
$race_data = $pdo->prepare("SELECT * FROM races WHERE id = :id");
$race_data->bindValue(':id', $race_id, PDO::PARAM_INT);
$race_data->execute();
$race_data_row = $race_data->fetch();
if ($race_data_row) {
    $race_exists = 'y';
    $race_slug = $race_data_row['raceSlug'];
    $race_date = $race_data_row['raceStart'];
    $race_mode = $race_data_row['raceMode'];
    $race_seed = $race_data_row['raceSeed'];
    $race_hash = $race_data_row['raceHash'];
    $race_team_flag = $race_data_row['raceIsTeam'];
    $race_spoiler_flag = $race_data_row['raceIsSpoiler'];
    $race_from_racetime = $race_data_row['raceFromRacetime'];
    $race_vod_flag = $race_data_row['vodRequired'];
    $race_login_flag = $race_data_row['loginRequired'];
    $race_edit_flag = $race_data_row['allowResultEdits'];
    $race_locked_flag = $race_data_row['locked'];
    $race_tournament_flag = $race_data_row['tournament_seed'];
    // If it's a spoiler race, get the spoiler link
    if ($race_spoiler_flag == 'y') {
        $race_spoiler_link = $race_data_row['raceSpoilerLink'];
    }
    // If it's a custom race, get the creator
    if ($race_from_racetime == 'n') {
        $race_created_by = $race_data_row['createdBy'];
        $creator_data = $pdo->prepare("SELECT display_name FROM asyncusers WHERE id = :id");
        $creator_data->bindValue(':id', $race_created_by, PDO::PARAM_INT);
        $creator_data->execute();
        $race_creator = $creator_data->fetchColumn();
    }
    // Build out the description based on the flags
    if ($race_team_flag == 'y') {
        $race_description = 'CO-OP/TEAM';
        $race_description_short = 'CO-OP';
    } else {
        $race_description = '';
        $race_description_short = '';
    }
    $db_description = $race_data_row['raceDescription'];
    if (strlen ($db_description) > 63) {
        if ($race_description == '') {
            $race_description .= $db_description;
            $race_description_short .= substr($db_description, 0, 60) . '...';
        } else {
            $race_description .= ' - ' . $db_description;
            $race_description_short .= ' - ' . substr($db_description, 0, 60) . '...';
        }
    } elseif ($db_description != '') {
        if ($race_description == '') {
            $race_description .= $db_description;
            $race_description_short .= $db_description;
        } else {
            $race_description .= ' - ' . $db_description;
            $race_description_short .= ' - ' . $db_description;
        }
    }
    if ($race_spoiler_flag == 'y') {
        if ($race_description == '') {
            $race_description .= '<a target="_blank" href="' . $race_spoiler_link . '">Link to Spoiler</a>';
            $race_description_short .= '<a target="_blank" href="' . $race_spoiler_link . '">Link to Spoiler</a>';
        } else {
            $race_description .= ' - <a target="_blank" href="' . $race_spoiler_link . '">Link to Spoiler</a>';
            $race_description_short .= ' - <a target="_blank" href="' . $race_spoiler_link . '">Link to Spoiler</a>';
        }
    }
    // Get participant count (racers or teams depending on race_team_flag)
    if ($race_team_flag == 'y') {
        $team_count = $pdo->prepare("SELECT COUNT(DISTINCT racerTeam) FROM results WHERE raceSlug = :slug");
        $team_count->bindValue(':slug', $race_slug, PDO::PARAM_STR);
        $team_count->execute();
        $participant_count = $team_count->fetchColumn();
    } else {
        $player_count = $pdo->prepare("SELECT COUNT(DISTINCT racerRacetimeID) FROM results WHERE raceSlug = :slug");
        $player_count->bindValue(':slug', $race_slug, PDO::PARAM_STR);
        $player_count->execute();
        $participant_count = $player_count->fetchColumn();
    }
} else {
    $race_exists = 'n';
}