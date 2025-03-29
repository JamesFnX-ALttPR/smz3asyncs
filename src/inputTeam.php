<?php

echo 's</caption>' . PHP_EOL;
echo '            <tbody>' . PHP_EOL;
echo '                <tr><td><label for="teamName">Team Name:</label> </td><td colspan="2"><input type="text" size="47" id="teamName" name="teamName" ';
if(isset($_POST['teamName'])) {
    echo 'value="' . $_POST['teamName'] . '" ';
}
echo 'required /></td></tr>' . PHP_EOL;
echo '                <tr><td></td><td><label for="racer1Name">Player 1</label></td><td><label for="racer2Name">Player 2</label></td></tr>' . PHP_EOL;
echo '                <tr><td><label title="If you have races on Racetime, your name will autocomplete.">Racer Names: </label></td><td><input list="racers" id="racer1Name" name="racer1Name" required ';
if(isset($_POST['racer1Name'])) {
    echo 'value="' . $_POST['racer1Name'] . '" ';
} elseif (isset($_SESSION['displayName'])) {
    echo ' value="' . $_SESSION['displayName'] . '"';
}
echo '/></td><td><input list="racers" id="racer2Name" name="racer2Name" required ';
if(isset($_POST['racer2Name'])) {
    echo 'value="' . $_POST['racer2Name'] . '" ';
}
echo '/>' . PHP_EOL;
echo '                    <datalist id="racers">' . PHP_EOL;
$stmt = $pdo->query('SELECT DISTINCT racetimeName FROM racerinfo ORDER BY racetimeName');
foreach($stmt as $row) {
    echo '                    <option value="' . $row['racetimeName'] . '"></option>' . PHP_EOL;
}
echo '                    </datalist>' . PHP_EOL;
echo '                </td></tr>' . PHP_EOL;
echo '                <tr><td><label for="racer1Forfeit">Check to Forfeit: </label></td><td><input type="checkbox" id="racer1Forfeit" name="racer1Forfeit" value="y" onclick="if (this.checked) { document.getElementsByClassName(\'timeInput\')[0].style.display = \'none\'; document.getElementsByClassName(\'timeInput\')[1].style.display = \'none\'; document.getElementsByClassName(\'timeInput\')[2].style.display = \'none\'; } else { document.getElementsByClassName(\'timeInput\')[0].style.display = \'table-row\'; document.getElementsByClassName(\'timeInput\')[1].style.display = \'table-row\'; document.getElementsByClassName(\'timeInput\')[2].style.display = \'table-row\'; }"></td></tr>' . PHP_EOL;
echo '                <tr class="timeInput"><td><label for="racer1RTHours" title="Your actual time, *not* the time on the credits screen.">Real Times: </label></td><td><input type="number" id="racer1RTHours" name="racer1RTHours" min="0" max="24" placeholder="HH" ';
if(isset($_POST['racer1RTHours'])) {
    echo 'value="' . $_POST['racer1RTHours'] . '" ';
}
echo '/>:<input type="number" id="racer1RTMinutes" name="racer1RTMinutes" min="0" max="59" placeholder="MM" ';
if(isset($_POST['racer1RTMinutes'])) {
    echo 'value="' . $_POST['racer1RTMinutes'] . '" ';
}
echo '/>:<input type="number" id="racer1RTSeconds" name="racer1RTSeconds" min="0" max="59" placeholder="SS" ';
if(isset($_POST['racer1RTSeconds'])) {
    echo 'value="' . $_POST['racer1RTSeconds'] . '" ';
}
echo '/></td><td><input type="number" id="racer2RTHours" name="racer2RTHours" min="0" max="24" placeholder="HH" ';
if(isset($_POST['racer2RTHours'])) {
    echo 'value="' . $_POST['racer2RTHours'] . '" ';
}
echo '/>:<input type="number" id="racer2RTMinutes" name="racer2RTMinutes" min="0" max="59" placeholder="MM" ';
if(isset($_POST['racer2RTMinutes'])) {
    echo 'value="' . $_POST['racer2RTMinutes'] . '" ';
}
echo '/>:<input type="number" id="racer2RTSeconds" name="racer2RTSeconds" min="0" max="59" placeholder="SS" ';
if(isset($_POST['racer2RTSeconds'])) {
    echo 'value="' . $_POST['racer2RTSeconds'] . '" ';
}
echo '/></td></tr>' . PHP_EOL;
echo '                <tr class="timeInput"><td><label for="racer1CR" title="Your collected checks from the credits screen.">Your Collection Rate: </label></td><td><input class="CR" type="number" id="racer1CR" name="racer1CR" min="0" ';
if(isset($_POST['racer1CR'])) {
    echo 'value="' . $_POST['racer1CR'] . '" ';
}
echo '/></td><td><input class="CR" type="number" id="racer2CR" name="racer2CR" min="0" ';
if(isset($_POST['racer2CR'])) {
    echo 'value="' . $_POST['racer2CR'] . '" ';
}
echo '/></td></tr>' . PHP_EOL;
echo '                <tr><td><label for="racer1Comments">Comments: </label></td><td><input type="text" id="racer1Comments" name="racer1Comments" ';
if(isset($_POST['racer1Comments'])) {
    echo 'value="' . $_POST['racer1Comments'] . '" ';
}
echo '/></td><td><input type="text" id="racer2Comments" name="racer2Comments" ';
if(isset($_POST['racer2Comments'])) {
    echo 'value="' . $_POST['racer2Comments'] . '" ';
}
echo '/></td></tr>' . PHP_EOL;
echo '                <tr><td><label for="racer1VOD" title="Provide a full link to your VOD, starting with https://.">Link to VODs: </label></td><td><input type="text" id="racer1VOD" name="racer1VOD" ';
if(isset($_POST['racer1VOD'])) {
    echo 'value="' . $_POST['racer1VOD'] . '" ';
}
echo '/></td><td><input type="text" id="racer2VOD" name="racer2VOD" ';
if(isset($_POST['racer2VOD'])) {
    echo 'value="' . $_POST['racer2VOD'] . '" ';
}
echo '/></td></tr>' . PHP_EOL;
echo '                <tr><td colspan="3" class="submitButton"><input type="Submit" class="submitButton" value="Submit Time" /></td></tr>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
if (isset($_SESSION['userid'])) {
    echo '        <input type="hidden" id="enteredBy" name="enteredBy" value="' . $_SESSION['userid'] . '" />' . PHP_EOL;
}
echo '            </form>' . PHP_EOL;
