<?php
$settings = get_option( 'zwivel-toc-settings' );
var_dump($settings);
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.vivifyideas.com/
 * @since      1.0.0
 *
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<h3>Headings:</h3>
<p class="description">Select the heading to consider when generating the table of contents. Deselecting a heading will exclude it.</p>
<div>
    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="update_zwivel_toc_settings" />
        <?php for ($i = 1; $i < 7; $i++) : ?>
            <?php $checked = (!empty($settings[$i])) ? 'checked="checked"' : ''; ?>
            <input type="hidden" name="zwivel-toc-settings[heading_levels][<?= $i ?>]" value="0">
            <input name="zwivel-toc-settings[heading_levels][<?= $i ?>]" id="zwivel-toc-settings[heading_levels][<?= $i ?>]" type="checkbox" value="<?= $i ?>" <?= $checked ?>>
            <label for="zwivel-toc-settings[heading_levels][<?= $i ?>]">Heading <?= $i ?> (H<?= $i ?>)</label>
            <br>
        <?php
            endfor;
            submit_button();
        ?>
    </form>
</div>
