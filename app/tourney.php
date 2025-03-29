<?php

require_once ('../includes/bootstrap.php');

if (isset($_GET['slug'])) {
    $slug = strip_tags($_GET['slug']);
    $stmt = $pdo->prepare("SELECT * FROM tournaments WHERE slug = :slug");
    $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch();
    if (! $row) {
        $pageTitle = 'Error Loading Tournament';
        require_once ('../includes/header.php');
        echo '        <div class="error">Unable to lead tournament or no tournament selected. Please try again.</div>' . PHP_EOL;
        require_once ('../src/tournamentCentral.php');
        require_once ('../includes/footer.php');
    } else {
        $name = $row['name'];
        $description = $row['description'];
        $discord = $row['discord_link'];
        $rulesDoc = $row['rules_doc'];
        $maxPlayers = $row['max_players'];
        $opening = $row['opening_rounds'];
        if ($opening == 'swiss') {
            $swissRounds = $row['swiss_rounds'];
        } elseif ($opening == 'groups') {
            $groupSize = $row['group_size'];
            $roundRobins = $row['group_rr'];
        }
        $bracketSize = $row['bracket_size'];
        $bracketStyle = $row['losses_to_eliminate'];
        $startTime = $row['start_time'];
        $complete = $row['complete'];
        $pageTitle = 'Tournament Details - ' . $name;
        require_once ('../includes/header.php');
        require_once ('../src/displayTourneyDetails.php');
        require_once ('../includes/footer.php');
    }
} else {
    $pageTitle = 'Error Loading Tournament';
    require_once ('../includes/header.php');
    echo '        <div class="error">Unable to lead tournament or no tournament selected. Please try again.</div>' . PHP_EOL;
    require_once ('../src/tournamentCentral.php');
    require_once ('../includes/footer.php');
}