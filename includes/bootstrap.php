<?php
require_once ('../includes/functions.php');
require_once ('../config/settings.php');
session_set_cookie_params([
    'lifetime' => 28800,
    'secure' => true,
    'httponly' => true
]);
session_start();
$domain = getRequestURL();

// Create DB connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $options);

$stmt = $pdo->prepare("SELECT last_login_ip FROM asyncusers WHERE is_banned = 'y'");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $bannedIP = $row['last_login_ip'];
    if ($bannedIP == $_SERVER['REMOTE_ADDR']) {
        echo '<!DOCTYPE html>' . PHP_EOL;
        echo '<html lang="en-US">' . PHP_EOL;
        echo '    <head>' . PHP_EOL;
        echo '        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" />' . PHP_EOL;
        echo '        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />' . PHP_EOL;
        echo '        <link rel="stylesheet" href="' . $domain . '/includes/styles.css" />' . PHP_EOL;
        echo '        <title>SMZ3 Asyncs - Unauthorized Access</title>' . PHP_EOL;
        echo '        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>' . PHP_EOL;
        echo '        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>' . PHP_EOL;
        echo '        <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>' . PHP_EOL;
        echo '        <script src="' . $domain . '/sorttable.js"></script>' . PHP_EOL;
        echo '        <meta charset="UTF-8">' . PHP_EOL;
        echo '    </head>' . PHP_EOL;
        echo '    <body>' . PHP_EOL;
        echo '        <div class="error">You are not authorized to view this content. Please reach out to James on the <a target="_blank" href="' . $domain . '/discord">Discord</a> if you feel you are receiving this message in error.</div>' . PHP_EOL;
        require_once ('../includes/footer.php');
        die();
    }
}

if ($maintenance == 1) {
    echo '<!DOCTYPE html>' . PHP_EOL;
    echo '<html lang="en-US">' . PHP_EOL;
    echo '    <head>' . PHP_EOL;
    echo '        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" />' . PHP_EOL;
    echo '        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />' . PHP_EOL;
    echo '        <link rel="stylesheet" href="' . $domain . '/includes/styles.css" />' . PHP_EOL;
    echo '        <title>SMZ3 Asyncs - Site Undergoing Maintenance</title>' . PHP_EOL;
    echo '        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>' . PHP_EOL;
    echo '        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>' . PHP_EOL;
    echo '        <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>' . PHP_EOL;
    echo '        <script src="' . $domain . '/sorttable.js"></script>' . PHP_EOL;
    echo '        <meta charset="UTF-8">' . PHP_EOL;
    echo '    </head>' . PHP_EOL;
    echo '    <body>' . PHP_EOL;
    echo '        <div class="error">The site is currently undergoing maintenance. For updates, please check our <a target="_blank" href="' . $domain . '/discord">Discord</a>.</div>' . PHP_EOL;
    require_once ('../includes/footer.php');
    die();
}
