<?php
defined('ABSPATH') or die();    // Disallow direct access to file.

add_action('admin_init', 'wp_new_comment_notifier_register_settings');
add_action('admin_menu', 'wp_new_comment_notifier_options_page_init');

function wp_new_comment_notifier_register_settings()
{
    register_setting('WP_New_Comment_Notifier_Admin_Options', 'wp_new_comment_notifier_email_list');
    register_setting('WP_New_Comment_Notifier_Admin_Options', 'wp_new_comment_notifier_username_ignore_list');
    register_setting('WP_New_Comment_Notifier_Admin_Options', 'wp_new_comment_notifier_ip_ignore_list');
}

function wp_new_comment_notifier_options_page_init()
{
    add_submenu_page("options-general.php", "WP New Comment Notifier", "WP New Comment Notifier", "manage_options", __FILE__, "wp_new_comment_notifier_options_page");
}

function wp_ncn_get_email_html_content($comment)
{
    $commentDate = get_comment_date(get_option('date_format') . ' \a\t ' . 'g:i:sa', $comment->comment_ID);
    $authorUrl = $comment->comment_author_url == "" ? "N/A" : $comment->comment_author_url;

    $emailContent = '<html>'
            . '<b>' . $comment->comment_author . ' (' . $comment->comment_author_IP . ')</b>' . '<br/>'
            . '<b>Date:</b> ' . $commentDate . '<br/>'
            . '<b>Email:</b> ' . $comment->comment_author_email . '<br/>'
            . '<b>Url:</b> ' . $authorUrl . '<br/>'
            . '<b>Posted at:</b> ' . get_permalink($comment->comment_post_ID) . '<br/><br/><br/>'
            . $comment->comment_content
            . '</html>';

    return $emailContent;
}

function wp_ncn_get_email_headers()
{
    $blog_name = get_bloginfo("name");
    $blog_admin_email = get_option("admin_email");
    $headers = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/html; charset=iso-8859-1";
    $headers[] = "From: " . $blog_name . " <" . $blog_admin_email . ">";
    $headers[] = "Reply-To: " . $blog_name . " <" . $blog_admin_email . ">";
    $headers[] = "Return-Path: " . $blog_name . " <" . $blog_admin_email . ">";
    $headers[] = "X-Mailer: PHP/" . phpversion();
    return implode("\r\n", $headers);
}

function wp_ncn_is_user_ignored($username)
{
    $ignorelist = explode(',', get_option("wp_new_comment_notifier_username_ignore_list"));

    foreach ($ignorelist as $listentry)
    {
        $entry = trim($listentry);

        if (strlen($entry) != 0 && strcasecmp($entry, $username) == 0)
        {
            return true;
        }
    }

    return false;
}

function wp_ncn_is_ip_ignored($ip)
{
    $ignorelist = explode(',', get_option("wp_new_comment_notifier_ip_ignore_list"));

    foreach ($ignorelist as $listentry)
    {
        $entry = trim($listentry);

        if (strlen($entry) != 0 && strcmp($entry, $ip) == 0)
        {
            return true;
        }
    }

    return false;
}

?>