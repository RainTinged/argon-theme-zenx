<?php
//Ajax 发送评论
function ajax_post_comment(){
	$parentID = $_POST['comment_parent'];
	if (is_comment_private_mode($parentID)){
		if (!user_can_view_comment($parentID)){
			exit(json_encode(array(
				'status' => 'failed',
				'msg' =>  __('不能回复其他人的悄悄话评论', 'argon'),
				'isAdmin' => current_user_can('level_7')
			)));
		}
	}
	if (get_option('argon_comment_enable_qq_avatar') == 'true'){
		if (check_qqnumber($_POST['email'])){
			$_POST['qq'] = $_POST['email'];
			$_POST['email'] .= "@qq.com";
		}else{
			$_POST['qq'] = "";
		}
	}
	$comment = wp_handle_comment_submission(wp_unslash($_POST));
	if (is_wp_error($comment)){
		$msg = $comment -> get_error_data();
		if (!empty($msg)){
			$msg = $comment -> get_error_message();
		}
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => $msg,
			'isAdmin' => current_user_can('level_7')
		)));
	}
	$user = wp_get_current_user();
	do_action('set_comment_cookies', $comment, $user);
	if (isset($_POST['qq'])){
		if (!empty($_POST['qq']) && get_option('argon_comment_enable_qq_avatar') == 'true'){
			$_comment = $comment;
			$_comment -> comment_author_email = $_POST['qq'] . "@avatarqq.com";
			do_action('set_comment_cookies', $_comment, $user);
		}
	}
	$html = wp_list_comments(
		array(
			'type'      => 'comment',
			'callback'  => 'argon_comment_format',
			'echo'      => false
		),
		array($comment)
	);
	$newCaptchaSeed = get_comment_captcha_seed(true);
	$newCaptcha = get_comment_captcha($newCaptchaSeed);
	if (current_user_can('level_7')){
		$newCaptchaAnswer = get_comment_captcha_answer($newCaptchaSeed);
	}else{
		$newCaptchaAnswer = "";
	}
	exit(json_encode(array(
		'status' => 'success',
		'html' => $html,
		'id' => $comment -> comment_ID,
		'parentID' => $comment -> comment_parent,
		'commentOrder' => (get_option("comment_order") == "" ? "desc" : get_option("comment_order")),
		'newCaptchaSeed' => $newCaptchaSeed,
		'newCaptcha' => $newCaptcha,
		'newCaptchaAnswer' => $newCaptchaAnswer,
		'isAdmin' => current_user_can('level_7'),
		'isLogin' => is_user_logged_in()
	)));
}
add_action('wp_ajax_ajax_post_comment', 'ajax_post_comment');
add_action('wp_ajax_nopriv_ajax_post_comment', 'ajax_post_comment');

//评论 Markdown 解析
require_once(get_template_directory() . '/parsedown.php');
function comment_markdown_parse($comment_content){
	global $allowedtags;
	$allowedtags['pre'] = array('class' => array());
	$allowedtags['i'] = array('class' => array(), 'aria-hidden' => array());
	$allowedtags['img'] = array('src' => array(), 'alt' => array(), 'class' => array());
	$allowedtags['ol'] = array();
	$allowedtags['ul'] = array();
	$allowedtags['li'] = array();
	$allowedtags['a']['class'] = array();
	$allowedtags['a']['data-src'] = array();
	$allowedtags['a']['target'] = array();
	$allowedtags['h1'] = $allowedtags['h2'] = $allowedtags['h3'] = $allowedtags['h4'] = $allowedtags['h5'] = $allowedtags['h6'] = array();
	$parsedown = new _Parsedown();
	$res = $parsedown -> text($comment_content);
	$res = preg_replace(
		'/<a (.*?)>(.*?)<\/a>/',
		'<a $1 target="_blank">$2</a>',
		$res
	);
	return $res;
}
//评论发送处理
function post_comment_preprocessing($comment){
	$_POST['comment_content_source'] = $comment['comment_content'];
	if ($_POST['use_markdown'] == 'true' && get_option("argon_comment_allow_markdown") != "false"){
		$comment['comment_content'] = comment_markdown_parse($comment['comment_content']);
	}
	return $comment;
}
add_filter('preprocess_comment' , 'post_comment_preprocessing');
//发送评论通知邮件
function comment_mail_notify($comment){
	if (get_option("argon_comment_allow_mailnotice") != "true"){
		return;
	}
	if ($comment == null){
		return;
	}
	$id = $comment -> comment_ID;
	$commentPostID = $comment -> comment_post_ID;
	$commentAuthor = $comment -> comment_author;
	$parentID = $comment -> comment_parent;
	if ($parentID == 0){
		return;
	}
	$parentComment = get_comment($parentID);
	$parentEmail =  $parentComment -> comment_author_email;
	$parentName = $parentComment -> comment_author;
	$emailTo = "$parentName <$parentEmail>";
	if (get_comment_meta($parentID, "enable_mailnotice", true) == "true"){
		if (check_email_address($parentEmail)){
			$title = __("您在", 'argon') . " 「" . wp_trim_words(get_post_title_by_id($commentPostID), 20) . "」 " . __("的评论有了新的回复", 'argon');
			$fullTitle = __("您在", 'argon') . " 「" . get_post_title_by_id($commentPostID) . "」 " . __("的评论有了新的回复", 'argon');
			$content = htmlspecialchars(get_comment_meta($id, "comment_content_source", true));
			$link = get_permalink($commentPostID) . "#comment-" . $id;
			$unsubscribeLink = site_url("unsubscribe-comment-mailnotice?comment=" . $parentID . "&token=" . get_comment_meta($parentID, "mailnotice_unsubscribe_key", true));
			$html = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html charset=UTF-8" /></head><body>
				<div style="background: #fff;box-shadow: 0 15px 35px rgba(50,50,93,.1), 0 5px 15px rgba(0,0,0,.07);border-radius: 6px;margin: 15px auto 50px auto;padding: 35px 30px;max-width: min(calc(100% - 100px), 1200px);">
				<div style="font-size:30px;text-align:center;margin-bottom:15px;">' . htmlspecialchars($fullTitle)  .'</div>
				<div style="background: rgba(0, 0, 0, .15);height: 1px;width: 300px;margin: auto;margin-bottom: 35px;"></div>
				<div style="font-size: 18px;border-left: 4px solid rgba(0, 0, 0, .15);width: max-content;width: -moz-max-content;margin: auto;padding: 20px 30px;background: rgba(0,0,0,.08);border-radius: 6px;box-shadow: 0 2px 4px rgba(0,0,0,.075)!important;min-width: 60%;max-width: 90%;margin-bottom: 40px;">
				<div style="margin-bottom: 10px;"><strong><span style="color: #5e72e4;">@' . htmlspecialchars($commentAuthor) . '</span> ' . __('回复了你', "argon") . ':</strong></div>
				' . str_replace('\n', '<div></div>', $content) . '</div>
				<table width="100%" style="border-collapse:collapse;border:none;empty-cells:show;max-width:100%;box-sizing:border-box" cellspacing="0" cellpadding="0"><tbody style="box-sizing:border-box"><tr style="box-sizing:border-box" align="center"><td style="min-width:5px;box-sizing:border-box"><table style="border-collapse:collapse;border:none;empty-cells:show;max-width:100%;box-sizing:border-box" cellspacing="0" cellpadding="0"><tbody style="box-sizing:border-box"><tr style="box-sizing:border-box"><td style="box-sizing:border-box"><a href="' . $link . '" style="display: block; line-height: 1; color: #fff;background-color: #5e72e4;border-color: #5e72e4;box-shadow: 0 4px 6px rgba(50,50,93,.11), 0 1px 3px rgba(0,0,0,.08);padding: 15px 25px;font-size: 18px;border-radius: 4px;text-decoration: none; margin: 10px;">' . __('前往查看', "argon") . '</a></td></tr></tbody></table></td></tr></tbody></table>
				<table width="100%" style="border-collapse:collapse;border:none;empty-cells:show;max-width:100%;box-sizing:border-box" cellspacing="0" cellpadding="0"><tbody style="box-sizing:border-box"><tr style="box-sizing:border-box" align="center"><td style="min-width:5px;box-sizing:border-box"><table style="border-collapse:collapse;border:none;empty-cells:show;max-width:100%;box-sizing:border-box" cellspacing="0" cellpadding="0"><tbody style="box-sizing:border-box"><tr style="box-sizing:border-box"><td style="box-sizing:border-box"><a href="' . $unsubscribeLink . '" style="display: block; line-height: 1;color: #5e72e4;font-size: 16px;text-decoration: none; margin: 10px;">' . __('退订该评论的邮件提醒', "argon") . '</a></td></tr></tbody></table></td></tr></tbody></table>
				</div></body></html>';
			$html = apply_filters("argon_comment_mail_notification_content", $html); 
			send_mail($emailTo, $title, $html);
		}
	}
}
//评论发送完成添加 Meta
function post_comment_updatemetas($id){
	$parentID = $_POST['comment_parent'];
	$comment = get_comment($id);
	$commentPostID = $comment -> comment_post_ID;
	$commentAuthor = $comment -> comment_author;
	$mailnoticeUnsubscribeKey = get_random_token();
	update_comment_meta($id, "comment_content_source", $_POST['comment_content_source']);
	set_user_token_cookie();
	update_comment_meta($id, "user_token", $_COOKIE["argon_user_token"]);
	$editHistory = array(array(
		'content' => $_POST['comment_content_source'],
		'time' => time(),
		'isfirst' => true
	));
	update_comment_meta($id, "comment_edit_history", addslashes(json_encode($editHistory, JSON_UNESCAPED_UNICODE)));
	if ($_POST['use_markdown'] == 'true' && get_option("argon_comment_allow_markdown") != "false"){
		update_comment_meta($id, "use_markdown", "true");
	}else{
		update_comment_meta($id, "use_markdown", "false");
	}
	if ($_POST['private_mode'] == 'true' && get_option("argon_comment_allow_privatemode") == "true"){
		update_comment_meta($id, "private_mode", $_COOKIE["argon_user_token"]);
	}else{
		update_comment_meta($id, "private_mode", "false");
	}
	if (is_comment_private_mode($parentID)){
		update_comment_meta($id, "private_mode", get_comment_meta($parentID, "private_mode", true));
	}
	if ($parentID!= 0 && !is_comment_private_mode($parentID)){
		update_comment_meta($id, "private_mode", "false");
	}
	if ($_POST['enable_mailnotice'] == 'true' && get_option("argon_comment_allow_mailnotice") == "true"){
		update_comment_meta($id, "enable_mailnotice", "true");
		update_comment_meta($id, "mailnotice_unsubscribe_key", $mailnoticeUnsubscribeKey);
	}else{
		update_comment_meta($id, "enable_mailnotice", "false");
	}
	if ($comment -> comment_approved == 1){
		comment_mail_notify($comment);
	}
	if (get_option('argon_comment_enable_qq_avatar') == 'true'){
		if (!empty($_POST['qq'])){
			update_comment_meta($id, "qq_number", $_POST['qq']);
		}
	}
}
add_action('comment_post' , 'post_comment_updatemetas');
add_action('comment_unapproved_to_approved', 'comment_mail_notify');
add_rewrite_rule('^unsubscribe-comment-mailnotice/?(.*)$', '/wp-content/themes/argon/unsubscribe-comment-mailnotice.php$1', 'top');
//编辑评论
function user_edit_comment(){
	header('Content-Type:application/json; charset=utf-8');
	if (get_option("argon_comment_allow_editing") == "false"){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('博主关闭了编辑评论功能', 'argon')
		)));
	}
	$id = $_POST["id"];
	$content = $_POST["comment"];
	$contentSource = $content;
	if (!check_comment_token($id) && !check_login_user_same(get_comment_user_id_by_id($id))){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('您不是这条评论的作者或 Token 已过期', 'argon')
		)));
	}
	if ($_POST["comment"] == ""){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('新的评论为空', 'argon')
		)));
	}
	if (get_comment_meta($id, "use_markdown", true) == "true"){
		$content = comment_markdown_parse($content);
	}
	$res = wp_update_comment(array(
		'comment_ID' => $id,
		'comment_content' => $content
	));
	if ($res == 1){
		update_comment_meta($id, "comment_content_source", $contentSource);
		update_comment_meta($id, "edited", "true");
		$editHistory = json_decode(get_comment_meta($id, "comment_edit_history", true));
		if (is_null($editHistory)){
			$editHistory = array();
		}
		array_push($editHistory, array(
			'content' => htmlspecialchars(stripslashes($contentSource)),
			'time' => time(),
			'isfirst' => false
		));
		update_comment_meta($id, "comment_edit_history", addslashes(json_encode($editHistory, JSON_UNESCAPED_UNICODE)));
		exit(json_encode(array(
			'status' => 'success',
			'msg' => __('编辑评论成功', 'argon'),
			'new_comment' => apply_filters('comment_text', argon_get_comment_text($id), $id),
			'new_comment_source' => htmlspecialchars(stripslashes($contentSource)),
			'can_visit_edit_history' => can_visit_comment_edit_history($id)
		)));
	}else{
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('编辑评论失败，可能原因: 与原评论相同', 'argon'),
		)));
	}
}
add_action('wp_ajax_user_edit_comment', 'user_edit_comment');
add_action('wp_ajax_nopriv_user_edit_comment', 'user_edit_comment');
//置顶评论
function pin_comment(){
	header('Content-Type:application/json; charset=utf-8');
	if (get_option("argon_enable_comment_pinning") == "false"){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('博主关闭了评论置顶功能', 'argon')
		)));
	}
	if (!current_user_can("moderate_comments")){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('您没有权限进行此操作', 'argon')
		)));
	}
	$id = $_POST["id"];
	$newPinnedStat = $_POST["pinned"] == "true";
	$origPinnedStat = get_comment_meta($id, "pinned", true) == "true";
	if ($newPinnedStat == $origPinnedStat){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => $newPinnedStat ? __('评论已经是置顶状态', 'argon') : __('评论已经是取消置顶状态', 'argon')
		)));
	}
	if (get_comment($id) -> comment_parent != 0){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('不能置顶子评论', 'argon')
		)));
	}
	if (is_comment_private_mode($id)){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('不能置顶悄悄话', 'argon')
		)));
	}
	update_comment_meta($id, "pinned", $newPinnedStat ? "true" : "false");
	exit(json_encode(array(
		'status' => 'success',
		'msg' => $newPinnedStat ? __('置顶评论成功', 'argon') : __('取消置顶成功', 'argon'),
	)));
}
add_action('wp_ajax_pin_comment', 'pin_comment');
add_action('wp_ajax_nopriv_pin_comment', 'pin_comment');
//输出评论分页页码
function get_argon_formatted_comment_paginate_links($maxPageNumbers, $extraClasses = ''){
	$args = array(
		'prev_text' => '',
		'next_text' => '',
		'before_page_number' => '',
		'after_page_number' => '',
		'show_all' => True,
		'echo' => False
	);
	$res = paginate_comments_links($args);
	$res = preg_replace('/\'/', '"', $res);
	$res = preg_replace('/<a class="prev page-numbers" href="(.*?)">(.*?)<\/a>/', '', $res);
	$res = preg_replace('/<a class="next page-numbers" href="(.*?)">(.*?)<\/a>/', '', $res);
	preg_match_all('/<(.*?)>(.*?)<\/(.*?)>/' , $res , $pages);
	$total = count($pages[0]);
	$current = 0;
	$urls = array();
	for ($i = 0; $i < $total; $i++){
		if (preg_match('/<span(.*?)>(.*?)<\/span>/' , $pages[0][$i])){
			$current = $i + 1;
		}else{
			preg_match('/<a(.*?)href="(.*?)">(.*?)<\/a>/' , $pages[0][$i] , $tmp);
			$urls[$i + 1] = $tmp[2];
		}
	}
	if ($total == 0){
		return "";
	}
	$from = max($current - ($maxPageNumbers - 1) / 2 , 1);
	$to = min($current + $maxPageNumbers - ( $current - $from + 1 ) , $total);
	if ($to - $from + 1 < $maxPageNumbers){
		$to = min($current + ($maxPageNumbers - 1) / 2 , $total);
		$from = max($current - ( $maxPageNumbers - ( $to - $current + 1 ) ) , 1);
	}
	$html = "";
	if ($from > 1){
		$html .= '<li class="page-item"><div aria-label="First Page" class="page-link" href="' . $urls[1] . '"><i class="fa fa-angle-double-left" aria-hidden="true"></i></div></li>';
	}
	if ($current > 1){
		$html .= '<li class="page-item"><div aria-label="Previous Page" class="page-link" href="' . $urls[$current - 1] . '"><i class="fa fa-angle-left" aria-hidden="true"></i></div></li>';
	}
	for ($i = $from; $i <= $to; $i++){
		if ($current == $i){
			$html .= '<li class="page-item active"><span class="page-link" style="cursor: default;">' . $i . '</span></li>';
		}else{
			$html .= '<li class="page-item"><div class="page-link" href="' . $urls[$i] . '">' . $i . '</div></li>';
		}
	}
	if ($current < $total){
		$html .= '<li class="page-item"><div aria-label="Next Page" class="page-link" href="' . $urls[$current + 1] . '"><i class="fa fa-angle-right" aria-hidden="true"></i></div></li>';
	}
	if ($to < $total){
		$html .= '<li class="page-item"><div aria-label="Last Page" class="page-link" href="' . $urls[$total] . '"><i class="fa fa-angle-double-right" aria-hidden="true"></i></div></li>';
	}
	return '<nav id="comments_navigation" class="comments-navigation"><ul class="pagination' . $extraClasses . '">' . $html . '</ul></nav>';
}
function get_argon_formatted_comment_paginate_links_for_all_platforms(){
	return get_argon_formatted_comment_paginate_links(7) . get_argon_formatted_comment_paginate_links(5, " pagination-mobile");
}
function get_argon_comment_paginate_links_prev_url(){
	$args = array(
		'prev_text' => '',
		'next_text' => '',
		'before_page_number' => '',
		'after_page_number' => '',
		'show_all' => True,
		'echo' => False
	);
	$str = paginate_comments_links($args);
	$str = preg_replace('/\'/', '"', $str);
	$url = "";
	preg_match('/<a class="prev page-numbers" href="(.*?)">(.*?)<\/a>/', $str, $url);
	if (!isset($url[1])){
		return NULL;
	}
	if (isset($_GET['fill_first_page']) || strpos(parse_url($_SERVER['REQUEST_URI'])['path'], 'comment-page-') === false){
		$parsed_url = parse_url($url[1]);
		if (!isset($parsed_url['query'])){
			$parsed_url['query'] = 'fill_first_page=true';
		}else
			if (strpos($parsed_url['query'], 'fill_first_page=true') === false){
			$parsed_url['query'] .= '&fill_first_page=true';
		}
		return $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'] . '?' . $parsed_url['query'];
	}
	return $url[1];
}
//评论重排序（置顶优先）
$GLOBALS['comment_order'] = get_option('comment_order');
function argon_comment_cmp($a, $b){
	$a_pinned = get_comment_meta($a -> comment_ID, 'pinned', true);
	$b_pinned = get_comment_meta($b -> comment_ID, 'pinned', true);
	if ($a_pinned != "true"){
		$a_pinned = "false";
	}
	if ($b_pinned != "true"){
		$b_pinned = "false";
	}
	if ($a_pinned == $b_pinned){
		return ($a -> comment_date_gmt) > ($b -> comment_date_gmt);
	}else{
		if ($a_pinned == "true"){
			return ($GLOBALS['comment_order'] == 'desc');
		}else{
			return ($GLOBALS['comment_order'] != 'desc');
		}
	}
}
function argon_get_comments(){
	global $wp_query;
	$args = array(
		'post__in'		 => array(get_the_ID()),
		'type'           => 'comment',
		'order'          => 'DESC',
		'orderby'        => 'comment_date_gmt',
		'status'         => 'approve'
	);
	if (is_user_logged_in()){
		$args['include_unapproved'] = array(get_current_user_id());
	} else {
		$unapproved_email = wp_get_unapproved_comment_author_email();
		if ($unapproved_email) {
			$args['include_unapproved'] = array($unapproved_email);
		}
	}
	$comment_query = new WP_Comment_Query;
	$comments = $comment_query -> query($args);
	if (get_option("argon_enable_comment_pinning", "false") == "true"){
		usort($comments, "argon_comment_cmp");
	}else{
		$comments = array_reverse($comments);
	}
	if (get_option("argon_comment_pagination_type", "feed") == "page"){
		return $comments;
	}
	if (!isset($_GET['fill_first_page']) && strpos(parse_url($_SERVER['REQUEST_URI'])['path'], 'comment-page-') !== false){
		return null;
	}
	$comments_per_page = get_option('comments_per_page');
	$comments_count = 0; 
	foreach ($comments as $comment){
		if ($comment -> comment_parent == 0){
			$comments_count++;
		}
	}
	$comments_pages = ceil($comments_count / $comments_per_page);
	if ($comments_pages > 1){
		$placeholders_count = $comments_pages * $comments_per_page - $comments_count;
		while ($placeholders_count--){
			array_unshift($comments, new WP_Comment((object) array(
				"placeholder" => true
			)));
		}
	}
	return $comments;
}
