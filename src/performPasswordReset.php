<?php

if (isset($_GET['request'])) {
    $stmt = $pdo->prepare("SELECT email FROM asyncusers WHERE callback_hash = :callback");
    $stmt->bindParam(':callback', $_GET['request'], PDO::PARAM_STR);
    $stmt->execute();
    $email = $stmt->fetchColumn();
    if ($email) {
        echo '        <form method="post" autocomplete="off" action="' . $domain . '/reset">' . PHP_EOL;
        echo '            <table class="register">' . PHP_EOL;
        echo '                <caption>Reset Your Password</caption>' . PHP_EOL;
        echo '                <tbody>' . PHP_EOL;
        echo '                    <tr>' . PHP_EOL;
        echo '                        <td>' . PHP_EOL;
        echo '                            <label for="email">Email: </label>' . PHP_EOL;
        echo '                        </td>' . PHP_EOL;
        echo '                        <td>' . PHP_EOL;
        echo '                            ' . $email . PHP_EOL;
        echo '                        </td>' . PHP_EOL;
        echo '                      </tr>' . PHP_EOL;
        echo '                      <tr>' . PHP_EOL;
        echo '                          <td>' . PHP_EOL;
        echo '                              <label for="password">Password: </label></td><td><input type="password" id="password" name="password" required />' . PHP_EOL;
        echo '                          </td>' . PHP_EOL;
        echo '                      </tr>' . PHP_EOL;
        echo '                      <tr id="passwordRequirements" style="display: none;">' . PHP_EOL;
        echo '                          <td colspan="2">' . PHP_EOL;
        echo '                              <h4>Passwords must contain the following:</h4>' . PHP_EOL;
        echo '                              <span id="lower">At least 1 lowercase letter</span><br />' . PHP_EOL;
        echo '                              <span id="upper">At least 1 uppercase letter</span><br />' . PHP_EOL;
        echo '                              <span id="number">At least 1 number</span><br />' . PHP_EOL;
        echo '                              <span id="special">At least 1 special character</span><br />' . PHP_EOL;
        echo '                              <span id="length">At least 8 characters</span><br />' . PHP_EOL;
        echo '                              <h4>Or; Password must be:</h4>' . PHP_EOL;
        echo '                              <span id="longer">At least 16 characters</span>' . PHP_EOL;
        echo '                          </td>' . PHP_EOL;
        echo '                      </tr>' . PHP_EOL;
        echo '                      <tr>' . PHP_EOL;
        echo '                          <td>' . PHP_EOL;
        echo '                              <label for="confirmPassword">Confirm Password: </label>' . PHP_EOL;
        echo '                          </td>' . PHP_EOL;
        echo '                          <td>' . PHP_EOL;
        echo '                              <input type="password" id="confirmPassword" name="confirmPassword" required />' . PHP_EOL;
        echo '                          </td>' . PHP_EOL;
        echo '                      </tr>' . PHP_EOL;
        echo '                      <tr id="matchingPassword" style="display: none;">' . PHP_EOL;
        echo '                          <td colspan="2">' . PHP_EOL;
        echo '                              <span class="invalid">Passwords do not match!</span>' . PHP_EOL;
        echo '                          </td>' . PHP_EOL;
        echo '                      </tr>' . PHP_EOL;
        echo '                      <tr>' . PHP_EOL;
        echo '                          <td colspan="2">' . PHP_EOL;
        echo '                              <div class="g-recaptcha" data-sitekey="8392acd6-1624-4ad4-8d48-625ad14a9f9c" style="text-align: center;"></div>' . PHP_EOL;
        echo '                          </td>' . PHP_EOL;
        echo '                      </tr>' . PHP_EOL;
        echo '                      <tr>' . PHP_EOL;
        echo '                          <td colspan="2" class="submitButton">' . PHP_EOL;
        echo '                              <input type="hidden" id="email" name="email" value="' . $email . '" />' . PHP_EOL;
        echo '                              <input type="Submit" id="submitButton" class="submitButton" value="Reset Password" disabled />' . PHP_EOL;
        echo '                          </td>' . PHP_EOL;
        echo '                      </tr>' . PHP_EOL;
        echo '                  </tbody>' . PHP_EOL;
        echo '              </table>' . PHP_EOL;
        echo '              </form>' . PHP_EOL;
        echo '              <script src="' . $domain  . '/includes/passwordReset.js"></script>' . PHP_EOL;
    } else {
        echo '        <div class="error">This request is no longer valid. Please submit a new request.</div><br />' . PHP_EOL;
        require_once ('../src/inputPasswordReset.php');
    }
} else {
    echo '        <div class="error">This request is no longer valid. Please submit a new request.</div><br />' . PHP_EOL;
    require_once ('../src/inputPasswordReset.php');
}
