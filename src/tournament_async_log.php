<?php
$ip_address = $_SERVER['REMOTE_ADDR'];
$stmt = $pdo->prepare("INSERT INTO tourney_async_log (asyncusers_id, races_id, ip_address, access_time) VALUES (:user_id, :race_id, :ip_address, NOW())");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':race_id', $race_id, PDO::PARAM_INT);
$stmt->bindValue(':ip_address', $ip_address, PDO::PARAM_STR);
$stmt->execute();