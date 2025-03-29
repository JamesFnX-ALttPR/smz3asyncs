<?php

require_once ('../includes/bootstrap.php');
$pageTitle = 'User Administration';
require_once ('../includes/header.php');
// Check to make sure you're an admin
if (! isset($_SESSION['userid'])) {
    echo '        <div class="error">You are not authorized to see this page. Please log in and try again.' . PHP_EOL;
    require_once ('../src/loginForm.php');
    require_once ('../includes/footer.php');
    die();
} else {
    if (is_post_request()) {
        require_once ('../src/processUserAdmin.php');
    } else {
        require_once ('../src/inputUserAdmin.php');
    }
    require_once ('../includes/footer.php');
}