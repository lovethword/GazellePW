<?
if (!check_perms('users_mod')) {
    error(403);
}
$Title = Lang::get('tools', 'unprocessed_bitcoin_donations');
View::show_header($Title, '', 'PageToolBitcoinUnproc');

// Find all donors
$AllDonations = DonationsBitcoin::get_received();

$DB->query("
	SELECT BitcoinAddress, SUM(Amount)
	FROM donations_bitcoin
	GROUP BY BitcoinAddress");
$OldDonations = G::$DB->to_pair(0, 1, false);
?>
<div class="LayoutBody">
    <div class="BodyHeader">
        <h2 class="BodyHeader-nav"><?= $Title ?></h2>
    </div>
    <div class="box2">
        <div class="pad"><?= Lang::get('tools', 'do_not_process_these_donations_manually') ?></div>
    </div>
    <?
    $NewDonations = array();
    $TotalUnproc = 0;
    foreach ($AllDonations as $Address => $Amount) {
        if (isset($OldDonations[$Address])) {
            if ($Amount == $OldDonations[$Address]) { // Direct comparison should be fine as everything comes from bitcoind
                continue;
            }
            $Debug->log_var(array('old' => $OldDonations[$Address], 'new' => $Amount), Lang::get('tools', 'new_donations_from_before') . "$Address" . Lang::get('tools', 'new_donations_from_after'));
            // PHP doesn't do fixed-point math, and json_decode has already botched the precision
            // so let's just round this off to satoshis and pray that we're on a 64 bit system
            $Amount = round($Amount - $OldDonations[$Address], 8);
        }
        $TotalUnproc += $Amount;
        $NewDonations[$Address] = $Amount;
    }
    ?>
    <table class="Table">
        <tr class="Table-rowHeader">
            <td class="Table-cell"><?= Lang::get('tools', 'bitcoin_address') ?></td>
            <td class="Table-cell"><?= Lang::get('tools', 'user') ?></td>
            <td class="Table-cell"><?= Lang::get('tools', 'unprocessed_amount_total') ?>: <?= $TotalUnproc ?: '0' ?>)</td>
            <td class="Table-cell"><?= Lang::get('tools', 'total_amount') ?></td>
            <td class="Table-cell"><?= Lang::get('tools', 'donor_rank') ?></td>
            <td class="Table-cell"><?= Lang::get('tools', 'special_rank') ?></td>
        </tr>
        <?
        if (!empty($NewDonations)) {
            foreach (DonationsBitcoin::get_userids(array_keys($NewDonations)) as $Address => $UserID) {
                $DonationEUR = Donations::currency_exchange($NewDonations[$Address], 'BTC');
        ?>
                <tr class="Table-row">
                    <td class="Table-cell"><?= $Address ?></td>
                    <td class="Table-cell"><?= Users::format_username($UserID, true, false, false) ?></td>
                    <td class="Table-cell"><?= $NewDonations[$Address] ?> (<?= "$DonationEUR EUR" ?>)</td>
                    <td class="Table-cell"><?= $AllDonations[$Address] ?></td>
                    <td class="Table-cell"><?= (int)Donations::get_rank($UserID) ?></td>
                    <td class="Table-cell"><?= (int)Donations::get_special_rank($UserID) ?></td>
                </tr>
            <?  }
        } else { ?>
            <tr class="Table-row">
                <td class="Table-cell Table-cellCenter" colspan="7"><?= Lang::get('tools', 'no_unprocessed_bitcoin_donations') ?></td>
            </tr>
        <? } ?>
    </table>
</div>
<?
View::show_footer();
