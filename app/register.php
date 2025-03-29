<?php

require_once ('../includes/bootstrap.php');
$pageTitle = 'Register New User';
require_once ('../includes/header.php');
if(is_post_request()) {
    require_once ('../src/processNewUser.php');
} else {
    require_once ('../src/inputNewUser.php');
}
require_once ('../includes/footer.php');