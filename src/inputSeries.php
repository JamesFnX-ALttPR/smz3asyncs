<?php
echo '        <form method="post" autocomplete="off" action="' . $domain . '/createseries">' . PHP_EOL;
echo '            <table class="createAsync">' . PHP_EOL;
echo '                <caption>Create New Series</caption>' . PHP_EOL;
echo '                <tbody>' . PHP_EOL;
echo '                    <tr><th><label for="name" class="rightAlign">Series Name:</label></th><td><input size="75" type="text" id="name" name="name" required /></td></tr>' . PHP_EOL;
echo '                    <tr><th><label for="description" class="rightAlign">Series Description:</label></th><td><textarea id="description" name="description" rows="4" cols="80" required>REQUIRED - Enter a description of your series here</textarea></td></tr>' . PHP_EOL;
echo '                    <tr><td colspan="2" class="submitButton"><input type="Submit" class="submitButton" value="Create Async" /></td></tr>' . PHP_EOL;
echo '                </tbody>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
echo '            <input type="hidden" id="createdBy" name="createdBy" value="' . $_SESSION['userid'] . '" />' . PHP_EOL;
echo '        </form>' . PHP_EOL;