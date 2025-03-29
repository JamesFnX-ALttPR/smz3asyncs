<?php

//Variables passed from form: name, desc, createdBy
//Process form input and output errors or confirmation screen

$errors = null; //Set error variable to empty, check for errors at the end

$seriesName = strip_tags($_POST['name']);
$seriesDescription = strip_tags($_POST['description']);
$createdBy = $_POST['createdBy'];

// Check if series name exists in DB, stop and write errors if so
$stmt = $pdo->prepare("SELECT id FROM series WHERE seriesName = :seriesName");
$stmt->bindValue(':seriesName', $seriesName, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetchColumn();
if ($row) {
    $errors .= 'There is an <a href="' . $domain . '/series/' . $row . '">series</a> with this name already!<br />';
}

if ($errors != null) {
    echo '        <div class="error">' . $errors . 'Please Try Again</div>' . PHP_EOL;
    include('../src/inputSeries.php');
} else {
    $stmt = $pdo->prepare("INSERT INTO series (seriesName, seriesDescription, createdBy) VALUES (:seriesName, :seriesDescription, :createdBy)");
    $stmt->bindValue(':seriesName', $seriesName, PDO::PARAM_STR);
    $stmt->bindValue(':seriesDescription', $seriesDescription, PDO::PARAM_STR);
    $stmt->bindValue(':createdBy', $createdBy, PDO::PARAM_STR);
    $stmt->execute();
    echo '        <table class="submitAsync">' . PHP_EOL;
    echo '            <thead>' . PHP_EOL;
    echo '                <caption>New Series Created</caption>' . PHP_EOL;
    echo '            </thead>' . PHP_EOL;
    echo '            <tbody>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Series Name:</th><td>' . $seriesName . '</td></tr>' . PHP_EOL;
    echo '                <tr><th class="rightAlign">Series Description:</th><td>' . $seriesDescription . '</td></tr>' . PHP_EOL;
    echo '                <tr><td colspan="2" class="centerAlign">You will see an option to add asyncs you <a href="' . $domain . '/search">search</a> to this series.</td></tr>' . PHP_EOL;
    echo '            </tbody>' . PHP_EOL;
    echo '        </table>' . PHP_EOL;
    /*$to = 'jamesfnx@gmail.com';
    $subject = 'ALttPR Asyncs - New Series Created';
    $headers = 'From: asyncs@alttprasyncs.com';
    $body = 'Hello!' . '\r\n' . 'A user has created a new series on alttprasyncs.com!' . '\r\n' . $_SESSION['displayName'] . ' - ' . $seriesName . '\r\n' . 'Check it out and see if they need help.';
    mail($to, $subject, $body, $headers); */
}
