<?php
require_once ('../includes/bootstrap.php');
$pageTitle = 'Create New Async';
require_once ('../includes/header.php');

if (isset($_POST['approved'])) {
    require_once ('../src/addAsyncToDatabase.php');
}
else {
    if(isset($_POST['seed'])) {
        require_once ('../src/processAsync.php');
    } else {
        if (! isset($_SESSION['userid'])) {
            echo '        <div class="error">You must be logged in to create asyncs. Please log in.</div>' . PHP_EOL;
            require_once ('../src/loginForm.php');
        } else {
            require_once ('../src/inputAsync.php');
        }
    }
}
require_once ('../includes/footer.php');