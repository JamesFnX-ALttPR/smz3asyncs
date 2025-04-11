<?php

// Determine rights of logged in user
$user_id = $_SESSION['userid'];
require_once ('../includes/user_info.php');
?>
        <table class="accountHome">
            <tbody>
<?php
if ($admin_flag == 'y') { // User Admin link for admins only
    echo '                <tr><td colspan="2" class="accountHomeLinks"><a href="' . $domain . '/useradmin">User Administration</a></td></tr>' . PHP_EOL;
    echo '                <tr><td colspan="2" class="accountHomeDesc">View users, grant extra permissions on the site, or ban abusive users.</td></tr>' . PHP_EOL;
}
?>
                <tr><td class="accountHomeLinks"><a href="<?= $domain ?>/settings">Account Settings</a></td><td class="accountHomeLinks"><a href="<?= $domain ?>/yourasyncs">Custom Asyncs</td></tr>
                <tr><td class="accountHomeDesc">View or change your display name, email, and pasword.</td><td class="accountHomeDesc">Create, view, or edit the custom asyncs you've created.</td></tr>
                <tr><td class="accountHomeLinks"><a href="<?= $domain ?>/yourresults">Submitted Results</a></td><td class="accountHomeLinks"><?php if ($admin_flag == 'y' || $series_flag == 'y') { echo '<a href="' . $domain .'/yourseries">Your Series</a>'; } ?></td></tr>
                <tr><td class="accountHomeDesc">View or edit the results you've submitted to asyncs.</td><td class="accountHomeDesc"><?php if ($admin_flag == 'y' || $series_flag == 'y') { echo 'Create, view, or edit the custom series you\'ve created.'; } ?></td></tr>
<?php
if ($admin_flag == 'y' || $organizer_flag == 'y') {
    echo '                <tr><td class="accountHomeLinks"><a href="' . $domain . '/toportal">Tournament Organizer Portal</a></td><td></td></tr>' . PHP_EOL;
    echo '                <tr><td class="accountHomeDesc">Create, view, and edit tournaments.</td><td></td></tr>' . PHP_EOL;
}
?>
            </tbody>
        </table>
