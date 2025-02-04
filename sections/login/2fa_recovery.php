<? View::show_header('Two-factor Authentication', '', 'PageLogin2FARecovery'); ?>
<span id="no-cookies" class="hidden u-colorWarning">You appear to have cookies disabled.<br /><br /></span>
<noscript><span class="u-colorWarning"><?= SITE_NAME ?> requires JavaScript to function properly. Please enable JavaScript in your browser.</span><br /><br />
</noscript>
<?
if (strtotime($BannedUntil) < time()) {
?>
    <form class="auth_form" name="login" id="loginform" method="post" action="login.php?act=2fa_recovery">
        <?

        if (!empty($BannedUntil) && $BannedUntil != '0000-00-00 00:00:00') {
            $DB->query("
			UPDATE login_attempts
			SET BannedUntil = '0000-00-00 00:00:00', Attempts = '0'
			WHERE ID = '" . db_string($AttemptID) . "'");
            $Attempts = 0;
        }
        if (isset($Err)) {
        ?>
            <span class="u-colorWarning"><?= $Err ?><br /><br /></span>
        <? } ?>
        <? if ($Attempts > 0) { ?>
            You have <span class="info"><?= (6 - $Attempts) ?></span> attempts remaining.<br /><br />
            <strong>WARNING:</strong> You will be banned for 6 hours after your login attempts run out!<br /><br />
        <? } ?>
        Note: You will only be able to use a recovery key once!
        <table class="layout">
            <tr>
                <td>2FA Recovery Key&nbsp;</td>
                <td colspan="2">
                    <input class="Input" type="text" name="2fa_recovery_key" id="2fa_recovery_key" required="required" autofocus="autofocus" placeholder="2FA Recovery Key" />
                </td>
            </tr>

            <tr>
                <td></td>
                <td><input class="Button" type="submit" name="login" value="Log in" /></td>
            </tr>
        </table>
    </form>
    <br /><br />
<?
} else {
?>
    <span class="u-colorWarning">You are banned from logging in for another <?= time_diff($BannedUntil) ?>.</span>
<?
}

?>
<script type="text/javascript">
    cookie.set('cookie_test', 1, 1);
    if (cookie.get('cookie_test') != null) {
        cookie.del('cookie_test');
    } else {
        $('#no-cookies').gshow();
    }
</script>
<? View::show_footer(); ?>