<?php

//Variables passed from form: name, desc, createdBy
//Process form input and output errors or confirmation screen

$errors = null; //Set error variable to empty, check for errors at the end

$series_name = strip_tags($_POST['name']);
$series_description = strip_tags($_POST['description']);
$createdBy = $_POST['createdBy'];

// Check if series name exists in DB, stop and write errors if so
$stmt = $pdo->prepare("SELECT id FROM series WHERE series_name = :series_name");
$stmt->bindValue(':series_name', $series_name, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetchColumn();
if ($row) {
    $errors .= 'There is an <a href="' . $domain . '/series/' . $row . '">series</a> with this name already!<br />';
}

if ($errors != null) {
    echo '        <div class="error">' . $errors . 'Please Try Again</div>' . PHP_EOL;
    include('../src/inputSeries.php');
} else {
    $stmt = $pdo->prepare("INSERT INTO series (series_name, series_description, createdBy) VALUES (:series_name, :series_description, :createdBy)");
    $stmt->bindValue(':series_name', $series_name, PDO::PARAM_STR);
    $stmt->bindValue(':series_description', $series_description, PDO::PARAM_STR);
    $stmt->bindValue(':createdBy', $createdBy, PDO::PARAM_STR);
    $stmt->execute();
    echo '        <table class="submitAsync">' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    echo '                <caption>New Series Created</caption>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Series Name:</th><td>' . $series_name . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Series Description:</th><td>' . $series_description . '</td></tr>' . PHP_EOL;
    echo '                <tr><td colspan="2" class="centerAlign">You will see an option to add asyncs you <a href="' . $domain . '/search">search</a> to this series.</td></tr>' . PHP_EOL;
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
}
