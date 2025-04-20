<?php
include('../src/selectJS.php');
?>
        <form method="post" autocomplete="off" action="<?= $domain ?>/editasync/<?= $race_id ?>">
            <table class="createAsync">
                <caption>Edit Async for <?= $race_slug ?><br /><?php echo hashToTable($raceHash); ?></caption>
                <tbody>
                    <tr><th><label for="seed" title="Include the entire URL for the seed's permalink, starting with https://">Link to Seed</label> </th><th><label for="mode" title="If there's an existing preset, it will autcomplete.">Mode</label></th></tr>
                    <tr><td class="centerAlign"><input type="text" size="46" id="seed" name="seed" value="<?= $race_seed ?>" required /></td><td class="centerAlign"><input size="46" list="modes" id="mode" name="mode" value="<?= $race_mode ?>" required />
                        <datalist id="modes">
<?php $stmt = $pdo->query('SELECT DISTINCT raceMode FROM races ORDER BY raceMode');
foreach($stmt as $row) {
    echo '                            <option value="' . $row['raceMode'] . '"</option>' . PHP_EOL;
}
?>
                        </datalist>
                    <tr><th colspan="2"><label for="hash1" title="The hash as it appears on the file select screen.">Hash</label></th></tr>
<?php
echo '                    </tr><td colspan="2"><select id="hash1" name="hash1" required>' . PHP_EOL;
createHashDropdown($hash1);
echo '                    </select> <select id="hash2" name="hash2" required>' . PHP_EOL;
createHashDropdown($hash2);
echo '                    </select><br /> <select id="hash3" name="hash3" required>' . PHP_EOL;
createHashDropdown($hash3);
echo '                    </select> <select id="hash4" name="hash4" required>' . PHP_EOL;
createHashDropdown($hash4);
?>
                    </select></td></tr>
                    <tr><th class="rightAlign"><label for="description">Description:</label> </th><td class="centerAlign"><input type="text" size="46" id="description" name="description" value="<?= $db_description ?>" /></td></tr>
                    <tr><th class="rightAlign"><label for="spoiler" title="Check here if this is a spoiler mode. A field will appear to add a link to the spoiler log.">Spoiler?</label> </th><td><input type="checkbox" id="spoiler" name="spoiler" value="y" <?php if ($race_spoiler_flag == 'y') { echo 'checked '; } ?>onclick="if (this.checked) { document.getElementsByClassName('spoiler')[0].style.display = 'table-row'; } else { document.getElementsByClassName('spoiler')[0].style.display = 'none'; }" /></td></tr>
                    <tr class="spoiler"<?php if ($race_spoiler_flag == 'y') { echo ' style="display: table-row;"'; } ?>><th class="rightAlign"><label for="spoilerLog" title="Include the entire URL for the seed\'s spoiler log, starting with https://">Link to Spoiler Log: </th><td class="centerAlign"><input type="text" size="46" id="spoilerLog" name="spoilerLog" value=<?php if (isset($race_spoiler_link)) { echo '"' . $race_spoiler_link . '"'; } else { echo '""'; } ?>" /></td></tr>
                    <tr><th class="rightAlign"><label for="team" title="Check here if this is a co-op/team seed meant for two players.">Co-Op/Team?</label> </th><td><input type="checkbox" id="team" name="team" value="y" <?php if ($race_team_flag == 'y') { echo 'checked '; } ?>/></td></tr>
                    <tr><th class="rightAlign"><label for="loginRequired" title="Check here if a user must login to submit a result.">Login Required?</label> </th><td><input type="checkbox" id="loginRequired" name="loginRequired" value="y" <?php if ($race_login_flag == 'y') { echo 'checked '; } ?>/></td></tr>
                    <tr><th class="rightAlign"><label for="vodRequired" title="Check here if a VOD link will be required to submit a result.">VOD Required?</label> </th><td><input type="checkbox" id="vodRequired" name="vodRequired" value="y" <?php if ($race_vod_flag == 'y') { echo 'checked '; } ?>/></td></tr>
                    <tr><th class="rightAlign"><label for="editDisallowed" title="Check here if you would like users to not be able to edit their result submissions after entering them.">Disallow Edits?</label> </th><td><input type="checkbox" id="editDisallowed" name="editDisallowed" value="y" <?php if ($race_edit_flag == 'n') { echo 'checked '; } ?>/></td></tr>
                    <tr><th class="rightAlign"><label for="locked" title="Check here if you would like to lock this async and not allow further results to be submitted until you unlock it.">Lock Async?</label> </th><td><input type="checkbox" id="locked" name="locked" value="y" <?php if ($race_locked_flag == 'y') { echo 'checked '; } ?>/></td></tr>
<?php if ($race_tournament_flag == 'y') {
    echo '                    <tr><td colspan="2" class="centerAlign">This is a tournament restricted async. Locking the async will make results available for everyone.<br />You will <span style="font-weight: bold;">NOT</span> be able to return this async to tournament mode.</td></tr><input type="hidden" id="tournament" name="tournament" value="y" />' . PHP_EOL;
}
?>
                    <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Save Changes" /></td><td class="submitButton"><a href="<?= $domain ?>/yourasyncs" class="fakeButton">Go Back</a></td></tr>
                </tbody>
            </table>
        </form>
