<form method="post" autocomplete="off" action="<?= $domain ?>/register">
            <table class="register">
                <caption>Register Account</caption>
                <tbody>
                    <tr>
                        <td>
                            <label for="email">Email: </label>
                        </td>
                        <td>
                            <input type="text" id="email" name="email" placeholder="you@domain.com" required <?php if (isset($_POST['email'])) { echo 'value="' . $_POST['email'] . '"'; } ?>/>
                        </td>
                    </tr>
                    <tr id="validEmail" style="display: none;">
                        <td colspan="2">
                            <span class="invalid">Please enter a valid email address.</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="displayName">Display Name: </label>
                        </td>
                        <td>
                            <input type="text" id="displayName" name="displayName" required <?php if (isset($_POST['displayName'])) { echo 'value="' . $_POST['displayName'] . '"'; } ?>/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="password">Password: </label></td><td><input type="password" id="password" name="password" required />
                        </td>
                    </tr>
                    <tr id="passwordRequirements" style="display: none;">
                        <td colspan="2">
                            <h4>Passwords must contain the following:</h4>
                            <span id="lower">At least 1 lowercase letter</span><br />
                            <span id="upper">At least 1 uppercase letter</span><br />
                            <span id="number">At least 1 number</span><br />
                            <span id="special">At least 1 special character</span><br />
                            <span id="length">At least 8 characters</span><br />
                            <h4>Or; Password must be:</h4>
                            <span id="longer">At least 16 characters</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="confirmPassword">Confirm Password: </label>
                        </td>
                        <td>
                            <input type="password" id="confirmPassword" name="confirmPassword" required />
                        </td>
                    </tr>
                    <tr id="matchingPassword" style="display: none;">
                        <td colspan="2">
                            <span class="invalid">Passwords do not match!</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <div class="g-recaptcha" data-sitekey="fbf6c4fa-4400-417e-b998-ff3abbe8e036" style="text-align: center;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="submitButton">
                            <input type="Submit" id="submitButton" class="submitButton" value="Register" disabled />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    <script src="<?= $domain ?>/includes/passwordVerification.js"></script>