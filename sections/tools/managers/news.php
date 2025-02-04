<?
enforce_login();
if (!check_perms('admin_manage_news')) {
    error(403);
}
$NewsCount = 5;
View::show_header('Manage news', 'bbcode,news_ajax');

switch ($_GET['action']) {
    case 'takeeditnews':
        if (!check_perms('admin_manage_news')) {
            error(403);
        }
        if (is_number($_POST['newsid'])) {
            authorize();

            $DB->query("
				UPDATE news
				SET Title = '" . db_string($_POST['title']) . "', Body = '" . db_string($_POST['body']) . "'
				WHERE ID = '" . db_string($_POST['newsid']) . "'");
            $Cache->delete_value('news');
            $Cache->delete_value('feed_news');
        }
        header('Location: index.php');
        break;
    case 'editnews':
        if (is_number($_GET['id'])) {
            $NewsID = $_GET['id'];
            $DB->query("
				SELECT Title, Body
				FROM news
				WHERE ID = $NewsID");
            list($Title, $Body) = $DB->next_record();
        }
}
?>
<div class="LayoutBody">
    <div class="BodyHeader">
        <h2 class="BodyHeader-nav"><?= ($_GET['action'] == 'news') ? 'Create a news post' : 'Edit news post'; ?></h2>
    </div>
    <form class="<?= ($_GET['action'] == 'news') ? 'create_form' : 'edit_form'; ?>" name="news_post" action="tools.php" method="post">
        <div class="BoxBody">
            <input type="hidden" name="action" value="<?= ($_GET['action'] == 'news') ? 'takenewnews' : 'takeeditnews'; ?>" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <? if ($_GET['action'] == 'editnews') { ?>
                <input type="hidden" name="newsid" value="<?= $NewsID; ?>" />
            <? } ?>
            <h3>Title</h3>
            <input class="Input" type="text" name="title" size="95" <? if (!empty($Title)) {
                                                                        echo ' value="' . display_str($Title) . '"';
                                                                    } ?> />
            <!-- Why did someone add this?  <input type="datetime" name="datetime" value="<?= sqltime() ?>" /> -->
            <br />
            <h3>Body</h3>
            <textarea class="Input" name="body" cols="95" rows="15"><? if (!empty($Body)) {
                                                                        echo display_str($Body);
                                                                    } ?></textarea> <br /><br />


            <div class="center">
                <input class="Button" type="submit" value="<?= ($_GET['action'] == 'news') ? 'Create news post' : 'Edit news post'; ?>" />
            </div>
        </div>
    </form>
    <? if ($_GET['action'] != 'editnews') { ?>
        <h2>News archive</h2>
        <?
        $DB->query('
	SELECT
		ID,
		Title,
		Body,
		Time
	FROM news
	ORDER BY Time DESC
	LIMIT ' . $NewsCount); // LIMIT 20
        $Count = 0;
        while (list($NewsID, $Title, $Body, $NewsTime) = $DB->next_record()) {
        ?>
            <div class="box vertical_space news_post">
                <div class="head">
                    <strong><?= display_str($Title) ?></strong> - posted <?= time_diff($NewsTime) ?>
                    - <a href="tools.php?action=editnews&amp;id=<?= $NewsID ?>" class="brackets">Edit</a>
                    <a href="tools.php?action=deletenews&amp;id=<?= $NewsID ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>" class="brackets">Delete</a>
                </div>
                <div class="pad"><?= Text::full_format($Body) ?></div>
            </div>
        <?
            if (++$Count > ($NewsCount - 1)) {
                break;
            }
        } ?>
        <div id="more_news">
            <div>
                <em><span><a href="#" onclick="news_ajax(event, 3, <?= $NewsCount ?>, 1, '<?= $LoggedUser['AuthKey'] ?>'); return false;">Click to load more news</a>.</span></em>
            </div>
        </div>
    <? } ?>
</div>
<? View::show_footer(); ?>