<?php
echo '        <form method="post" autocomplete="off" action="' . $domain . '/editresult/' . $resultID . '">' . PHP_EOL;
echo '            <table class="createAsync">' . PHP_EOL;
echo '                <caption>Edit Result for '. $racerName . '<br />' . $raceSlug . '</caption>' . PHP_EOL;
if ($raceIsTeam == 'y') {
    echo '                <tr><td><label for"racerTeam" title="Editing this will update the team name for both team members.">Your Team: </label></td><td><input type="text" id="racerTeam" name="racerTeam" value ="' . $racerTeam . '" /></td></tr>' . PHP_EOL;
}
echo '                <tr><td><label for="racerForfeit">Check to Forfeit: </label></td><td><input type="checkbox" id="racerForfeit" name="racerForfeit" value="y" ';
if ($racerForfeit == 'y') {
    echo 'checked ';
}
echo 'onclick="if (this.checked) { document.getElementsByClassName(\'timeInput\')[0].style.display = \'none\'; document.getElementsByClassName(\'timeInput\')[1].style.display = \'none\'; document.getElementsByClassName(\'timeInput\')[2].style.display = \'none\'; } else { document.getElementsByClassName(\'timeInput\')[0].style.display = \'table-row\'; document.getElementsByClassName(\'timeInput\')[1].style.display = \'table-row\'; document.getElementsByClassName(\'timeInput\')[2].style.display = \'table-row\'; }" /></td></tr>' . PHP_EOL;
echo '                <tr class="timeInput"';
if ($racerForfeit == 'y') {
    echo ' style="display: none;"';
}
echo '><td><label for="racerRTHours" title="Your actual time, *not* the time on the credits screen.">Your Real Time: </label></td><td><input type="number" id="racerRTHours" name="racerRTHours" min="0" max="24" placeholder="HH"';
if(isset($racerRTHours)) {
    echo ' value ="' . $racerRTHours . '"';
}
echo ' />:<input type="number" id="racerRTMinutes" name="racerRTMinutes" min="0" max="59" placeholder="MM"';
if(isset($racerRTMinutes)) {
    echo ' value ="' . $racerRTMinutes . '"';
}
echo ' />:<input type="number" id="racerRTSeconds" name="racerRTSeconds" min="0" max="59" placeholder="SS"';
if(isset($racerRTSeconds)) {
    echo ' value ="' . $racerRTSeconds . '"';
}
echo ' /></td></tr>' . PHP_EOL;
echo '                <tr class="timeInput"';
if ($racerForfeit == 'y') {
    echo ' style="display: none;"';
}
echo '><td><label for="racerCR" title="Your collected checks from the credits screen.">Your Collection Rate: </label></td><td><input class="CR" type="number" id="racerCR" name="racerCR" min="0"';
if($racerCR != null) {
    echo ' value ="' . $racerCR . '"';
}
echo ' /></td></tr>' . PHP_EOL;
echo '                <tr><td><label for="racerComments">Comments: </label></td><td><input type="text" id="racerComments" name="racerComments"';
if($racerComment != null) {
    echo ' value ="' . $racerComment . '"';
}
echo ' /></td></tr>' . PHP_EOL;
echo '                <tr><td><label for="racerVOD" title="Provide a full link to your VOD, starting with https://.">Link to VOD: </label></td><td><input type="text" id="racerVOD" name="racerVOD"';
if($racerVODLink != null) {
    echo ' value ="' . $racerVODLink . '"';
}
if ($vodRequired == 'y') {
    echo ' required';
}
echo ' /></td></tr>' . PHP_EOL;
echo '                <tr><td class="submitButton"><input type="Submit" class="submitButton" value="Edit Result" /></td><td class="submitButton"><a href="' . $domain . '/yourresults' . '" class="fakeButton">Go Back</a></td></tr>' . PHP_EOL;
echo '            </table>' . PHP_EOL;
echo '        <input type="hidden" id="raceSlug" name="raceSlug" value="' . $raceSlug . '" /><input type="hidden" id="racerName" name="racerName" value="' . $racerName . '" />' . PHP_EOL;
echo '        </form>' . PHP_EOL;