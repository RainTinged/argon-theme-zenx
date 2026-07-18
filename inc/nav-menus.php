<?php
/*主题菜单*/
add_action('init', 'init_nav_menus');
function init_nav_menus(){
	register_nav_menus( array(
		'toolbar_menu' => __('顶部导航', 'argon'),
		'leftbar_menu' => __('左侧栏菜单', 'argon'),
		'leftbar_author_links' => __('左侧栏作者个人链接', 'argon'),
		'leftbar_friend_links' => __('左侧栏友情链接', 'argon')
	));
}

//隐藏 admin 管理条
//show_admin_bar(false);
