<?php

require_once ('../includes/bootstrap.php');
$pageTitle = 'Account Settings';

if (isset($_SESSION['userid'])) {
    if (is_post_request()) {
        require_once ('../src/processAccountInfo.php');
    } else {
        require_once ('../includes/header.php');
        require_once ('../src/displayAccountInfo.php');
    }
} else {
    echo '        <div class="error">You must log in to see this page.</div><br />' . PHP_EOL;
    require_once ('../src/loginForm.php');
}
require_once ('../includes/footer.php');