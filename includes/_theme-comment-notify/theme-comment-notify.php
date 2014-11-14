<?php

// theme_comment_notify::init();
class theme_comment_notify {
	private static $iden = 'theme-comment-notify';

	public static function init(){
		
		add_action('comment_post',get_class() . '::reply_notify');
		add_action('comment_unapproved_to_approved', get_class() . '::approved_notify');

	}
	public static function approved_notify($comment){
		
		if(!is_email($comment->comment_author_email)) return false;
		
		$to = $comment->comment_author_email;
		
		$post_title = get_the_title($comment->comment_post_ID);
		
		$mail_title = sprintf(___('[%s] Your comment has been approved in "%s".'),get_bloginfo('name'),$post_title);
		
		$mail_content = '
			<p>' . sprintf(___('Your comment (%s) has been approved.'),esc_html($comment->comment_content)) . '</p>
			<p><a target="_blank" href="' . esc_url(get_comment_link($comment->comment_ID)) . '">' . sprintf(___('View it on "%s".'),esc_html($post_title)) . '</a></p>
		';
		
		add_filter('wp_mail_content_type',get_class() . '::set_html_content_type');
		
		wp_mail($to,$mail_title,$mail_content);
		
		remove_filter('wp_mail_content_type',get_class() . '::set_html_content_type');

	}
	public static function reply_notify($comment_id){
		/** 
		 * get current comment for parent comment
		 */
		$c_comment = get_comment($comment_id);
		
		if($c_comment->comment_parent == 0 && $c_comment->comment_approved != 1) return false;
			
		$parent_id = $c_comment->comment_parent;

		/** 
		 * send start
		 */
		self::send_email($parent_id,$c_comment);
		
	}
	private static function send_email($comment_id,$child_comment){
		
		$comment = get_comment($comment_id);
		/** 
		 * compare email
		 */
		$to = $comment->comment_author_email;
		if(!is_email($to) || $to == $child_comment->comment_author_email) return false;
		
		$post_id = $comment->comment_post_ID;
		$post_title = get_the_title($post_id);
		
		$mail_title = sprintf(___('[%s] Your comment has a reply in "%s".'),esc_html(get_bloginfo('name')),esc_html($post_title));
		$mail_content = '
			<p>' . sprintf(___('Your comment (%s) about "%s" has a reply.'),$child_comment->comment_content,$post_title) . '</p>
			<p>' . $child_comment->comment_content . '</p>
			<p><a target="_blank" href="' . esc_url(get_comment_link($comment->comment_ID)) . '">' . sprintf(___('View it on "%s".'),esc_html($post_title)) . '</a></p>
			
		';
		
		add_filter('wp_mail_content_type',get_class() . '::set_html_content_type');
		
		wp_mail($to,$mail_title,$mail_content);
		
		remove_filter('wp_mail_content_type',get_class() . '::set_html_content_type');

	}
	public static function set_html_content_type(){
		return 'text/html';
	}
}

?>
