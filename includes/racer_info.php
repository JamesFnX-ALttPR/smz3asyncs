<?php

// Input - $racer_id
// Output
//  $racer_name - Display name from Racetime or submitted asyncs
//  $racer_discriminator - Racetime discriminator number
//  $racer_fullname - The full Racetime name with discriminator or the name from a submitted async
// Get information about a racer

$racer_data = $pdo->prepare("SELECT * FROM racerinfo WHERE racetimeID = :id");
$racer_data->bindValue(':id', $racer_id, PDO::PARAM_STR);
$racer_data->execute();
$racer_data_row = $racer_data->fetch();
$racer_name = $racer_data_row['rtgg_name'];
$racer_discriminator = $racer_data_row['rtgg_discriminator'];
if ($racer_discriminator != '') {
    $racer_fullname = $racer_name . '#' . $racer_discriminator;
} else {
    $racer_fullname = $racer_name;
}