<?php

require_once ('../includes/bootstrap.php');

$pageTitle = 'Async Search';
require_once ('../includes/header.php');
if(isset($_POST['searchBox'])) {
    $submitted = 1;
} else {
    $submitted = 0;
}
if($submitted == 0) {
    require_once ('../src/inputSearch.php');
} else {
    require_once ('../src/processSearch.php');
}
require_once ('../includes/footer.php');