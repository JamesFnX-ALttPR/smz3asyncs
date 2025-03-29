<?php

require_once ('../includes/bootstrap.php');

$pageTitle = 'Home';
require_once ('../includes/header.php');
echo '        <div class="asyncMiddle">If you experience any issues with searching asyncs, posting times, creating an account, logging in, or anything else, please reach out to me on the <a target="_blank" href="' . $domain . '/discord">Discord</a>.</div><br />' . PHP_EOL;
require_once ('../src/inputSearch.php');
echo '        <br />' . PHP_EOL;
require_once ('../src/displayLastRaces.php');
require_once ('../includes/footer.php');

?>
