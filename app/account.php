<?php

require_once ('../includes/bootstrap.php');
$pageTitle = 'Account Home';

require_once ('../includes/header.php');
if (isset($_SESSION['userid'])) {
    require_once ('../src/accountHome.php');
} else {
    echo '        <div class="error">You must log in to see this page.</div><br />' . PHP_EOL;
    require_once ('../src/loginForm.php');
}
require_once ('../includes/footer.php');