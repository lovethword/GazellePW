<?
if (!isset($_GET['id']) || !is_number($_GET['id'])) {
    error(404);
}
$ArticleID = (int)$_GET['id'];

$Latest = Wiki::get_article($ArticleID);
list($Revision, $Title, $Body, $Read, $Edit, $Date, $AuthorID, $AuthorName) = array_shift($Latest);
if ($Read > $LoggedUser['EffectiveClass']) {
    error(404);
}
if ($Edit > $LoggedUser['EffectiveClass']) {
    error(403);
}

View::show_header("Revisions of " . $Title, '', 'PageWikiRevision');
?>
<div class="LayoutBody">
    <div class="BodyHeader">
        <h2 class="BodyHeader-nav"><?= Lang::get('wiki', 'revision_history_before') ?><a href="wiki.php?action=article&amp;id=<?= $ArticleID ?>"><?= $Title ?></a><?= Lang::get('wiki', 'revision_history_after') ?></h2>
    </div>
    <form action="wiki.php" method="get">
        <input type="hidden" name="action" id="action" value="<? Lang::get('wiki', 'compare') ?>" />
        <input type="hidden" name="id" id="id" value="<?= $ArticleID ?>" />
        <div class="TableContainer">
            <table class="TableWikiRevision Table">
                <tr class="Table-rowHeader">
                    <td class="Table-cell"><?= Lang::get('wiki', 'history_revision') ?></td>
                    <td class="Table-cell"><?= Lang::get('wiki', 'history_title') ?></td>
                    <td class="Table-cell"><?= Lang::get('wiki', 'history_author') ?></td>
                    <td class="Table-cell"><?= Lang::get('wiki', 'history_age') ?></td>
                    <td class="Table-cell"><?= Lang::get('wiki', 'history_old') ?></td>
                    <td class="Table-cell"><?= Lang::get('wiki', 'history_new') ?></td>
                </tr>
                <tr class="Table-row">
                    <td class="Table-cell"><?= $Revision ?></td>
                    <td class="Table-cell"><?= $Title ?></td>
                    <td class="Table-cell"><?= Users::format_username($AuthorID, false, false, false) ?></td>
                    <td class="Table-cell"><?= time_diff($Date) ?></td>
                    <td class="Table-cell"><input type="radio" name="old" value="<?= $Revision ?>" disabled="disabled" /></td>
                    <td class="Table-cell"><input type="radio" name="new" value="<?= $Revision ?>" checked="checked" /></td>
                </tr>
                <?
                $DB->query("
	SELECT
		Revision,
		Title,
		Author,
		Date
	FROM wiki_revisions
	WHERE ID = '$ArticleID'
	ORDER BY Revision DESC");
                while (list($Revision, $Title, $AuthorID, $Date) = $DB->next_record()) { ?>
                    <tr class="Table-row">
                        <td class="Table-cell"><?= $Revision ?></td>
                        <td class="Table-cell"><?= $Title ?></td>
                        <td class="Table-cell"><?= Users::format_username($AuthorID, false, false, false) ?></td>
                        <td class="Table-cell"><?= time_diff($Date) ?></td>
                        <td class="Table-cell"><input type="radio" name="old" value="<?= $Revision ?>" /></td>
                        <td class="Table-cell"><input type="radio" name="new" value="<?= $Revision ?>" /></td>
                    </tr>
                <? } ?>
            </table>
        </div>
        <div class="center">
            <input class="Button" type="submit" value="Compare" />
        </div>
    </form>
</div>
<? View::show_footer(); ?>