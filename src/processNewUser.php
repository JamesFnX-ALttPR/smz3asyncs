<?php
 
 $errors = '';
 // Check if email is in valid format and is not already registered
 if ($_POST['email'] != '') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors .= 'Email submitted is <strong>NOT</strong> a valid email address.<br />';
    } else {
        $checkDuplicateEmail = $pdo->prepare('SELECT id FROM asyncusers WHERE email = ?');
        $checkDuplicateEmail->execute([$email]);
        $row = $checkDuplicateEmail->fetchColumn();
        if ($row) {
            $errors .= 'Email submitted is already registered, please try logging in.<br />';
        }
    }
} else {
    $errors .= 'Please enter your email address.<br />';
}

// Check if password is in a valid format
if ($_POST['password'] != '') {
    if(strlen($_POST['password']) < 8) {
        $errors .= 'Password does not meet requirements.<br />';
    }
    $pattern = "#.*^(?=.{8,64})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
    if (preg_match($pattern, $_POST['password']) == false && strlen($_POST['password']) < 16) {
        $errors .= 'Password does not meet requirements.<br />';
    }
} else {
    $errors .= 'Please enter a new password.<br />';
}

// Check if password fields match
if ($_POST['confirmPassword'] != '') {
    if ($_POST['confirmPassword'] != $_POST['password']) {
        $errors .= 'Passwords do not match. Please make sure they match and try again.<br />';
    }
} else {
    $errors .= 'Please enter a new password.<br />';
}

// Verify with hCaptcha
if (isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])) {
    // Get verify response
    $data = array(
        'secret' => "ES_e984ba4ab5324376ada817c539ffa7b5",
        'response' => $_POST['h-captcha-response']
    );
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $verifyResponse = curl_exec($verify);
    $responseData = json_decode($verifyResponse);
    if ($responseData->success) {
    } else {
        $errors .= 'Unable to verify, please complete CAPTCHA.<br />';
    }
} else {
    $errors .= 'Please complete CAPTCHA.<br />';
}

$displayName = strip_tags($_POST['displayName']);

// Output errors if needed, otherwise add to database and load login page
if ($errors != '') {
    echo '        <div class="error">' . $errors . 'Please Try Again</div><hr />' . PHP_EOL;
    require_once ('../src/inputNewUser.php');
} else {
    $login_ip = $_SERVER['REMOTE_ADDR'];
    $sql = "INSERT INTO asyncusers (email, password, is_admin, displayName, registered_ip, registered_date) VALUES (:email, :password, 'n', :displayName, :ip, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', password_hash($_POST['password'], PASSWORD_BCRYPT));
    $stmt->bindValue(':displayName', $displayName, PDO::PARAM_STR);
    $stmt->bindValue(':ip', $login_ip, PDO::PARAM_STR);
    $stmt->execute();
    echo '        <div class="error">User successfully registered. Please log in.</div><hr />' . PHP_EOL;
    require_once ('../src/loginForm.php');
}