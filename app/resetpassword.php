<?php

require_once ('../includes/bootstrap.php');
$pageTitle = "Reset Password";
require_once ('../includes/header.php');
if (is_post_request()) {
    require_once ('../src/processPasswordReset.php');
} elseif (is_get_request() && isset($_GET['request'])) {
    require_once ('../src/performPasswordReset.php');
} else {
    require_once ('../src/inputPasswordReset.php');
}