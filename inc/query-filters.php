<?php
//首页显示说说
function argon_home_add_post_type_shuoshuo($query){
	if (is_home() && $query -> is_main_query()){
		$query -> set('post_type', array('post', 'shuoshuo'));
	}
	return $query;
}
if (get_option("argon_home_show_shuoshuo") == "true"){
	add_action('pre_get_posts', 'argon_home_add_post_type_shuoshuo');
}
//首页隐藏特定分类文章
function argon_home_hide_categories($query){
	if (is_home() && $query -> is_main_query()){
		$excludeCategories = explode(",", get_option("argon_hide_categories"));
		$excludeCategories = array_map(function($cat) { return -$cat; }, $excludeCategories);
		$query -> set('category__not_in', $excludeCategories);
		$query -> set('tag__not_in', $excludeCategories);
	}
	return $query;
}
if (get_option("argon_hide_categories") != ""){
	add_action('pre_get_posts', 'argon_home_hide_categories');
}
