<?php

$request = $_SERVER['REQUEST_URI'];
$escaped_request = htmlspecialchars( $request, ENT_QUOTES, 'UTF-8' );

echo '        <form method="post" autocomplete="off" action="' . $domain . '/login">' . PHP_EOL;
echo '            <table class="register">' . PHP_EOL;
echo '                <thead>' . PHP_EOL;
echo '                    <caption>Login to Account</caption>' . PHP_EOL;
echo '                </thead>' . PHP_EOL;
echo '                <tbody>' . PHP_EOL;
echo '                    <tr><td><label for="email">Email: </td><td><input type="text" id="email" name="email" placeholder="you@domain.com" required /></td></tr>' . PHP_EOL;
echo '                    <tr><td><label for="password">Password: </td><td><input type="password" id="password" name="password" required /></td></tr>' . PHP_EOL;
echo '                    <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Login" /></td><td class="submitAsync"><a href="' . $domain . '/register' . '" class="fakeButton">I need to register</a></td>' . PHP_EOL;
echo '                    <tr><td class="submitButton" colspan="2"><a href="' . $domain . '/resetpassword" class="fakeButton">I forgot my password</a></td>' . PHP_EOL;
echo '                </tbody>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
echo '            <input type="hidden" id="redirectTo" name="redirectTo" value="';
if ($escaped_request == '/login' || $escaped_request == '/logout' || $escaped_request == '/reset' || $escaped_request == '/register') {
    $escaped_request = '/account';
}
echo $escaped_request . '" />' . PHP_EOL;
echo '        </form>' . PHP_EOL;
