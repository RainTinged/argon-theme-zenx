<?php
//访问者 Token & Session
function get_random_token(){
	return md5(uniqid(microtime(true), true));
}
function set_user_token_cookie(){
	if (!isset($_COOKIE["argon_user_token"]) || strlen($_COOKIE["argon_user_token"]) != 32){
		$newToken = get_random_token();
		setcookie("argon_user_token", $newToken, time() + 10 * 365 * 24 * 60 * 60, "/");
		$_COOKIE["argon_user_token"] = $newToken;
	}
}
function session_init(){
	set_user_token_cookie();
	if (!session_id()){
		session_start();
	}
}
session_init();
//add_action('init', 'session_init');

//页面 Description Meta
function get_seo_description(){
	global $post;
	if (is_single() || is_page()){
		if (get_the_excerpt() != ""){
			return preg_replace('/ \[&hellip;]$/', '&hellip;', get_the_excerpt());
		}
		if (!post_password_required()){
			return htmlspecialchars(mb_substr(str_replace("\n", '', strip_tags($post -> post_content)), 0, 50)) . "...";
		}else{
			return __("这是一个加密页面，需要密码来查看", 'argon');
		}
	}else{
		return get_option('argon_seo_description');
	}
}
//页面 Keywords
function get_seo_keywords(){
	if (is_single()){
		global $post;
		$tags = get_the_tags('', ',', '', $post -> ID);
		if ($tags != null){
			$res = "";
			foreach ($tags as $tag){
				if ($res != ""){
					$res .= ",";
				}
				$res .= $tag -> name;
			}
			return $res;
		}
	}
	if (is_category()){
		return single_cat_title('', false);
	}
	if (is_tag()){
		return single_tag_title('', false);
	}
	if (is_author()){
		return get_the_author();
	}
	if (is_post_type_archive()){
		return post_type_archive_title('', false);
	}
	if (is_tax()){
		return single_term_title('', false);
	}
	return get_option('argon_seo_keywords');
}
//页面分享预览图
function get_og_image(){
	global $post;
	$postID = $post -> ID;
	$argon_first_image_as_thumbnail = get_post_meta($postID, 'argon_first_image_as_thumbnail', 'true');
	if (has_post_thumbnail() || $argon_first_image_as_thumbnail == 'true'){
		return argon_get_post_thumbnail($postID);
	}
	return '';
}
