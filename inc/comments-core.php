<?php
//解析 UA 和相应图标
require_once(get_template_directory() . '/useragent-parser.php');
$argon_comment_ua = get_option("argon_comment_ua");
$argon_comment_show_ua = Array();
if (strpos($argon_comment_ua, 'platform') !== false){
	$argon_comment_show_ua['platform'] = true;
}
if (strpos($argon_comment_ua, 'browser') !== false){
	$argon_comment_show_ua['browser'] = true;
}
if (strpos($argon_comment_ua, 'version') !== false){
	$argon_comment_show_ua['version'] = true;
}
function parse_ua_and_icon($userAgent){
	global $argon_comment_ua;
	global $argon_comment_show_ua;
	if ($argon_comment_ua == "" || $argon_comment_ua == "hidden"){
		return "";
	}
	$parsed = argon_parse_user_agent($userAgent);
	$out = "<div class='comment-useragent'>";
	if (isset($argon_comment_show_ua['platform']) && $argon_comment_show_ua['platform'] == true){
		if (isset($GLOBALS['UA_ICON'][$parsed['platform']])){
			$out .= $GLOBALS['UA_ICON'][$parsed['platform']] . " ";
		}else{
			$out .= $GLOBALS['UA_ICON']['Unknown'] . " ";
		}
		$out .= $parsed['platform'];
	}
	if (isset($argon_comment_show_ua['browser']) && $argon_comment_show_ua['browser'] == true){
		if (isset($GLOBALS['UA_ICON'][$parsed['browser']])){
			$out .= " " . $GLOBALS['UA_ICON'][$parsed['browser']];
		}else{
			$out .= " " . $GLOBALS['UA_ICON']['Unknown'];
		}
		$out .= " " . $parsed['browser'];
		if (isset($argon_comment_show_ua['version']) && $argon_comment_show_ua['version'] == true){
			$out .= " " . $parsed['version'];
		}
	}
	$out .= "</div>";
	return apply_filters("argon_comment_ua_icon", $out);
}
//发送邮件
function send_mail($to, $subject, $content){
	wp_mail($to, $subject, $content, array('Content-Type: text/html; charset=UTF-8'));
}
function check_email_address($email){
	return (bool) preg_match( "/^\w+((-\w+)|(\.\w+))*@[A-Za-z0-9]+(([.\-])[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/", $email );
}
//检验评论 Token 和用户 Token 是否一致
function check_comment_token($id){
	if (strlen($_COOKIE['argon_user_token']) != 32){
		return false;
	}
	if ($_COOKIE['argon_user_token'] != get_comment_meta($id, "user_token", true)){
		return false;
	}
	return true;
}
//检验评论发送者 ID 和当前登录用户 ID 是否一致
function check_login_user_same($userid){
	if ($userid == 0){
		return false;
	}
	if ($userid != (wp_get_current_user() -> ID)){
		return false;
	}
	return true;
}
function get_comment_user_id_by_id($comment_ID){
	$comment = get_comment($comment_ID);
	return $comment -> user_id;
}
function check_comment_userid($id){
	if (!check_login_user_same(get_comment_user_id_by_id($id))){
		return false;
	}
	return true;
}
//悄悄话
function is_comment_private_mode($id){
	if (strlen(get_comment_meta($id, "private_mode", true)) != 32){
		return false;
	}
	return true;
}
function user_can_view_comment($id){
	if (!is_comment_private_mode($id)){
		return true;
	}
	if (current_user_can("manage_options")){
		return true;
	}
	if ($_COOKIE['argon_user_token'] == get_comment_meta($id, "private_mode", true)){
		return true;
	}
	return false;
}
//过滤 RSS 中悄悄话
function remove_rss_private_comment_title_and_author($str){
	global $comment;
	if (isset($comment -> comment_ID) && is_comment_private_mode($comment -> comment_ID)){
		return "***";
	}
	return $str;
}
add_filter('the_title_rss' , 'remove_rss_private_comment_title_and_author');
add_filter('comment_author_rss' , 'remove_rss_private_comment_title_and_author');
function remove_rss_private_comment_content($str){
	global $comment;
	if (is_comment_private_mode($comment -> comment_ID)){
		$comment -> comment_content = __('该评论为悄悄话', 'argon');
		return $comment -> comment_content;
	}
	return $str;
}
add_filter('comment_text_rss' , 'remove_rss_private_comment_content');
//评论回复信息
function get_comment_parent_info($comment){
	if (!$GLOBALS['argon_comment_options']['show_comment_parent_info']){
		return "";
	}
	if ($comment -> comment_parent == 0){
		return "";
	}
	$parent_comment = get_comment($comment -> comment_parent);
	return '<div class="comment-parent-info" data-parent-id=' . $parent_comment -> comment_ID . '><i class="fa fa-reply" aria-hidden="true"></i> ' . get_comment_author($parent_comment -> comment_ID) . '</div>';
}
//是否可以查看评论编辑记录
function can_visit_comment_edit_history($id){
	$who_can_visit_comment_edit_history = get_option("argon_who_can_visit_comment_edit_history");
	if ($who_can_visit_comment_edit_history == ""){
		$who_can_visit_comment_edit_history = "admin";
	}
	switch ($who_can_visit_comment_edit_history) {
		case 'everyone':
			return true;
		case 'commentsender':
			if (check_comment_token($id) || check_comment_userid($id)){
				return true;
			}
			return false;
		default:
			if (current_user_can("moderate_comments")){
				return true;
			}
			return false;
	}
}
//获取评论编辑记录
function get_comment_edit_history(){
	$id = $_POST['id'];
	if (!can_visit_comment_edit_history($id)){
		exit(json_encode(array(
			'id' => $_POST['id'],
			'history' => ""
		)));
	}
	$editHistory = json_decode(get_comment_meta($id, "comment_edit_history", true));
	$editHistory = array_reverse($editHistory);
	$res = "";
	$position = count($editHistory) + 1;
	date_default_timezone_set(get_option('timezone_string'));
	foreach ($editHistory as $edition){
		$position -= 1;
		$res .= "<div class='comment-edit-history-item'>
					<div class='comment-edit-history-title'>
						<div class='comment-edit-history-id'>
							#" . $position . "
						</div>
						" . ($edition -> isfirst ? "<span class='badge badge-primary badge-admin'>" . __("最初版本", 'argon') . "</span>" : "") . "
					</div>
					<div class='comment-edit-history-time'>" . date('Y-m-d H:i:s', $edition -> time) . "</div>
					<div class='comment-edit-history-content'>" . str_replace("\n", "</br>", $edition -> content) . "</div>
				</div>";
	}
	exit(json_encode(array(
		'id' => $_POST['id'],
		'history' => $res
	)));
}
add_action('wp_ajax_get_comment_edit_history', 'get_comment_edit_history');
add_action('wp_ajax_nopriv_get_comment_edit_history', 'get_comment_edit_history');
//是否可以置顶/取消置顶
function is_comment_pinable($id){
	if (get_comment($id) -> comment_approved != "1"){
		return false;
	}
	if (get_comment($id) -> comment_parent != 0){
		return false;
	}
	if (is_comment_private_mode($id)){
		return false;
	}
	return true;
}
//评论内容格式化
function argon_get_comment_text($comment_ID = 0, $args = array()) {
	$comment = get_comment($comment_ID);
	$comment_text = get_comment_text($comment, $args);
	$enableMarkdown = get_comment_meta(get_comment_ID(), "use_markdown", true);
	//图片
	$comment_text = preg_replace(
		'/<a data-src="(.*?)" title="(.*?)" class="comment-image"(.*?)>([\w\W]*)<\/a>/',
		'<img src="$1" alt="$2" />',
		$comment_text
	);
	$comment_text = preg_replace(
		'/<img src="(.*?)" alt="(.*?)" \/>/',
		'<a href="$1" title="$2" data-fancybox="comment-' . $comment -> comment_ID . '-image" class="comment-image" rel="nofollow">
			<i class="fa fa-image" aria-hidden="true"></i>
			' . __('查看图片', 'argon') . '
			<img src="" alt="$2" class="comment-image-preview">
			<i class="comment-image-preview-mask"></i>
		</a>',
		$comment_text
	);
	//表情
	if (get_option("argon_comment_emotion_keyboard", "true") != "false"){
		global $emotionListDefault;
		$emotionList = apply_filters("argon_emotion_list", $emotionListDefault);
		foreach ($emotionList as $groupIndex => $group){ 
			foreach ($group['list'] as $index => $emotion){
				if ($emotion['type'] != 'sticker'){
					continue;
				}
				if (!isset($emotion['code']) || mb_strlen($emotion['code']) == 0){
					continue;
				}
				if (!isset($emotion['src']) || mb_strlen($emotion['src']) == 0){
					continue;
				}
				$comment_text = str_replace(':' . $emotion['code'] . ':', "<img class='comment-sticker lazyload' src='data:image/svg+xml;base64,PHN2ZyBjbGFzcz0iZW1vdGlvbi1sb2FkaW5nIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9Im5vbmUiIHZpZXdCb3g9Ii04IC04IDQwIDQwIiBzdHJva2U9IiM4ODgiIG9wYWNpdHk9Ii41IiB3aWR0aD0iNjAiIGhlaWdodD0iNjAiPgogIDxwYXRoIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgc3Ryb2tlLXdpZHRoPSIxLjUiIGQ9Ik0xNC44MjggMTQuODI4YTQgNCAwIDAxLTUuNjU2IDBNOSAxMGguMDFNMTUgMTBoLjAxTTIxIDEyYTkgOSAwIDExLTE4IDAgOSA5IDAgMDExOCAweiIvPgo8L3N2Zz4=' data-original='" . $emotion['src'] . "'/><noscript><img class='comment-sticker' src='" . $emotion['src'] . "'/></noscript>", $comment_text);
			}
		}
	}
	return apply_filters( 'comment_text', $comment_text, $comment, $args );
}
//评论点赞
function get_comment_upvotes($id) {
	$comment = get_comment($id);
	if ($comment == null){
		return 0;
	}
	$upvotes = get_comment_meta($comment -> comment_ID, "upvotes", true);
	if ($upvotes == null) {
		$upvotes = 0;
	}
	return $upvotes;
}
function set_comment_upvotes($id){
	$comment = get_comment($id);
	if ($comment == null){
		return 0;
	}
	$upvotes = get_comment_meta($comment -> comment_ID, "upvotes", true);
	if ($upvotes == null) {
		$upvotes = 0;
	}
	$upvotes++;
	update_comment_meta($comment -> comment_ID, "upvotes", $upvotes);
	return $upvotes;
}
function is_comment_upvoted($id){
	$upvotedList = isset( $_COOKIE['argon_comment_upvoted'] ) ? $_COOKIE['argon_comment_upvoted'] : '';
	if (in_array($id, explode(',', $upvotedList))){
		return true;
	}
	return false;
}
function upvote_comment(){
	if (get_option("argon_enable_comment_upvote", "false") != "true"){
		return;
	}
	header('Content-Type:application/json; charset=utf-8');
	$ID = $_POST["comment_id"];
	$comment = get_comment($ID);
	if ($comment == null){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('评论不存在', 'argon'),
			'total_upvote' => 0
		)));
	}
	$upvotedList = isset( $_COOKIE['argon_comment_upvoted'] ) ? $_COOKIE['argon_comment_upvoted'] : '';
	if (in_array($ID, explode(',', $upvotedList))){
		exit(json_encode(array(
			'status' => 'failed',
			'msg' => __('该评论已被赞过', 'argon'),
			'total_upvote' => get_comment_upvotes($ID)
		)));
	}
	set_comment_upvotes($ID);
	setcookie('argon_comment_upvoted', $upvotedList . $ID . "," , time() + 3153600000 , '/');
	exit(json_encode(array(
		'ID' => $ID,
		'status' => 'success',
		'msg' => __('点赞成功', 'argon'),
		'total_upvote' => format_number_in_kilos(get_comment_upvotes($ID))
	)));
}
add_action('wp_ajax_upvote_comment' , 'upvote_comment');
add_action('wp_ajax_nopriv_upvote_comment' , 'upvote_comment');
//评论样式格式化
$GLOBALS['argon_comment_options']['enable_upvote'] = (get_option("argon_enable_comment_upvote", "false") == "true");
$GLOBALS['argon_comment_options']['enable_pinning'] = (get_option("argon_enable_comment_pinning", "false") == "true");
$GLOBALS['argon_comment_options']['current_user_can_moderate_comments'] = current_user_can('moderate_comments');
$GLOBALS['argon_comment_options']['show_comment_parent_info'] = (get_option("argon_show_comment_parent_info", "true") == "true");
function argon_comment_format($comment, $args, $depth){
	global $comment_enable_upvote, $comment_enable_pinning;
	$GLOBALS['comment'] = $comment;
	if (!($comment -> placeholder) && user_can_view_comment(get_comment_ID())){
	?>
	<li class="comment-item" id="comment-<?php comment_ID(); ?>" data-id="<?php comment_ID(); ?>" data-use-markdown="<?php echo get_comment_meta(get_comment_ID(), "use_markdown", true);?>">
		<div class="comment-item-left-wrapper">
			<div class="comment-item-avatar">
				<?php if(function_exists('get_avatar') && get_option('show_avatars')){
					echo get_avatar($comment, 40);
				}?>
			</div>
			<?php if ($GLOBALS['argon_comment_options']['enable_upvote']){ ?>
				<button class="comment-upvote btn btn-icon btn-outline-primary btn-sm <?php echo (is_comment_upvoted(get_comment_ID()) ? 'upvoted' : ''); ?>" type="button" data-id="<?php comment_ID(); ?>">
					<span class="btn-inner--icon"><i class="fa fa-caret-up"></i></span>
					<span class="btn-inner--text">
						<span class="comment-upvote-num"><?php echo format_number_in_kilos(get_comment_upvotes(get_comment_ID())); ?></span>
					</span>
				</button>
			<?php } ?>
		</div>
		<div class="comment-item-inner" id="comment-inner-<?php comment_ID();?>">
			<div class="comment-item-title">
				<div class="comment-name">
					<div class="comment-author"><?php echo get_comment_author_link();?></div>
					<?php if (user_can($comment -> user_id , "update_core")){
						echo '<span class="badge badge-primary badge-admin">' . __('博主', 'argon') . '</span>';}
					?>
					<?php echo get_comment_parent_info($comment); ?>
					<?php if ($GLOBALS['argon_comment_options']['enable_pinning'] && get_comment_meta(get_comment_ID(), "pinned", true) == "true"){
						echo '<span class="badge badge-danger badge-pinned"><i class="fa fa-thumb-tack" aria-hidden="true"></i> ' . _x('置顶', 'pinned', 'argon') . '</span>';
					}?>
					<?php if (is_comment_private_mode(get_comment_ID()) && user_can_view_comment(get_comment_ID())){
						echo '<span class="badge badge-success badge-private-comment">' . __('悄悄话', 'argon') . '</span>';}
					?>
					<?php if ($comment -> comment_approved == 0){
						echo '<span class="badge badge-warning badge-unapproved">' . __('待审核', 'argon') . '</span>';}
					?>
					<?php
						echo parse_ua_and_icon($comment -> comment_agent);
					?>
				</div>
				<div class="comment-info">
					<?php if (get_comment_meta(get_comment_ID(), "edited", true) == "true") { ?>
						<div class="comment-edited<?php if (can_visit_comment_edit_history(get_comment_ID())){echo ' comment-edithistory-accessible';}?>">
							<i class="fa fa-pencil" aria-hidden="true"></i><?php _e('已编辑', 'argon')?>
						</div>
					<?php } ?>
					<div class="comment-time">
						<span class="human-time" data-time="<?php echo get_comment_time('U', true);?>"><?php echo human_time_diff(get_comment_time('U') , current_time('timestamp')) . __("前", "argon");?></span>
						<div class="comment-time-details"><?php echo get_comment_time('Y-n-d G:i:s');?></div>
					</div>
				</div>
			</div>
			<div class="comment-item-text">
				<?php echo argon_get_comment_text();?>
			</div>
			<div class="comment-item-source" style="display: none;" aria-hidden="true"><?php echo htmlspecialchars(get_comment_meta(get_comment_ID(), "comment_content_source", true));?></div>
			<div class="comment-operations">
				<?php if ($GLOBALS['argon_comment_options']['enable_pinning'] && $GLOBALS['argon_comment_options']['current_user_can_moderate_comments'] && is_comment_pinable(get_comment_ID())) {
					if (get_comment_meta(get_comment_ID(), "pinned", true) == "true") { ?>
						<button class="comment-unpin btn btn-sm btn-outline-primary" data-id="<?php comment_ID(); ?>" type="button" style="margin-right: 2px;"><?php _e('取消置顶', 'argon')?></button>
					<?php } else { ?>
						<button class="comment-pin btn btn-sm btn-outline-primary" data-id="<?php comment_ID(); ?>" type="button" style="margin-right: 2px;"><?php _ex('置顶', 'to pin', 'argon')?></button>
				<?php }
					} ?>
				<?php if ((check_comment_token(get_comment_ID()) || check_login_user_same($comment -> user_id)) && (get_option("argon_comment_allow_editing") != "false")) { ?>
					<button class="comment-edit btn btn-sm btn-outline-primary" data-id="<?php comment_ID(); ?>" type="button" style="margin-right: 2px;"><?php _e('编辑', 'argon')?></button>
				<?php } ?>
				<button class="comment-reply btn btn-sm btn-outline-primary" data-id="<?php comment_ID(); ?>" type="button"><?php _e('回复', 'argon')?></button>
			</div>
		</div>
	</li>
	<li class="comment-divider"></li>
	<li>
<?php }}
//评论样式格式化 (说说预览界面)
function argon_comment_shuoshuo_preview_format($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;?>
	<li class="comment-item" id="comment-<?php comment_ID(); ?>">
		<div class="comment-item-inner " id="comment-inner-<?php comment_ID();?>">
			<span class="shuoshuo-comment-item-title">
				<?php echo get_comment_author_link();?>
				<?php if( user_can($comment -> user_id , "update_core") ){
					echo '<span class="badge badge-primary badge-admin">' . __('博主', 'argon') . '</span>';}
				?>
				<?php if( $comment -> comment_approved == 0 ){
					echo '<span class="badge badge-warning badge-unapproved">' . __('待审核', 'argon') . '</span>';}
				?>
				:
			</span>
			<span class="shuoshuo-comment-item-text">
				<?php echo strip_tags(get_comment_text());?>
			</span>
		</div>
	</li>
	<li>
<?php }
function comment_author_link_filter($html){
	return str_replace('href=', 'target="_blank" href=', $html);
}
add_filter('get_comment_author_link', 'comment_author_link_filter');
//QQ Avatar 获取
function get_avatar_by_qqnumber($avatar){
	global $comment;
	if (!isset($comment) || !isset($comment -> comment_ID)){
		return $avatar;
	}
	$qqnumber = get_comment_meta($comment -> comment_ID, 'qq_number', true);
	if (!empty($qqnumber)){
		preg_match_all('/width=\'(.*?)\'/', $avatar, $preg_res);
		$size = $preg_res[1][0];
		return "<img src='https://q1.qlogo.cn/g?b=qq&s=640&nk=" . $qqnumber ."' class='avatar avatar-" . $size . " photo' width='" . $size . "' height='" . $size . "'>";
	}
	return $avatar;
}
add_filter('get_avatar', 'get_avatar_by_qqnumber');
//判断 QQ 号合法性
if (!function_exists('check_qqnumber')){
	function check_qqnumber($qqnumber){
		if (preg_match("/^[1-9][0-9]{4,10}$/", $qqnumber)){
			return true;
		} else {
			return false;
		}
	}
}
