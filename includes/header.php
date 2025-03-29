<?php

// Generate the HTML for the start of a page
echo '<!DOCTYPE html>' . PHP_EOL;
echo '<html lang="en-US">' . PHP_EOL;
echo '    <head>' . PHP_EOL;
echo '        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" />' . PHP_EOL;
echo '        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />' . PHP_EOL;
echo '        <link rel="stylesheet" href="' . $domain . '/includes/styles.css" />' . PHP_EOL;
echo '        <title>SMZ3 Asyncs - ' . $pageTitle . '</title>' . PHP_EOL; //Variable $pageTitle specified on each page
echo '        <script src="https://js.hcaptcha.com/1/api.js" async defer></script>' . PHP_EOL;
echo '        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>' . PHP_EOL;
echo '        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>' . PHP_EOL;
echo '        <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>' . PHP_EOL;
echo '        <script src="' . $domain . '/sorttable.js"></script>' . PHP_EOL;
echo '        <meta charset="UTF-8">' . PHP_EOL;
echo '    </head>' . PHP_EOL;
echo '    <body>' . PHP_EOL;
echo '        <script>' . PHP_EOL;
echo '            $(document).ready(function() {' . PHP_EOL;
echo '                $(\'.js-example-basic-single\').select2();' . PHP_EOL;
echo '            });' . PHP_EOL;
echo '            $(\'.js-example-basic-single\').next(\'.select2\').find(\'select2-selection\').on(\'keypress\', function (e) {' . PHP_EOL;
echo '                $(e.target).closest(\'div\').find(\'.js-example-basic-single\').select2(\'open\');' . PHP_EOL;
echo '                setTimeout(function () {' . PHP_EOL;
echo '                    $(\'.select2-search__field\').focus();' . PHP_EOL;
echo '                    $(\'.select2-search__field\').val(e.key);' . PHP_EOL;
echo '                }, 100);' . PHP_EOL;
echo '            });' . PHP_EOL;
echo '        </script>' . PHP_EOL;
echo '        <div class="topline"><a class="toplinks" href="' . $domain . '/search">Search</a>';
$stmt = $pdo->prepare("SELECT id FROM series");
$stmt->execute();
$chk = $stmt->fetchColumn();
if ($chk) {
    echo '<a class="toplinks" href="' . $domain . '/series">Series</a>';
}
echo '<a class="toplinks" href="' . $domain . '/discord" target="_blank">Discord</a>';
if (isset($_SESSION['userid'])) {
    echo '<a class="toplinks" href="' . $domain . '/account">Account</a><a class="toplinks" href="' . $domain . '/logout">Logout</a>';
} else {
    echo '<a class="toplinks" href="' . $domain . '/login">Login</a>';
}
echo '</div>' . PHP_EOL;
echo '        <hr />' . PHP_EOL;
echo '        <h1>SMZ3 Asyncs</h1>' . PHP_EOL;
