<?php
if (defined('WP_UNINSTALL_PLUGIN'))
{
    delete_option('wp_new_comment_notifier_email_list');
    delete_option('wp_new_comment_notifier_username_ignore_list');
    delete_option('wp_new_comment_notifier_ip_ignore_list');
}
?>