<?php

$stmt = $pdo->prepare("SELECT email, display_name, default_search_range FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
$currentEmail = $row['email'];
$currentdisplay_name = $row['display_name'];
$currentOffset = $row['default_search_range'];

echo '        <form method="post" autocomplete="off" action="' . $domain . '/settings">' . PHP_EOL;
echo '            <table class="register">' . PHP_EOL;
echo '                <caption>View/Edit Account Info</caption>' . PHP_EOL;
echo '                <tbody>' . PHP_EOL;
echo '                    <tr><th colspan="2">Change Email, Display Name, and Default Search Range</th></tr>' . PHP_EOL;
echo '                    <tr><td><label for="email">Email: </label></td><td><input type="text" id="email" name="email" value ="' . $currentEmail . '" required /></td></tr>' . PHP_EOL;
echo '                    <tr><td><label for="display_name">Display Name: </label></td><td><input type="text" id="display_name" name="display_name" value="' . $currentdisplay_name . '" required /></td></tr>' . PHP_EOL;
echo '                    <tr><td><label for="default_search_range" title="Number of days you\'d like to search by default. Input 0 if you want to search the entire database by default.">Default Search Range:</label></td><td><input type="number" id="default_search_range" name="default_search_range" min="0" value="' . $currentOffset . '" required /><td></tr>' . PHP_EOL;
echo '                    <tr><th colspan="2">Change Password</th></tr>' . PHP_EOL;
echo '                    <tr><td><label for="password1">New Password: </label></td><td><input type="password" id="password1" name="password1" /></td></tr>' . PHP_EOL;
echo '                    <tr><td><label for="password2">Confirm New Password: </label></td><td><input type="password" id="password2" name="password2" /></td></tr>' . PHP_EOL;
echo '                    <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Update Info" /></td><td class="submitButton"><a href="' . $domain . '/account' . '" class="fakeButton">Go Back</a></td>' . PHP_EOL;
echo '                </tbody>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
echo '        </form>' . PHP_EOL;