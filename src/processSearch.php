<?php
//Series creation section
if (isset ($_SESSION['userid'])) {
    $user_id = $_SESSION['userid'];
    require_once ('../includes/user_info.php');
}
// Find out if there are any series to add races to - if so, we'll add a column and form for that
if (isset ($admin_flag)) {
    if ($admin_flag == 'y') {
        $stmt = $pdo->prepare("SELECT id FROM series");
        $stmt->execute();
        $rslt = $stmt->fetchColumn();
        if ($rslt) {
            $seriesColumn = 'y';
        } else {
            $seriesColumn = 'n';
        }
    }
} elseif (isset ($series_flag)) {
    if ($series_flag == 'y') {
        $stmt = $pdo->prepare("SELECT id FROM series WHERE createdBy = :createdBy");
        $stmt->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
        $stmt->execute();
        $rslt = $stmt->fetchColumn();
        if ($rslt) {
            $seriesColumn = 'y';
        } else {
            $seriesColumn = 'n';
        }
    }
} else {
    $seriesColumn = 'n';
}
// End series creation section
if ($_POST['searchBox'] != '') {
    $searchTerm = strip_tags($_POST['searchBox']);
    $searchTermLike = '%' . $searchTerm . '%';
} else {
    $searchTerm = '';
}
if ($_POST['hash1'] != '' && $_POST['hash2'] != '' && $_POST['hash3'] != '' && $_POST['hash4'] != '') {
    $searchHash = '(' . $_POST['hash1'] . ' ' . $_POST['hash2'] . ' ' . $_POST['hash3'] . ' ' . $_POST['hash4'] . ')';
} else {
    $searchHash = '';
}
if (isset($_POST['includeRunner'])) {
    $includeRunner = $_POST['includeRunner'];
}
if (isset($_POST['excludeRunner'])) {
    $excludeRunner = $_POST['excludeRunner'];
}
if (isset($_POST['raceType'])) {
    $raceType = $_POST['raceType'];
}
if (isset($_POST['filter_coop'])) {
    $filter_coop = $_POST['filter_coop'];
}
if ($searchTerm == '' && $searchHash == '') {
    echo '        <div class="error">You must search for a term or a full hash. Please try again.</div>' . PHP_EOL;
    include('../src/selectJS.php');
    include('../src/inputSearch.php');
} else {
    if($_POST['startDate'] != '') {
        $startDate = $_POST['startDate'];
    }
    if($_POST['endDate'] != '') {
        $endDate = date_create($_POST['endDate'])->modify('+1 day -1 millisecond')->format('Y-m-d H:i:s');
    }
    if(isset($startDate) && isset($endDate)) {
        $dateQuery = ' AND raceStart BETWEEN \'' . $startDate . '\' AND \'' . $endDate . '\'';
    } elseif (isset($startDate) && !isset($endDate)) {
        $dateQuery = ' AND raceStart > \'' . $startDate . '\'';
    } elseif (!isset($startDate) && isset($endDate)) {
        $dateQuery = ' AND raceStart < \'' . $endDate . '\'';
    } else {
        $dateQuery = '';
    }
    // Let's make the SQL query
    if ($searchTerm != '' && $searchHash != '') {
        $searchQuery = "SELECT COUNT(id) FROM races WHERE tournament_seed = 'n' AND (raceMode = :raceMode OR raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = :rtgg_name)) OR raceSlug LIKE :raceSlug OR raceSeed LIKE :raceSeed OR raceDescription LIKE :raceDescription OR raceHash = :raceHash)" . $dateQuery;
        if (isset($includeRunner) && $includeRunner != '') {
            $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $includeRunner . "'))";
        }
        if (isset($excludeRunner) && $excludeRunner !== '') {
            $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID NOT IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $excludeRunner . "'))";
        }
        if (isset($raceType)) {
            if ($raceType == 'racetime') {
                $searchQuery .= " AND raceFromRacetime = 'y'";
            } elseif ($raceType == 'custom') {
                $searchQuery .= " AND raceFromRacetime = 'n'";
            }
        }
        if (isset($filter_coop)) {
            if ($filter_coop == 'solo') {
                $searchQuery .= " AND raceIsTeam = 'n'";
            } elseif ($filter_coop == 'coop') {
                $searchQuery .= " AND raceIsTeam = 'y'";
            }
        }
        $searchQuery .= " ORDER BY raceStart DESC";
        $stmt = $pdo->prepare($searchQuery);
        $stmt->bindValue(':raceMode', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':rtgg_name', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':raceSlug', $searchTermLike, PDO::PARAM_STR);
        $stmt->bindValue(':raceSeed', $searchTermLike, PDO::PARAM_STR);
        $stmt->bindValue(':raceDescription', $searchTermLike, PDO::PARAM_STR);
        $stmt->bindValue(':raceHash', $searchHash, PDO::PARAM_STR);
        $stmt->execute();
    } elseif ($searchTerm != '' && $searchHash == '') {
        $searchQuery = "SELECT COUNT(id) FROM races WHERE tournament_seed = 'n' AND (raceMode = :raceMode OR raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = :rtgg_name)) OR raceSlug LIKE :raceSlug OR raceSeed LIKE :raceSeed OR raceDescription LIKE :raceDescription)" . $dateQuery;
        if (isset($includeRunner) && $includeRunner != '') {
            $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $includeRunner . "'))";
        }
        if (isset($excludeRunner) && $excludeRunner != '') {
            $searchQuery .= " AND raceSlug NOT IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $excludeRunner . "'))";
        }
        if (isset($raceType)) {
            if ($raceType == 'racetime') {
                $searchQuery .= " AND raceFromRacetime = 'y'";
            } elseif ($raceType == 'custom') {
                $searchQuery .= " AND raceFromRacetime = 'n'";
            }
        }
        if (isset($filter_coop)) {
            if ($filter_coop == 'solo') {
                $searchQuery .= " AND raceIsTeam = 'n'";
            } elseif ($filter_coop == 'coop') {
                $searchQuery .= " AND raceIsTeam = 'y'";
            }
        }
        $searchQuery .= " ORDER BY raceStart DESC";
        $stmt = $pdo->prepare($searchQuery);
        $stmt->bindValue(':raceMode', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':rtgg_name', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':raceSlug', $searchTermLike, PDO::PARAM_STR);
        $stmt->bindValue(':raceSeed', $searchTermLike, PDO::PARAM_STR);
        $stmt->bindValue(':raceDescription', $searchTermLike, PDO::PARAM_STR);
        $stmt->execute();
    } elseif ($searchTerm == '' && $searchHash != '') {
        $searchQuery = "SELECT COUNT(id) FROM races WHERE tournament_seed = 'n' AND raceHash = :raceHash" . $dateQuery;
        if (isset($includeRunner) && $includeRunner != '') {
            $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $includeRunner . "'))";
        }
        if (isset($excludeRunner) && $excludeRunner != '') {
            $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID NOT IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $excludeRunner . "'))";
        }
        if (isset($raceType)) {
            if ($raceType == 'racetime') {
                $searchQuery .= " AND raceFromRacetime = 'y'";
            } elseif ($raceType == 'custom') {
                $searchQuery .= " AND raceFromRacetime = 'n'";
            }
        }
        if (isset($filter_coop)) {
            if ($filter_coop == 'solo') {
                $searchQuery .= " AND raceIsTeam = 'n'";
            } elseif ($filter_coop == 'coop') {
                $searchQuery .= " AND raceIsTeam = 'y'";
            }
        }
        $searchQuery .= " ORDER BY raceStart DESC";
        $stmt = $pdo->prepare($searchQuery);
        $stmt->bindValue(':raceHash', $searchHash, PDO::PARAM_STR);
        $stmt->execute();
    }
    $rslt = $stmt->fetchColumn();
    if (! $rslt) {
        echo '        <div class="error">No results found for your search. Please try again.</div>' . PHP_EOL;
        include('../src/selectJS.php');
        include('../src/inputSearch.php');
    } else {
        if ($searchTerm != '' && $searchHash != '') {
            $searchQuery = "SELECT rtgg_name FROM racerinfo WHERE racetimeID in (SELECT racerRacetimeID FROM results WHERE raceSlug in (SELECT raceSlug FROM races WHERE (raceMode = :raceMode OR raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = :rtgg_name)) OR raceSlug LIKE :raceSlug OR raceSeed LIKE :raceSeed OR raceDescription LIKE :raceDescription OR raceHash = :raceHash)" . $dateQuery . ")) ORDER BY rtgg_name";
            $stmt = $pdo->prepare($searchQuery);
            $stmt->bindValue(':raceMode', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':rtgg_name', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':raceSlug', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceSeed', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceDescription', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceHash', $searchHash, PDO::PARAM_STR);
        } elseif ($searchTerm != '' && $searchHash == '') {
            $searchQuery = "SELECT rtgg_name FROM racerinfo WHERE racetimeID in (SELECT racerRacetimeID FROM results WHERE raceSlug in (SELECT raceSlug FROM races WHERE (raceMode = :raceMode OR raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = :rtgg_name)) OR raceSlug LIKE :raceSlug OR raceSeed LIKE :raceSeed OR raceDescription LIKE :raceDescription)" . $dateQuery . ")) ORDER BY rtgg_name";
            $stmt = $pdo->prepare($searchQuery);
            $stmt->bindValue(':raceMode', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':rtgg_name', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':raceSlug', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceSeed', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceDescription', $searchTermLike, PDO::PARAM_STR);
        } elseif ($searchTerm == '' && $searchHash != '') {
            $searchQuery = "SELECT rtgg_name FROM racerinfo WHERE racetimeID in (SELECT racerRacetimeID FROM results WHERE raceSlug in (SELECT raceSlug FROM races WHERE raceHash = :raceHash" . $dateQuery . ")) ORDER BY rtgg_name";
            $stmt = $pdo->prepare($searchQuery);
            $stmt->bindValue(':raceHash', $searchHash, PDO::PARAM_STR);
        }
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $players[] = $row['rtgg_name'];
        }
        echo '        <form id="refineSearch" method="post" action="">' . PHP_EOL;
        echo '            <table class="searchRefine">' . PHP_EOL;
        echo '                <caption>Refine Your Search</caption>' . PHP_EOL;
        echo '                <thead>' . PHP_EOL;
        echo '                    <tr><th><label for="includeRunner">Include Runner</label></th><th><label for="excludeRunner">Exclude Runner</label></th><th><label for="raceType">Types of Races</label></th><th><label for="filter_coop">Filter Co-Op</th><th><label>Change Date Range</label></th></tr>' . PHP_EOL;
        echo '                </thead>' . PHP_EOL . '                <tbody>' . PHP_EOL;
        echo '                    <tr><td><select class="js-example-basic-single" id="includeRunner" name="includeRunner" form="refineSearch">' . PHP_EOL;
        echo '                        <option value=""></option>' . PHP_EOL;
        foreach ($players as $name) {
            echo '                        <option value="' . $name . '"';
            if (isset ($includeRunner) && $name == $includeRunner) {
                echo ' selected';
            }
            echo '>' . $name . '</option>' . PHP_EOL;
        }
        echo '                     </select></td><td><select class="js-example-basic-single" id="excludeRunner" name="excludeRunner" form="refineSearch">' . PHP_EOL;
        echo '                        <option value=""></option>' . PHP_EOL;
        foreach ($players as $name) {
            echo '                        <option value="' . $name . '"';
            if (isset ($excludeRunner) && $name == $excludeRunner) {
                echo ' selected';
            }
            echo '>' . $name . '</option>' . PHP_EOL;
        }
        echo '                     </select></td><td><input type="radio" id="raceType1" name="raceType" value="racetime" /> <label for="raceType1">Racetime Races Only</label><br /><input type="radio" id="raceType2" name="raceType" value="custom" /> <label for="raceType2">Custom Asyncs Only</label><br /><input type="radio" id="raceType3" name="raceType" value="both" checked /> <label for="raceType3">All Races</label></td><td><input type="radio" id="filter_coop1" name="filter_coop" value="solo" /> <label for="filter_coop1">Solo Races Only</label><br /><input type="radio" id="filter_coop2" name="filter_coop" value="coop" /> <label for="filter_coop2">Co-Op Races Only</label><br /><input type="radio" id="filter_coop3" name="filter_coop" value="all" checked /> <label for="filter_coop3">All Races</label></td><td><label for="startDate">From:</label> <input type="date" id="startDate" name="startDate" min="2022-02-21" max="' . date("Y-m-d") . '"';
        if (isset($startDate)) {
            echo ' value="' . $startDate . '"';
        }
        echo ' /><br /><div style="text-align: right;"><label for="endDate">To:</label> <input type="date" id="endDate" name="endDate" min="2022-02-21" max="' . date("Y-m-d") . '"';
        if (isset($endDate)) {
            echo ' value="' . $endDate . '"';
        }
        echo ' /></div></td></tr>' . PHP_EOL;
        echo '                     <tr><td colspan="5" class="submitButton"><input type="Submit" class="submitButton" value="Refine Search" /></td></tr>' . PHP_EOL;
        echo '                 </tbody>' . PHP_EOL;
        echo '             </table>' . PHP_EOL;
        echo '             <input type="hidden" id="searchBox" name="searchBox" value="' . $searchTerm . '" />';
        echo '<input type="hidden" id="hash1" name="hash1" value="' . $_POST['hash1'] . '" /><input type="hidden" id="hash2" name="hash2" value="' . $_POST['hash2'] . '" /><input type="hidden" id="hash3" name="hash3" value="' . $_POST['hash3'] . '" /><input type="hidden" id="hash4" name="hash4" value="' . $_POST['hash4'] . '" />' . PHP_EOL;
        echo '        </form><hr /><br />' . PHP_EOL;
        echo '        <table class="searchResults sortable">' . PHP_EOL;
        echo '            <caption class="searchResults">Search Results for ';
        if ($searchTerm != '' && $searchHash != '') {
            echo $searchTerm . ' - ' . hashToTable($searchHash);
        } elseif ($searchTerm != '' && $searchHash == '') {
            echo $searchTerm;
        } else {
            echo hashToTable($searchHash);
        }
        echo '</caption>' . PHP_EOL;
        echo '            <thead>' . PHP_EOL;
        echo '                <tr><th>Date (UTC)</th><th>Mode</th><th>Description</th><th>Racetime Room</th><th>Seed</th><th>Hash</th><th>Participants</th><th>Async</th><th>Results</th>';
        if ($seriesColumn == 'y') {
            echo '<th><form method="post" action="' . $domain . '/addtoseries" id="addtoseries"><input type="submit" class="submitButton" form="addtoseries" value="Add to Series" /></form></th>';
        }
        echo '</tr>' . PHP_EOL;
        echo '            </thead>' . PHP_EOL;
        echo '            <tbody>' . PHP_EOL;
        if ($searchTerm != '' && $searchHash != '') {
            $searchQuery = "SELECT id FROM races WHERE tournament_seed = 'n' AND (raceMode = :raceMode OR raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = :rtgg_name)) OR raceSlug LIKE :raceSlug OR raceSeed LIKE :raceSeed OR raceDescription LIKE :raceDescription OR raceHash = :raceHash)" . $dateQuery;
            if (isset($includeRunner) && $includeRunner != '') {
                $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $includeRunner . "'))";
            }
            if (isset($excludeRunner) && $excludeRunner !== '') {
                $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID NOT IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $excludeRunner . "'))";
            }
            if (isset($raceType)) {
                if ($raceType == 'racetime') {
                    $searchQuery .= " AND raceFromRacetime = 'y'";
                } elseif ($raceType == 'custom') {
                    $searchQuery .= " AND raceFromRacetime = 'n'";
                }
            }
            if (isset($filter_coop)) {
                if ($filter_coop == 'solo') {
                    $searchQuery .= " AND raceIsTeam = 'n'";
                } elseif ($filter_coop == 'coop') {
                    $searchQuery .= " AND raceIsTeam = 'y'";
                }
            }
            $searchQuery .= " ORDER BY raceStart DESC";
            $stmt = $pdo->prepare($searchQuery);
            $stmt->bindValue(':raceMode', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':rtgg_name', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':raceSlug', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceSeed', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceDescription', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceHash', $searchHash, PDO::PARAM_STR);
            $stmt->execute();
        } elseif ($searchTerm != '' && $searchHash == '') {
            $searchQuery = "SELECT id FROM races WHERE tournament_seed = 'n' AND (raceMode = :raceMode OR raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = :rtgg_name)) OR raceSlug LIKE :raceSlug OR raceSeed LIKE :raceSeed OR raceDescription LIKE :raceDescription)" . $dateQuery;
            if (isset($includeRunner) && $includeRunner != '') {
                $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $includeRunner . "'))";
            }
            if (isset($excludeRunner) && $excludeRunner != '') {
                $searchQuery .= " AND raceSlug NOT IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $excludeRunner . "'))";
            }
            if (isset($raceType)) {
                if ($raceType == 'racetime') {
                    $searchQuery .= " AND raceFromRacetime = 'y'";
                } elseif ($raceType == 'custom') {
                    $searchQuery .= " AND raceFromRacetime = 'n'";
                }
            }
            if (isset($filter_coop)) {
                if ($filter_coop == 'solo') {
                    $searchQuery .= " AND raceIsTeam = 'n'";
                } elseif ($filter_coop == 'coop') {
                    $searchQuery .= " AND raceIsTeam = 'y'";
                }
            }
            $searchQuery .= " ORDER BY raceStart DESC";
            $stmt = $pdo->prepare($searchQuery);
            $stmt->bindValue(':raceMode', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':rtgg_name', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':raceSlug', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceSeed', $searchTermLike, PDO::PARAM_STR);
            $stmt->bindValue(':raceDescription', $searchTermLike, PDO::PARAM_STR);
            $stmt->execute();
        } elseif ($searchTerm == '' && $searchHash != '') {
            $searchQuery = "SELECT id FROM races WHERE tournament_seed = 'n' AND raceHash = :raceHash" . $dateQuery;
            if (isset($includeRunner) && $includeRunner != '') {
                $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $includeRunner . "'))";
            }
            if (isset($excludeRunner) && $excludeRunner != '') {
                $searchQuery .= " AND raceSlug IN (SELECT raceSlug FROM results WHERE racerRacetimeID NOT IN (SELECT racetimeID FROM racerinfo WHERE rtgg_name = '" . $excludeRunner . "'))";
            }
            if (isset($raceType)) {
                if ($raceType == 'racetime') {
                    $searchQuery .= " AND raceFromRacetime = 'y'";
                } elseif ($raceType == 'custom') {
                    $searchQuery .= " AND raceFromRacetime = 'n'";
                }
            }
            if (isset($filter_coop)) {
                if ($filter_coop == 'solo') {
                    $searchQuery .= " AND raceIsTeam = 'n'";
                } elseif ($filter_coop == 'coop') {
                    $searchQuery .= " AND raceIsTeam = 'y'";
                }
            }
            $searchQuery .= " ORDER BY raceStart DESC";
            $stmt = $pdo->prepare($searchQuery);
            $stmt->bindValue(':raceHash', $searchHash, PDO::PARAM_STR);
            $stmt->execute();
        }
        $rowCounter = 0;
        while($row = $stmt->fetch()) {
            $rowCounter++;
            if($rowCounter % 2 == 0) {
                $startOfRow = '                <tr class="even">';
            } else {
                $startOfRow = '                <tr class="odd">';
            }
            $race_id = $row['id'];
            require ('../includes/race_info.php');
            echo $startOfRow . '<td>' . $race_date . '</td><td>' . $race_mode . '</td><td>' . $race_description_short . '</td><td>';
            if ($race_from_racetime == 'y' ) {
                echo '<a target="_blank" href="https://racetime.gg/smz3/' . $race_slug . '">';
            }
            echo $race_slug;
            if ($race_from_racetime == 'y') {
                echo '</a>';
            }
            echo '</td><td><a target="_blank" href="' . $race_seed . '">Download Seed</a></td><td>' . hashToImages($race_hash) . '</td><td>' . $participant_count . '<td><a href="' . $domain . '/async/' . $race_id . '">Submit Async</a></td><td><a href="' . $domain . '/results/' . $race_id . '">View Results</a></td>';
            if ($seriesColumn == 'y') {
                echo '<td><select form="addtoseries" id="seed_' . $race_id . '" name="seed_' . $race_id . '"><option value=""></option>';
                if ($admin_flag == 'y') {
                    $stmt2 = $pdo->prepare("SELECT id, series_name FROM series");
                } elseif ($series_flag == 'y') {
                    $stmt2 = $pdo->prepare("SELECT id, series_name FROM series WHERE createdBy = :createdBy");
                    $stmt2->bindValue(':createdBy', $_SESSION['userid'], PDO::PARAM_INT);
                }
                $stmt2->execute();
                while ($row2 = $stmt2->fetch()) {
                    echo '<option value="' . $row2['id'] . '">' . $row2['series_name'] . '</option>';
                }
                echo '</select></td>';
            }
            echo '</tr>' . PHP_EOL;
        }
        echo '            </tbody>' . PHP_EOL;
        echo '        </table>' . PHP_EOL;
    }
}