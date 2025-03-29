<?php
include('../src/selectJS.php');
echo '        <form method="post" autocomplete="off" action="' . $domain . '/editasync/' . $raceID . '">' . PHP_EOL;
echo '            <table class="createAsync">' . PHP_EOL;
echo '                <thead>' . PHP_EOL;
echo '                    <caption>Edit Async for '. $raceSlug . '<br />' . hashToTable($raceHash) . '</caption>' . PHP_EOL;
echo '                </thead>' . PHP_EOL;
echo '                <tbody>' . PHP_EOL;
echo '                    <tr><th><label for="seed" title="Include the entire URL for the seed\'s permalink, starting with https://">Link to Seed</label> </th><th><label for="mode" title="If there\'s an existing preset, it will autcomplete.">Mode</label></th></tr>' . PHP_EOL;
echo '                    <tr><td class="centerAlign"><input type="text" size="46" id="seed" name="seed" value="' . $raceSeed . '" required /></td><td class="centerAlign"><input size="46" list="modes" id="mode" name="mode" value="' . $raceMode . '" required />' . PHP_EOL;
echo '                        <datalist id="modes">' . PHP_EOL;
$stmt = $pdo->query('SELECT DISTINCT raceMode FROM races ORDER BY raceMode');
foreach($stmt as $row) {
    echo '                            <option value="' . $row['raceMode'] . '"</option>' . PHP_EOL;
}
echo '                        </datalist>' . PHP_EOL;
echo '                    <tr><th colspan="2"><label for="hash1" title="The hash as it appears on the file select screen.">Hash</label></th></tr>' . PHP_EOL;
echo '                    </tr><td colspan="2"><select id="hash1" name="hash1" required>' . PHP_EOL;
createHashDropdown($hash1);
echo '                    </select> <select id="hash2" name="hash2" required>' . PHP_EOL;
createHashDropdown($hash2);
echo '                    </select><br /> <select id="hash3" name="hash3" required>' . PHP_EOL;
createHashDropdown($hash3);
echo '                    </select> <select id="hash4" name="hash4" required>' . PHP_EOL;
createHashDropdown($hash4);
echo '                    </select></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="description">Description:</label> </th><td class="centerAlign"><input type="text" size="46" id="description" name="description" value="'. $raceDescription . '" /></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="spoiler" title="Check here if this is a spoiler mode. A field will appear to add a link to the spoiler log.">Spoiler?</label> </th><td><input type="checkbox" id="spoiler" name="spoiler" value="y" '; if ($raceIsSpoiler == 'y') { echo 'checked '; } echo 'onclick="if (this.checked) { document.getElementsByClassName(\'spoiler\')[0].style.display = \'table-row\'; } else { document.getElementsByClassName(\'spoiler\')[0].style.display = \'none\'; }" /></td></tr>' . PHP_EOL;
echo '                    <tr class="spoiler"'; if ($raceIsSpoiler == 'y') { echo ' style="display: table-row;"'; } echo '><th class="rightAlign"><label for="spoilerLog" title="Include the entire URL for the seed\'s spoiler log, starting with https://">Link to Spoiler Log: </th><td class="centerAlign"><input type="text" size="46" id="spoilerLog" name="spoilerLog" value="' . $raceSpoilerLink .'" /></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="team" title="Check here if this is a co-op/team seed meant for two players.">Co-Op/Team?</label> </th><td><input type="checkbox" id="team" name="team" value="y" '; if ($raceIsTeam == 'y') { echo 'checked '; } echo '/></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="loginRequired" title="Check here if a user must login to submit a result.">Login Required?</label> </th><td><input type="checkbox" id="loginRequired" name="loginRequired" value="y" '; if ($loginRequired == 'y') { echo 'checked '; } echo '/></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="vodRequired" title="Check here if a VOD link will be required to submit a result.">VOD Required?</label> </th><td><input type="checkbox" id="vodRequired" name="vodRequired" value="y" '; if ($vodRequired == 'y') { echo 'checked '; } echo '/></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="editDisallowed" title="Check here if you would like users to not be able to edit their result submissions after entering them.">Disallow Edits?</label> </th><td><input type="checkbox" id="editDisallowed" name="editDisallowed" value="y" '; if ($allowResultEdits == 'n') { echo 'checked '; } echo '/></td></tr>' . PHP_EOL;
echo '                    <tr><th class="rightAlign"><label for="locked" title="Check here if you would like to lock this async and not allow further results to be submitted until you unlock it.">Lock Async?</label> </th><td><input type="checkbox" id="locked" name="locked" value="y" '; if ($locked == 'y') { echo 'checked '; } echo '/></td></tr>' . PHP_EOL;
echo '                    <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Save Changes" /></td><td class="submitButton"><a href="' . $domain . '/yourasyncs' . '" class="fakeButton">Go Back</a></td></tr>' . PHP_EOL;
echo '                </tbody>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
echo '        </form>' . PHP_EOL;