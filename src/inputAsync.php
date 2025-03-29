<?php
// include('../src/selectJS.php');
?>
        <form method="post" autocomplete="off" action="<?= $domain ?>/createasync">
            <table class="createAsync">
                <caption>Create New Async</caption>
                <thead>
                    <tr><th><label for="seed" title="Include the entire URL for the seed's permalink, starting with https://">Link to Seed</label></th><th><label for="mode" title="If there's an existing preset, it will autcomplete.">Mode</label></th></tr>
                </thead>
                <tbody>
                    <tr><td class="centerAlign"><input type="text" size="46" id="seed" name="seed" required /></td><td class="centerAlign"><input size="46" list="modes" id="mode" name="mode" required />
                        <datalist id="modes">
<?php
$stmt = $pdo->query('SELECT DISTINCT raceMode FROM races ORDER BY raceMode');
foreach($stmt as $row) {
    echo '                            <option value="' . $row['raceMode'] . '"></option>' . PHP_EOL;
}
?>
                        </datalist>
                    <tr><th colspan="2"><label for="hash1" title="The hash as it appears on the file select screen.">Hash</label></th></tr>
                    <tr><td colspan="2" style="text-align: center"><select id="hash1" name="hash1" class="js-example-basic-single" required>
<?php
createHashDropdown();
?>
                    </select> <select id="hash2" name="hash2" class="js-example-basic-single" required>
<?php
createHashDropdown();
?>
                    </select><br /> <select id="hash3" name="hash3" class="js-example-basic-single" required>
<?php
createHashDropdown();
?>
                    </select> <select id="hash4" name="hash4" class="js-example-basic-single" required>
<?php
createHashDropdown();
?>
                    </select></td></tr>
                    <tr><th class="rightAlign"><label for="description">Description:</label></th><td class="centerAlign"><input type="text" size="46" id="description" name="description" /></td></tr>
                    <tr><th class="rightAlign"><label for="spoiler" title="Check here if this is a spoiler mode. A field will appear to add a link to the spoiler log.">Spoiler?</label></th><td><input type="checkbox" id="spoiler" name="spoiler" value="y" onclick="if (this.checked) { document.getElementsByClassName('spoiler')[0].style.display = 'table-row'; } else { document.getElementsByClassName('spoiler')[0].style.display = 'none'; }" /></td></tr>
                    <tr class="spoiler"><th class="rightAlign"><label for="spoilerLog" title="Include the entire URL for the seed's spoiler log, starting with https://">Link to Spoiler Log:</label> </th><td class="centerAlign"><input type="text" size="46" id="spoilerLog" name="spoilerLog" /></td></tr>
                    <tr><th class="rightAlign"><label for="team" title="Check here if this is a co-op/team seed meant for two players.">Co-Op/Team?</label></th><td><input type="checkbox" id="team" name="team" value="y" /></td></tr>
                    <tr><th class="rightAlign"><label for="loginRequired" title="Check here if a user must login to submit a result.">Login Required?</label></th><td><input type="checkbox" id="loginRequired" name="loginRequired" value="y" /></td></tr>
                    <tr><th class="rightAlign"><label for="vodRequired" title="Check here if a VOD link will be required to submit a result.">VOD Required?</label></th><td><input type="checkbox" id="vodRequired" name="vodRequired" value="y" /></td></tr>
                    <tr><th class="rightAlign"><label for="editDisallowed" title="Check here if you would like users to not be able to edit their result submissions after entering them.">Disallow Edits?</label></th><td><input type="checkbox" id="editDisallowed" name="editDisallowed" value="y" /></td></tr>
                    <tr><th class="rightAlign"><label for="tournament_seed" title="Check here if this seed is for a tournament. Login will be required to view the async details. Participants will not be viewable from the submission page. Results will not be viewable unless you've submitted a time.">Tournament Seed?</label></th><td><input type="checkbox" id="tournament_seed" name="tournament_seed" value="y" /></td></tr>
                    <tr><td colspan="2" class="submitButton"><input type="Submit" class="submitButton" value="Create Async" /></td></tr>
                </tbody>
            </table>
        </form>
