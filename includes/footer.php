<?php

echo '        <hr />' . PHP_EOL;
if (isset($_SESSION['userid'])) {
    echo '        <div><span class="footerleft">Logged in as ' . $_SESSION['displayName'] . '</span>';
} else {
    echo '        <div>';
}
echo '<span class="footerright">' . date("Y") . ' - Created by <a target="_blank" href="https://twitch.tv/jamesfnx">JamesFnX</a></span></div>' . PHP_EOL;
echo '    </body>' . PHP_EOL;
echo '</html>' . PHP_EOL;