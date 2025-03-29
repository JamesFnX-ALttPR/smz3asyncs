<?php

//Determine if logged in account is an admin
$stmt = $pdo->prepare("SELECT is_admin, is_seriesMaker, is_organizer FROM asyncusers WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['userid'], PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch();
$isAdmin = $row['is_admin'];
$isSeriesMaker = $row['is_seriesMaker'];
$isOrganizer = $row['is_organizer'];

echo '        <table class="accountHome">' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
if ($isAdmin == 'y') {
    echo '                <tr><td colspan="2" class="accountHomeLinks"><a href="' . $domain . '/useradmin">User Administration</a></td></tr>' . PHP_EOL;
    echo '                <tr><td colspan="2" class="accountHomeDesc">View users, grant extra permissions on the site, or ban abusive users.</td></tr>' . PHP_EOL;
}
echo '                <tr><td class="accountHomeLinks"><a href="' . $domain . '/settings">Account Settings</a></td><td class="accountHomeLinks"><a href="' . $domain . '/yourasyncs">Custom Asyncs</td></tr>' . PHP_EOL;
echo '                <tr><td class="accountHomeDesc">View or change your display name, email, and pasword.</td><td class="accountHomeDesc">Create, view, or edit the custom asyncs you\'ve created.</td></tr>'. PHP_EOL;
echo '                <tr><td class="accountHomeLinks"><a href="' . $domain . '/yourresults">Submitted Results</a></td><td class="accountHomeLinks">';
if ($isAdmin == 'y' || $isSeriesMaker == 'y') {
    echo '<a href="' . $domain .'/yourseries">Your Series</a>';
}
echo '</td></tr>' . PHP_EOL;
echo '                <tr><td class="accountHomeDesc">View or edit the results you\'ve submitted to asyncs.</td><td class="accountHomeDesc">';
if ($isAdmin == 'y' || $isSeriesMaker == 'y') {
    echo 'Create, view, or edit the custom series you\'ve created.';
}
echo '</td></tr>' . PHP_EOL;
if ($isAdmin == 'y' || $isOrganizer == 'y') {
    echo '                <tr><td class="accountHomeLinks"><a href="' . $domain . '/toportal">Tournament Organizer Portal</a></td><td></td></tr>' . PHP_EOL;
    echo '                <tr><td class="accountHomeDesc">Create, view, and edit tournaments.</td><td></td></tr>' . PHP_EOL;
}
echo '            </tbody>' . PHP_EOL;
echo '        </table>' . PHP_EOL;