<?php
//主题选项页面
function themeoptions_admin_menu(){
	/*后台管理面板侧栏添加选项*/
	add_menu_page(__("Argon 主题设置", 'argon'), __("Argon 主题选项", 'argon'), 'edit_theme_options', basename(__FILE__), 'themeoptions_page');
}
include_once(get_template_directory() . '/settings.php');
