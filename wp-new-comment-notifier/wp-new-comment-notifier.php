<?php
/*
  Plugin Name: WP New Comment Notifier
  Plugin URI:
  Description: A wordpress plugin that sends a notification to the emails of your choice when a new comment has been posted.
  Author: CooLMinE
  Author URI: http://www.fluxbytes.com/
  Version: 0.3
 */

// Disallow direct access to file.
defined('ABSPATH') or die();

Class WP_New_Comment_Notifier
{
    public static function register_settings()
    {
        register_setting('WP_New_Comment_Notifier_Admin_Options', 'wp_new_comment_notifier_email_list');
        register_setting('WP_New_Comment_Notifier_Admin_Options', 'wp_new_comment_notifier_username_ignore_list');
        register_setting('WP_New_Comment_Notifier_Admin_Options', 'wp_new_comment_notifier_ip_ignore_list');
    }

    public static function register_option_page()
    {
        add_submenu_page("options-general.php", "WP New Comment Notifier", "WP New Comment Notifier", "manage_options", __FILE__, array(__CLASS__, "show_option_page"));
    }

    public static function show_option_page()
    {
        include "wp-new-comment-notifier-options.php";
    }

    /*
     * Returns an HTML formatted string with all necessary details about the comment that was posted.
     */
    public static function get_email_html_content($comment)
    {
        $commentDate = get_comment_date(get_option('date_format') . ' \a\t ' . 'g:i:sa', $comment->comment_ID);
        $authorUrl = $comment->comment_author_url == "" ? "N/A" : $comment->comment_author_url;
        $authorEmail = $comment->comment_author_email;
        $commentUrl = get_permalink($comment->comment_post_ID);

        $emailContent = '<html>'
                . '<b>' . $comment->comment_author . ' (' . $comment->comment_author_IP . ')</b>' . '<br/>'
                . '<b>Date:</b> ' . $commentDate . '<br/>'
                . "<b>Email:</b>  <a href='mailto:{$authorEmail}'>{$authorEmail}</a>" . '<br/>'
                . '<b>Url:</b> ' . $authorUrl . '<br/>'
                . "<b>Posted at:</b> <a href='{$commentUrl}' target='_blank'>{$commentUrl}</a>" . '<br/><br/><br/>'
                . $comment->comment_content
                . '</html>';

        return $emailContent;
    }

    /*
     * Returns the headers that should be set before sending an email.
     */
    public static function get_email_headers()
    {
        $blog_name = get_bloginfo("name");
        $blog_admin_email = get_option("admin_email");
        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: " . $blog_name . " <" . $blog_admin_email . ">";
        $headers[] = "Reply-To: " . $blog_name . " <" . $blog_admin_email . ">";
        $headers[] = "Return-Path: " . $blog_name . " <" . $blog_admin_email . ">";
        $headers[] = "X-Mailer: PHP/" . phpversion();
        return implode("\r\n", $headers);
    }

    /*
     * Checks if the username is in the ignore list.
     * 
     * Returns true if the username is in the ignore list, otherwise returns false.
     */
    public static function is_user_ignored($username)
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

    /*
     * Checks if an IP is set to be ignored.
     * 
     * Returns true if the IP is in the ignore list, otherwise returns false.
     */
    public static function is_ip_ignored($ip)
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

    /*
     * Triggers when a new comment is posted.
     * 
     * The function checks if the user that posted or the user's IP in is the ignored list, 
     * if the user should not be ignored then the function proceeds to send an email to all the emails addresses 
     * that have been set in the plugin's options page. 
     */
    public static function on_new_comment_posted($comment_id)
    {
        $comment = get_comment($comment_id);
        $subject = "New Comment by $comment->comment_author";
        $emails = trim(get_option('wp_new_comment_notifier_email_list'));
        $emailContent = WP_New_Comment_Notifier::get_email_html_content($comment);

        if (WP_New_Comment_Notifier::is_user_ignored($comment->comment_author) == true || WP_New_Comment_Notifier::is_ip_ignored($comment->comment_author_IP) == true)
        {
            return;
        }

        if ($comment->comment_approved == "spam")
        {
            $subject.= " (SPAM)";
        }

        if (strpos($emails, '@') !== false)
        {
            $emailHeaders = WP_New_Comment_Notifier::get_email_headers();
            foreach (explode(',', $emails) as $email)
            {
                mail($email, $subject, $emailContent, $emailHeaders);
            }
        }
    }
}


add_action('admin_init', array('WP_New_Comment_Notifier', 'register_settings'));
add_action('admin_menu', array('WP_New_Comment_Notifier', 'register_option_page'));
add_action('comment_post', array('WP_New_Comment_Notifier', 'on_new_comment_posted'));
?>