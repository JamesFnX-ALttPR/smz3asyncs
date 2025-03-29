<?php

// Coming from form - email

//  First we check if CAPTCHA is verified

if (isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])) {
    // Get verify response
    $data = array(
        'secret' => "ES_e984ba4ab5324376ada817c539ffa7b5",
        'response' => $_POST['h-captcha-response']
    );
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $verifyResponse = curl_exec($verify);
    $responseData = json_decode($verifyResponse);
    if ($responseData->success) {
        // No matter what, we'll say we emailed the email on file if it exists
        echo '        <div class="error">Thank you for your request. If this matches an email on file, we will send an email with further instructions.</div>' . PHP_EOL;
        // Let's check the email and see if it's in the database
        if ($_POST['email'] != '') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $stmt = $pdo->prepare("SELECT email, displayName, is_banned FROM asyncusers WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row) {
                $displayName = $row['displayName'];
                $banned = $row['is_banned'];
                if ($banned == 'n') {
                    $callback = createCallbackLink();
                    $link = $domain . '/resetpassword/' . $callback;
                    $subject = 'SMZ3 Asyncs - Password Reset Request';
                    $headers = [];
                    $headers[] = 'From: SMZ3 Asyncs <alttprasyncs@gmail.com>';
                    $headers[] = 'Reply-To: alttprasyncs@gmail.com';
                    $headers[] = 'Bcc: jamesfnx@gmail.com';
                    $headers[] = 'X-Mailer: PHP/' . phpversion();
                    $headers[] = 'Content-Type: text/html; charset=iso-8859-1';
                    $headers[] = 'MIME-Version: 1.0';
                    $message = '<html>
    <head>
        <title>SMZ3 Asyncs - Password Reset Request</title>
    </head>
    <body>
        <p>Hello ' . $displayName . '!
        Someone recently requested to reset your password. If this was you, please click on the following link or copy it into your browser:<br />
        <br />
        <a target="_blank" href="' . $link . '">' . $link . '</a><br />
        <br />
        This link will expire in 24 hours.<br />
        If you did not request a password reset, please ignore this email.<br />
        <br />
        Thanks,<br />
        The ALttPR Asyncs Team</p>
    </body>
</html>';
                    mail($email, $subject, $message, implode("\r\n", $headers));
                    $stmt2 = $pdo->prepare("UPDATE asyncusers SET resetCallback = :callback WHERE email = :email");
                    $stmt2->bindParam(':callback', $callback);
                    $stmt2->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt2->execute();
                    $event = "CREATE EVENT remove_" . $callback . " ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 1 DAY DO UPDATE asyncusers SET resetCallback = NULL WHERE email = :email";
                    $stmt2 = $pdo->prepare($event);
                    $stmt2->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt2->execute();
                }
            }
        }
    } else {
        echo '        <div class="error">Unable to verify, please complete CAPTCHA.</div>' . PHP_EOL;
    }
} else {
    echo '        <div class="error">Unable to verify, please complete CAPTCHA.</div>' . PHP_EOL;
}

