<?php

$stmt = $pdo->prepare("SELECT id FROM asyncusers ORDER BY id");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $id = $row['id'];
    $cr_var = $id . '_creator';
    $to_var = $id . '_organizer';
    $ba_var = $id . '_banned';
    $creator = $_POST[$cr_var];
    $organizer = $_POST[$to_var];
    $banned = $_POST[$ba_var];
    $updateStmt = $pdo->prepare("UPDATE asyncusers SET series_flag = :creator, organizer_flag = :organizer, is_banned = :banned WHERE id = :id");
    $updateStmt->bindParam(':creator', $creator, PDO::PARAM_STR);
    $updateStmt->bindParam(':organizer', $organizer, PDO::PARAM_STR);
    $updateStmt->bindParam(':banned', $banned, PDO::PARAM_STR);
    $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $updateStmt->execute();
    unset ($id); unset ($cr_var); unset ($to_var); unset ($ba_var); unset ($creator); unset ($organizer); unset ($banned);
}
echo '        <div class="error">User records have been updated.</div><hr />' . PHP_EOL;
require_once ('../src/inputUserAdmin.php');