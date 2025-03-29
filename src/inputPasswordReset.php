<form method="post" autocomplete="off" action="<?= $domain ?>/resetpassword">
            <table class="register">
                <caption>Reset Your Password</caption>
                <tbody>
                    <tr>
                        <td>
                            <label for="email">Registered Email: </label>
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
                        <td colspan="2">
                        <div class="g-recaptcha" data-sitekey="8392acd6-1624-4ad4-8d48-625ad14a9f9c" style="text-align: center;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="submitButton">
                            <input type="Submit" id="submitButton" class="submitButton" value="Reset Password" disabled />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    <script src="<?= $domain ?>/includes/emailVerification.js"></script>