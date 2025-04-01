<?php

// Variables from form page - email, display_name, default_search_range (password1), (password2)

// Get current display name and email for user
$stmt = $pdo->prepare("SELECT email, display_name, default_search_range FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
$currentEmail = $row['email'];
$currentdisplay_name = $row['display_name'];
$currentOffset = $row['default_search_range'];
$errors = '';

$newEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
if (! filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    $errors .= 'Email submitted is <strong>NOT</strong> a valid email address.<br /><br />';
} else {
    if ($newEmail != $currentEmail) {
        $checkDuplicateEmail = $pdo->prepare('SELECT id FROM asyncusers WHERE email = ?');
        $checkDuplicateEmail->bindValue(':email', $newEmail, PDO::PARAM_STR);
        $checkDuplicateEmail->execute();
        $row = $checkDuplicateEmail->fetchColumn();
        if ($row) {
            $errors .= 'Email submitted is registered to another user. Your informtion has not been changed.<br />';
        }
    }
}

// If new passwords have been entered, check and make sure they match and are sufficient
if ($_POST['password1'] != '') {
    if(strlen($_POST['password1']) < 8) {
        $errors .= 'Password must be at least eight characters and contain at least one number, upper case letter, lower case letter, and special character.<br />';
    }
    $pattern = "#.*^(?=.{8,64})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
    if (preg_match($pattern, $_POST['password1']) == false) {
        $errors .= 'Password must be at least eight characters and contain at least one number, upper case letter, lower case letter, and special character.<br />';
    }
}
if ($_POST['password2'] != '') {
    if ($_POST['password2'] != $_POST['password1']) {
        $errors .= 'Passwords do not match. Please make sure they match and try again.<br />';
    }
}

$newdisplay_name = strip_tags($_POST['display_name']);
$newOffset = $_POST['default_search_range'];

//Process errors and update only new information in the database
if ($errors != '') {
    require_once ('../includes/header.php');
    echo '        <div class="error">' . $errors . ' - Please Try Again</div>' . PHP_EOL;
    require_once ('../src/displayAccountInfo.php');
} else {
    if ($newEmail != $currentEmail) {
        $stmt = $pdo->prepare("UPDATE asyncusers SET email = :email WHERE id = :id");
        $stmt->bindValue(':email', $newEmail, PDO::PARAM_STR);
        $stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
        $stmt->execute();
    }
    if ($newOffset != $currentOffset) {
        $stmt = $pdo->prepare("UPDATE asyncusers SET default_search_range = :offset WHERE id = :id");
        $stmt->bindValue(':offset', $newOffset, PDO::PARAM_INT);
        $stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
        $stmt->execute();
    }
    if ($newdisplay_name != $currentdisplay_name) {
        $stmt = $pdo->prepare("UPDATE asyncusers SET display_name = :display_name WHERE id = :id");
        $stmt->bindValue(':display_name', $newdisplay_name, PDO::PARAM_STR);
        $stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['display_name'] = $newdisplay_name;
    }
    if ($_POST['password1'] != '' && $_POST['password2'] != '' && $_POST['password1'] == $_POST['password2']) {
        $stmt = $pdo->prepare("UPDATE asyncusers SET password = :password WHERE id = :id");
        $stmt->bindValue(':password', password_hash($_POST['password1'], PASSWORD_BCRYPT));
        $stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
        $stmt->execute();
        unset($_SESSION);
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    require_once ('../includes/header.php');
    echo '        <div class="error">Account Info Updated!';
    if ($_POST['password1'] != '' && $_POST['password2'] != '' && $_POST['password1'] == $_POST['password2']) {
        echo ' Please log in.</div><br />' . PHP_EOL;
        require_once ('../src/loginForm.php');
    } else {
        echo '</div><br />' . PHP_EOL;
        require_once ('../src/accountHome.php');
    }
}