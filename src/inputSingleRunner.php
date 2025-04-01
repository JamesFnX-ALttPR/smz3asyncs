                <tbody>
                     <tr><td><label for="racer1Name" title="If you have races on Racetime, your name will autocomplete.">Your Name: </label></td><td><input list="racers" id="racer1Name" name="racer1Name" required <?php if(isset($racerName)) { echo 'value="' . $racerName . '"'; } elseif (isset($_SESSION['display_name'])) { echo 'value="' . $_SESSION['display_name'] . '"'; } ?> />
                        <datalist id="racers">
<?php
$stmt = $pdo->query('SELECT DISTINCT rtgg_name FROM racerinfo ORDER BY rtgg_name');
foreach($stmt as $row) {
    echo '                             <option value="' . $row['rtgg_name'] . '"></option>' . PHP_EOL;
}
?>
                        </datalist>
                    </td></tr>
                    <tr><td><label for="racer1Forfeit">Check to Forfeit: </label></td><td><input type="checkbox" id="racer1Forfeit" name="racer1Forfeit" value="y" onclick="if (this.checked) { document.getElementsByClassName('timeInput')[0].style.display = 'none'; document.getElementsByClassName('timeInput')[1].style.display = 'none'; document.getElementsByClassName('timeInput')[2].style.display = 'none'; } else { document.getElementsByClassName('timeInput')[0].style.display = 'table-row'; document.getElementsByClassName('timeInput')[1].style.display = 'table-row'; document.getElementsByClassName('timeInput')[2].style.display = 'table-row'; }" /></td></tr>
                    <tr class="timeInput"><td><label for="racer1RTHours" title="Your actual time, *not* the time on the credits screen.">Your Real Time: </label></td><td><input type="number" id="racer1RTHours" name="racer1RTHours" min="0" max="24" placeholder="HH"<?php if(isset($_POST['racer1RTHours'])) { echo ' value ="' . $_POST['racer1RTHours'] . '"';} ?> />:<input type="number" id="racer1RTMinutes" name="racer1RTMinutes" min="0" max="59" placeholder="MM"<?php if(isset($_POST['racer1RTMinutes'])) { echo 'value ="' . $_POST['racer1RTMinutes'] . '"'; } ?> />:<input type="number" id="racer1RTSeconds" name="racer1RTSeconds" min="0" max="59" placeholder="SS"<?php if(isset($_POST['racer1RTSeconds'])) { echo ' value ="' . $_POST['racer1RTSeconds'] . '"'; } ?> /></td></tr>
                    <tr class="timeInput"><td><label for="racer1CR" title="Your collected checks from the credits screen.">Your Collection Rate: </label></td><td><input class="CR" type="number" id="racer1CR" name="racer1CR" min="0"<?php if(isset($_POST['racer1CR'])) { echo ' value ="' . $_POST['racer1CR'] . '"'; } ?>/></td></tr>
                    <tr><td><label for="racer1Comments">Comments: </label></td><td><input type="text" id="racer1Comments" name="racer1Comments"<?php if(isset($_POST['racer1Comments'])) { echo ' value ="' . $_POST['racer1Comments'] . '"'; } ?> /></td></tr>
                    <tr><td><label for="racer1VOD" title="Provide a full link to your VOD, starting with https://.">Link to VOD: </label></td><td><input <?php if($race_vod_flag == 'y') { echo 'required '; } ?>type="text" id="racer1VOD" name="racer1VOD"<?php if(isset($_POST['racer1VOD'])) { echo ' value ="' . $_POST['racer1VOD'] . '"'; } ?> /></td></tr>
                    <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Submit Time" /></td><td class="submitAsync"><a href="<?= $domain ?>/results/<?= $race_id  ?>" class="fakeButton">Show Results Only</a></td></tr>
                </tbody>
            </table>
<?php
if (isset($_SESSION['userid'])) {
    echo '            <input type="hidden" id="enteredBy" name="enteredBy" value="' . $_SESSION['userid'] . '" />' . PHP_EOL;
}
?>
        </form>
