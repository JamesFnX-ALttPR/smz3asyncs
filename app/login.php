<?php

require_once ('../includes/bootstrap.php');
$pageTitle = 'Login';
if(is_post_request()) {
    require_once ('../src/processLogin.php');
    require_once ('../includes/header.php');
    if (isset($errors)) {
        echo '        <div class="error">We were unable to log you in.<br />Please check your email and password and try again.</div>' . PHP_EOL;
        require_once ('../src/loginForm.php');
    } else {
        echo '        <div class="asyncMiddle">Logged in as ' . $_POST['email'] . '</div>';
    }
} else {
    require_once ('../includes/header.php');
    require_once ('../src/loginForm.php');
}
require_once ('../includes/footer.php');