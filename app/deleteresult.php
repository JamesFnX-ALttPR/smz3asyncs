<?php

require_once ('../includes/bootstrap.php');
if (isset($_SESSION['userid'])) {
    $pageTitle = 'Delete Results';
    require_once ('../includes/header.php');
    require_once ('../src/processDeleteResults.php');
    require_once ('../includes/footer.php');
} else {
    $pageTitle = 'Please Log In';
    require_once ('../includes/header.php');
    echo '        <div class="error">You must log in to see this page.</div><br />' . PHP_EOL;
    require_once ('../src/loginForm.php');
    require_once ('../includes/footer.php');
}