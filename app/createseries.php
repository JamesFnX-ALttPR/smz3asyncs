<?php
require_once ('../includes/bootstrap.php');
$pageTitle = 'Create New Series';
require_once ('../includes/header.php');

if(isset($_POST['name'])) {
    require_once ('../src/processSeries.php');
} else {
    if (! isset($_SESSION['userid'])) {
        echo '        <div class="error">You must be logged in to create asyncs. Please log in.</div>' . PHP_EOL;
        require_once ('../src/loginForm.php');
    } else {
        require_once ('../src/inputSeries.php');
    }
}
require_once ('../includes/footer.php');