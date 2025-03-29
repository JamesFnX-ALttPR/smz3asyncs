<?php

// Check if logged in user is an admin
$stmt = $pdo->prepare("SELECT is_admin FROM asyncusers WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$isAdmin = $stmt->fetchColumn();
if ($isAdmin == 'n') {
    echo '        <div class="error">You are not authorized to see this page. Please log in and try again.' . PHP_EOL;
    require_once ('../src/loginForm.php');
    require_once ('../includes/footer.php');
    die();
}
$rowCount = 0;
echo '        <form method="post" action="" id="userAdmin">' . PHP_EOL;
echo '        <table class="userAdmin">' . PHP_EOL;
echo '            <caption>User Administration</caption>' . PHP_EOL;
echo '            <thead>' . PHP_EOL;
echo '                <tr><th>Display Name</th><th>Email Address</th><th>Registered</th><th>Last Logged In</th><th>Series Creator</th><th>Tourney Organizer</th><th>Banned</th></tr>' . PHP_EOL;
echo '            </thead>' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
$stmt = $pdo->prepare("SELECT id, displayName, email, registered_date, last_login_date, is_seriesMaker, is_organizer, is_banned FROM asyncusers");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $rowCount++;
    $id = $row['id'];
    $displayName = $row['displayName'];
    $email = $row['email'];
    $registeredDate = $row['registered_date'];
    $lastLoginDate = $row['last_login_date'];
    $isCreator = $row['is_seriesMaker'];
    $isOrganizer = $row['is_organizer'];
    $isBanned = $row['is_banned'];
    if($rowCount % 2 == 0) {
        $startOfRow = '                <tr class="even">';
    } else {
        $startOfRow = '                <tr class="odd">';
    }
    echo $startOfRow . '<td>' . $displayName . '</td><td>' . $email . '</td><td>';
    if ($registeredDate != null) {
        echo gmdate('n-j-Y', strtotime($registeredDate));
    } else {
        echo 'N/A';
    }
    echo '</td><td>';
    if ($lastLoginDate != null) {
        echo gmdate('n-j-Y', strtotime($lastLoginDate));
    } else {
        echo 'N/A';
    }
    echo '</td><td><select id="' . $id . '_creator" name="' . $id . '_creator"><option value="y"'; if ($isCreator == 'y') { echo ' selected'; } echo '>Yes</option><option value="n"'; if ($isCreator == 'n') { echo ' selected'; } echo '>No</option></td>';
    echo '<td><select id="' . $id . '_organizer" name="' . $id . '_organizer"><option value="y"'; if ($isOrganizer == 'y') { echo ' selected'; } echo '>Yes</option><option value="n"'; if ($isOrganizer == 'n') { echo ' selected'; } echo '>No</option></td>';
    echo '<td><select id="' . $id . '_banned" name="' . $id . '_banned"><option value="y"'; if ($isBanned == 'y') { echo ' selected'; } echo '>Yes</option><option value="n"'; if ($isBanned == 'n') { echo ' selected'; } echo '>No</option></td>';
    echo '</tr>' . PHP_EOL;
    unset ($id); unset ($displayName); unset ($email); unset ($registeredDate); unset ($lastLoginDate); unset ($isCreator); unset ($isOrganizer); unset ($isBanned);
}
echo '                <tr><td colspan="7" class="submitButton"><input type="Submit" class="submitButton" value="Update Users" /></td></tr>' . PHP_EOL;
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
echo '        </form>' . PHP_EOL;
