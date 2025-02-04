<?

enforce_login();
if (!check_perms('site_moderate_forums')) {
    error(403);
}


$ForumID = $_GET['forumid'];
if (!is_number($ForumID)) {
    error(404);
}


if (!empty($_POST['add']) || (!empty($_POST['del']))) {
    if (!empty($_POST['add'])) {
        if (is_number($_POST['new_thread'])) {
            $DB->query("
				INSERT INTO forums_specific_rules (ForumID, ThreadID)
				VALUES ($ForumID, " . $_POST['new_thread'] . ')');
        }
    }
    if (!empty($_POST['del'])) {
        if (is_number($_POST['threadid'])) {
            $DB->query("
				DELETE FROM forums_specific_rules
				WHERE ForumID = $ForumID
					AND ThreadID = " . $_POST['threadid']);
        }
    }
    $Cache->delete_value('forums_list');
}


$DB->query("
	SELECT ThreadID
	FROM forums_specific_rules
	WHERE ForumID = $ForumID");
$ThreadIDs = $DB->collect('ThreadID');

View::show_header('Edit Forum Rule', '', 'PageForumEditRule');
?>
<div class="Box">
    <div class="Box-body thin">
        <div class="BodyHeader">
            <h2 class="BodyHeader-nav">
                <a href="forums.php"><?= Lang::get('forums', 'forums') ?></a>
                &gt;
                <a href="forums.php?action=viewforum&amp;forumid=<?= $ForumID ?>"><?= $Forums[$ForumID]['Name'] ?></a>
                &gt;
                <?= Lang::get('forums', 'edit_forum_specific_rules') ?>
            </h2>
        </div>
        <table class="TableEditRule">
            <tr class="Table-rowHeader">
                <td class="Table-cell"><?= Lang::get('forums', 'thread_id') ?></td>
                <td class="Table-cell"></td>
            </tr>
            <tr>
                <form class="add_form" name="forum_rules" action="" method="post">
                    <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                    <td>
                        <input class="Input" type="text" name="new_thread" size="8" />
                    </td>
                    <td>
                        <input class="Button" type="submit" name="add" value="Add thread" />
                    </td>
                </form>
            </tr>
            <? foreach ($ThreadIDs as $ThreadID) { ?>
                <tr>
                    <td><?= $ThreadID ?></td>
                    <td>
                        <form class="delete_form" name="forum_rules" action="" method="post">
                            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
                            <input type="hidden" name="threadid" value="<?= $ThreadID ?>" />
                            <input class="Button" type="submit" name="del" value="Delete link" />
                        </form>
                    </td>
                </tr>
            <?  } ?>
        </table>
    </div>
</div>
<?
View::show_footer();
?>