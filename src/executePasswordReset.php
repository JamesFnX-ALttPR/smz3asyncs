<?php

$errors = '';

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
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

// Output errors if needed, otherwise add to database and load login page
if ($errors != '') {
    echo '        <div class="error">' . $errors . 'Please use the link emailed and try again</div><hr />' . PHP_EOL;
} else {
    $sql = "UPDATE asyncusers SET password = :password, resetCallback = null WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', password_hash($_POST['password'], PASSWORD_BCRYPT));
    $stmt->execute();
    echo '        <div class="error">Your password has been updated. Please log in.</div><hr />' . PHP_EOL;
    require_once ('../src/loginForm.php');
}