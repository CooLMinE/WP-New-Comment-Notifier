<?php

/*
  Plugin Name: WP New Comment Notifier
  Plugin URI:
  Description: A wordpress plugin that sends a notification to the emails of your choice when a new comment has been posted.
  Author: CooLMinE
  Author URI: http://www.fluxbytes.com/
  Version: 0.1
 */

defined('ABSPATH') or die();    // Disallow direct access to file.

include "wp-new-comment-notifier-options.php";
include "functions.php";

add_action('comment_post', 'wp_ncn_on_new_comment_posted');

function wp_ncn_on_new_comment_posted($comment_id)
{
    $comment = get_comment($comment_id);
    $subject = "New Comment by $comment->comment_author";
    $emails = trim(get_option('wp_new_comment_notifier_email_list'));
    $emailContent = wp_ncn_get_email_html_content($comment);

    if (wp_ncn_is_user_ignored($comment->comment_author) == true || wp_ncn_is_ip_ignored($comment->comment_author_IP) == true)
    {
        return;
    }

    if ($comment->comment_approved == "spam")
    {
        $subject.= " (SPAM)";
    }

    if (strpos($emails, '@') !== false)
    {
        foreach (explode(',', $emails) as $email)
        {
            mail($email, $subject, $emailContent, wp_ncn_get_email_headers());
        }
    }
}
?>