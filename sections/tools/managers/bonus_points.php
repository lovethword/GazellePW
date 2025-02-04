<?php
if (!check_perms('users_mod')) {
    error(403);
}
$Message = "";
if (isset($_REQUEST['add_points'])) {
    authorize();
    $Points = floatval($_REQUEST['num_points']);

    if ($Points < 0) {
        error('Please enter a valid number of points.');
    }

    $sql = "
		UPDATE users_main
		SET BonusPoints = BonusPoints + {$Points}
		WHERE Enabled = '1'";
    $DB->query($sql);
    $sql = "
		SELECT ID
		FROM users_main
		WHERE Enabled = '1'";
    $DB->query($sql);
    while (list($UserID) = $DB->next_record()) {
        $Cache->delete_value("user_stats_{$UserID}");
    }
    $Message = '<strong>' . number_format($Points) . ' bonus points added to all enabled users.</strong><br /><br />';
}

View::show_header(Lang::get('tools', 'add_bonus_sitewide'), '', 'PageToolBonusPoint');

?>
<div class="BodyHeader">
    <h2 class="BodyHeader-nav"><?= Lang::get('tools', 'add_bonus_points_to_all_users') ?></h2>
</div>
<div class="BoxBody" style="margin-left: auto; margin-right: auto; text-align: center; max-width: 40%;">
    <?= $Message ?>
    <form class="add_form" name="fltokens" action="" method="post">
        <input type="hidden" name="action" value="bonus_points" />
        <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
        <?= Lang::get('tools', 'points_to_add') ?>: <input class="Input" type="text" name="num_points" size="10" style="text-align: right;" /><br /><br />
        <input class="Button" type="submit" name="add_points" value="Add points" />
    </form>
</div>
<br />
<?

View::show_footer();
?>