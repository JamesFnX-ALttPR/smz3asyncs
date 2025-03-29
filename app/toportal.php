<?php
require_once ('../includes/bootstrap.php');
$pageTitle = 'Tournament Organizer Portal';
require_once ('../includes/header.php');

if (isset($_SESSION['userid'])) {
    if (is_post_request()) {
        require_once ('../src/processCreateTournament.php');
    } else {
        require_once ('../src/displayTOPortal.php');
        require_once ('../src/inputTournament.php');
    }
} else {
    echo '        <div class="error">You must log in to see this page.</div><br />' . PHP_EOL;
    require_once ('../src/loginForm.php');
}
require_once ('../includes/footer.php');