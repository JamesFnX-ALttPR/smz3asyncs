<?php

$errors = '';
$stmt = $pdo->prepare("SELECT id, password, is_admin, displayName FROM asyncusers WHERE email = :email");
$stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch();
if (! $row) {
    $errors .= 'y';
} else {
    if (password_verify($_POST['password'], $row['password']) == true) {
        session_regenerate_id();
        $_SESSION['userid'] = $row['id'];
        $_SESSION['displayName'] = $row['displayName'];
        $login_ip = $_SERVER['REMOTE_ADDR'];
        $stmt2 = $pdo->prepare("UPDATE asyncusers SET last_login_ip = :ip, last_login_date = NOW() WHERE email = :email");
        $stmt2->bindParam(':ip', $login_ip, PDO::PARAM_STR);
        $stmt2->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt2->execute();
        header('Location: '. $domain . $_POST['redirectTo']);
        exit();
    } else {
        $errors .= 'y';
    }
}