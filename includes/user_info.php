<?php

// Inputs - $user_id
// Outputs - $admin_flag, $series_flag, $organizer_flag
// Script for consistent flag setting of users across multiple pages

$user_id = $_SESSION['userid'];
$stmt = $pdo->prepare("SELECT display_name, admin_flag, series_flag, organizer_flag FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
$user_display_name = $row['display_name'];
$admin_flag = $row['admin_flag'];
$series_flag = $row['series_flag'];
$organizer_flag = $row['organizer_flag'];
unset ($row);
unset ($stmt);