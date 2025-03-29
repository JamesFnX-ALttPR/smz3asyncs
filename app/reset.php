<?php

require_once ('../includes/bootstrap.php');
$pageTitle = "Reset Password";
require_once ('../includes/header.php');
if (is_post_request()) {
    require_once ('../src/executePasswordReset.php');
} else {
    require_once ('../src/inputPasswordReset.php');
}