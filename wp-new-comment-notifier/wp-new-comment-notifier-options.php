<?php
defined('ABSPATH') or die();    // Disallow direct access to file.

function wp_new_comment_notifier_options_page()
{
    ?>
    <div class="wrap">
        <h2>WP New Comment Notifier Options</h2>
        <form method="post" action="options.php">
    <?php settings_fields('WP_New_Comment_Notifier_Admin_Options'); ?>
            <label><b>Email List</b> <small><i>(Seperate emails by commas)</i></small></label><br>
            <textarea style='width:50%; resize:none; margin-bottom: 30px;' rows="5" wrap="off" cols='1' name="wp_new_comment_notifier_email_list"><?php echo get_option('wp_new_comment_notifier_email_list', get_option("admin_email")); ?></textarea><br>

            <label><b>Ignore Usernames</b> <small><i>(Seperate usernames by commas)</i></small></label><br>
            <textarea style='width:50%; resize:none; margin-bottom: 30px;' rows="5" wrap="off" cols='1' name="wp_new_comment_notifier_username_ignore_list"><?php echo get_option('wp_new_comment_notifier_username_ignore_list', ""); ?></textarea><br>

            <label><b>Ignore IP Addresses</b> <small><i>(Seperate IP addresses by commas)</i></small></label><br>
            <textarea style='width:50%; resize:none;' rows="5" wrap="off" cols='1' name="wp_new_comment_notifier_ip_ignore_list"><?php echo get_option('wp_new_comment_notifier_ip_ignore_list', ""); ?></textarea><br>
    <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
?>