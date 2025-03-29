<?php

echo '<script>' . PHP_EOL;
echo '$(document).ready(function(){' . PHP_EOL;
echo '    $(\'input[type="radio"]\').click(function(){' . PHP_EOL;
echo '        var inputValue = $(this).attr("value");' . PHP_EOL;
echo '        var targetBox = $("." + inputValue);' . PHP_EOL;
echo '        $(".box").not(targetBox).hide();' . PHP_EOL;
echo '        $(targetBox).show();' . PHP_EOL;
echo '    });' . PHP_EOL;
echo '});' . PHP_EOL;
echo '</script>' . PHP_EOL;
echo '        <form method="post" action="' . $domain .'/toportal">' . PHP_EOL;
echo '        <table class="input">' . PHP_EOL;
echo '            <caption>Enter Tournament Information</caption>' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label for="t_name">Tournament Name:</label> </th><td><input type="text" id="t_name" name="t_name" required /></td></tr>' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label for="t_desc">Description:</label> </th><td><textarea id="t_desc" name="t_desc" rows="3" cols="35">Enter a brief description of the tournament.</textarea></td></tr>' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label for="t_start">Start Date/Time (';
if (date('I') == 1) {
    echo 'EDT';
} else {
    echo 'EST';
}
echo '):</label> </th><td><input type="datetime-local" id="t_start" name="t_start" required /></td></tr>' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label class="passwordLabel" for="t_discord" title="Full link to the Discord hosting the tournament.">Link to Discord:</label> </th><td><input type="text" id="t_discord" name="t_discord" /></td></tr>' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label class="passwordLabel" for="t_rulesdoc" title="Full link to the rules document for the tournament.">Link to Rules Doc:</label> </th><td><input type="text" id="t_rulesdoc" name="t_rulesdoc" /></td></tr>' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label class="passwordLabel" for="t_maxplayers" title="The maximum number of entries allowed for your tourney. Leave blank or enter 0 if there is no entry cap.">Max Players:</label> </th><td><input type="number" min="0" id="t_maxplayers" name="t_maxplayers" required />' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label class="passwordLabel" for="t_openingrounds" title="Choose the opening round type: Swiss pairings, Groups, on None (straight to brackets).">Opening Rounds:</label> </th><td><input type="radio" id="openingrounds1" name="t_openingrounds" value="swiss" /> <label for="openingrounds1">Swiss</label><br /><input type="radio" id="openingrounds2" name="t_openingrounds" value="groups" /> <label for="openingrounds2">Groups</label><br /><input type="radio" id="openingrounds3" name="t_openingrounds" value="none" checked /> <label for="openingrounds3">None</label></td></tr>' . PHP_EOL;
echo '            <tr class="swiss box"><th class="tourneyFormLabel"><label class="passwordLabel" for="t_swissrounds" title="Enter the number of rounds of Swiss pairings before cutting to brackets.">Swiss Rounds:</label> </th><td><input type="number" id="t_swissrounds" name="t_swissrounds" min="1" /></td></tr>' . PHP_EOL;
echo '            <tr class="groups box"><th class="tourneyFormLabel"><label class="passwordLabel" for="t_groupsize" title="Enter the number of players per group. If there is an uneven number of players, some groups will run with one less player.">Group Size:</label> </th><td><input type="number" id="t_groupsize" name="t_groupsize" min="2" /></td></tr>' . PHP_EOL;
echo '            <tr class="groups box"><th class="tourneyFormLabel"><label class="passwordLabel" for="t_grouprr" title="Enter the number of matches each player will play vs. each other person in the group.">Matches per Opponent in Group:</label> </th><td><input type="number" id="t_grouprr" name="t_grouprr" min="1" /></td><tr>' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label class="passwordLabel" for="t_bracketsize" title="If \'None\' is selected for opening rounds, all players will make brackets. Leave this blank.">Players in Brackets:</label> </th><td><input type="number" id="t_bracketsize" name="t_bracketsize" min="0" /></td></tr>' . PHP_EOL;
echo '            <tr><th class="tourneyFormLabel"><label for="t_losses">Bracket Style:</label> </th><td><input type="radio" id="singleelim" name="t_losses" value="1" checked /> <label for="singleelim">Single Elimination</label><br /><input type="radio" id="doubleelim" name="t_losses" value="2" /> <label for="doubleelim">Double Elimination</label></td></tr>' . PHP_EOL;
echo '            <tr><td colspan="2" class="submitButton"><input type="Submit" class="submitButton" value="Create Tournament" /></td></tr>' . PHP_EOL;
echo '        </table>' . PHP_EOL;
echo '        </form>' . PHP_EOL;
