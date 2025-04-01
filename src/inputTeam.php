                <tbody>
                    <tr><td><label for="teamName">Team Name:</label> </td><td colspan="2"><input type="text" size="47" id="teamName" name="teamName" <?php if(isset($_POST['teamName'])) { echo 'value="' . $_POST['teamName'] . '" '; } ?>required /></td></tr>
                    <tr><td></td><td><label for="racer1Name">Player 1</label></td><td><label for="racer2Name">Player 2</label></td></tr>
                    <tr><td><label title="If you have races on Racetime, your name will autocomplete.">Racer Names: </label></td><td><input list="racers" id="racer1Name" name="racer1Name" required <?php if(isset($_POST['racer1Name'])) { echo 'value="' . $_POST['racer1Name'] . '" '; } elseif (isset($_SESSION['display_name'])) { echo ' value="' . $_SESSION['display_name'] . '"'; } ?>/></td><td><input list="racers" id="racer2Name" name="racer2Name" required <?php if(isset($_POST['racer2Name'])) { echo 'value="' . $_POST['racer2Name'] . '" '; } ?>/>
                        <datalist id="racers">
<?php
$stmt = $pdo->query('SELECT DISTINCT rtgg_name FROM racerinfo ORDER BY rtgg_name');
foreach($stmt as $row) {
    echo '                            <option value="' . $row['rtgg_name'] . '"></option>' . PHP_EOL;
}
?>
                        </datalist>
                    </td></tr>
                    <tr><td><label for="racer1Forfeit">Check to Forfeit: </label></td><td><input type="checkbox" id="racer1Forfeit" name="racer1Forfeit" value="y" onclick="if (this.checked) { document.getElementsByClassName('timeInput')[0].style.display = 'none'; document.getElementsByClassName('timeInput')[1].style.display = 'none'; document.getElementsByClassName('timeInput')[2].style.display = 'none'; } else { document.getElementsByClassName('timeInput')[0].style.display = 'table-row'; document.getElementsByClassName('timeInput')[1].style.display = 'table-row'; document.getElementsByClassName('timeInput')[2].style.display = 'table-row'; }"></td></tr>
                    <tr class="timeInput"><td><label for="racer1RTHours" title="Your actual time, *not* the time on the credits screen.">Real Times: </label></td><td><input type="number" id="racer1RTHours" name="racer1RTHours" min="0" max="24" placeholder="HH" <?php if(isset($_POST['racer1RTHours'])) { echo 'value="' . $_POST['racer1RTHours'] . '" '; } ?>/>:<input type="number" id="racer1RTMinutes" name="racer1RTMinutes" min="0" max="59" placeholder="MM" <?php if(isset($_POST['racer1RTMinutes'])) { echo 'value="' . $_POST['racer1RTMinutes'] . '" '; } ?>/>:<input type="number" id="racer1RTSeconds" name="racer1RTSeconds" min="0" max="59" placeholder="SS" <?php if(isset($_POST['racer1RTSeconds'])) { echo 'value="' . $_POST['racer1RTSeconds'] . '" '; } ?>/></td><td><input type="number" id="racer2RTHours" name="racer2RTHours" min="0" max="24" placeholder="HH" <?php if(isset($_POST['racer2RTHours'])) { echo 'value="' . $_POST['racer2RTHours'] . '" '; } ?>/>:<input type="number" id="racer2RTMinutes" name="racer2RTMinutes" min="0" max="59" placeholder="MM" <?php if(isset($_POST['racer2RTMinutes'])) { echo 'value="' . $_POST['racer2RTMinutes'] . '" '; } ?>/>:<input type="number" id="racer2RTSeconds" name="racer2RTSeconds" min="0" max="59" placeholder="SS" <?php if(isset($_POST['racer2RTSeconds'])) { echo 'value="' . $_POST['racer2RTSeconds'] . '" '; } ?>/></td></tr>
                    <tr class="timeInput"><td><label for="racer1CR" title="Your collected checks from the credits screen.">Your Collection Rate: </label></td><td><input class="CR" type="number" id="racer1CR" name="racer1CR" min="0" <?php if(isset($_POST['racer1CR'])) { echo 'value="' . $_POST['racer1CR'] . '" '; } ?>/></td><td><input class="CR" type="number" id="racer2CR" name="racer2CR" min="0" <?php if(isset($_POST['racer2CR'])) { echo 'value="' . $_POST['racer2CR'] . '" '; } ?>/></td></tr>
                    <tr><td><label for="racer1Comments">Comments: </label></td><td><input type="text" id="racer1Comments" name="racer1Comments" <?php if(isset($_POST['racer1Comments'])) { echo 'value="' . $_POST['racer1Comments'] . '" '; } ?>/></td><td><input type="text" id="racer2Comments" name="racer2Comments" <?php if(isset($_POST['racer2Comments'])) { echo 'value="' . $_POST['racer2Comments'] . '" '; } ?>/></td></tr>
                    <tr><td><label for="racer1VOD" title="Provide a full link to your VOD, starting with https://.">Link to VODs: </label></td><td><input type="text" id="racer1VOD" name="racer1VOD" <?php if(isset($_POST['racer1VOD'])) { echo 'value="' . $_POST['racer1VOD'] . '" '; } ?>/></td><td><input type="text" id="racer2VOD" name="racer2VOD" <?php if(isset($_POST['racer2VOD'])) { echo 'value="' . $_POST['racer2VOD'] . '" '; } ?>/></td></tr>
                    <tr><td colspan="3" class="submitButton"><input type="Submit" class="submitButton" value="Submit Time" /> <a href="<?= $domain ?>/results/<?= $race_id ?>" class="fakeButton">Show Results Only</a></td></tr>
                </tbody>
            </table>
<?php
if (isset($_SESSION['userid'])) {
    echo '            <input type="hidden" id="enteredBy" name="enteredBy" value="' . $_SESSION['userid'] . '" />' . PHP_EOL;
}
?>
        </form>
