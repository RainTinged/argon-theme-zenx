<?php
//注册小工具
function argon_widgets_init() {
	register_sidebar(
		array(
			'name'          => __('左侧栏小工具', 'argon'),
			'id'            => 'leftbar-tools',
			'description'   => __( '左侧栏小工具 (如果设置会在侧栏增加一个 Tab)', 'argon'),
			'before_widget' => '<div id="%1$s" class="widget %2$s card bg-white border-0">',
			'after_widget'  => '</div>',
			'before_title'  => '<h6 class="font-weight-bold text-black">',
			'after_title'   => '</h6>',
		)
	);
	register_sidebar(
		array(
			'name'          => __('右侧栏小工具', 'argon'),
			'id'            => 'rightbar-tools',
			'description'   => __( '右侧栏小工具 (在 "Argon 主题选项" 中选择 "三栏布局" 才会显示)', 'argon'),
			'before_widget' => '<div id="%1$s" class="widget %2$s card shadow-sm bg-white border-0">',
			'after_widget'  => '</div>',
			'before_title'  => '<h6 class="font-weight-bold text-black">',
			'after_title'   => '</h6>',
		)
	);
	register_sidebar(
		array(
			'name'          => __('站点概览额外内容', 'argon'),
			'id'            => 'leftbar-siteinfo-extra-tools',
			'description'   => __( '站点概览额外内容', 'argon'),
			'before_widget' => '<div id="%1$s" class="widget %2$s card bg-white border-0">',
			'after_widget'  => '</div>',
			'before_title'  => '<h6 class="font-weight-bold text-black">',
			'after_title'   => '</h6>',
		)
	);
}
add_action('widgets_init', 'argon_widgets_init');
//注册新后台主题配色方案
function argon_add_admin_color(){
	wp_admin_css_color(
		'argon',
		'Argon',
		get_bloginfo('template_directory') . "/admin.css",
		array("#5e72e4", "#324cdc", "#e8ebfb"),
		array('base' => '#525f7f', 'focus' => '#5e72e4', 'current' => '#fff')
	);
}
add_action('admin_init', 'argon_add_admin_color');
function argon_admin_themecolor_css(){
	$themecolor = get_option("argon_theme_color", "#5e72e4");
	$RGB = hexstr2rgb($themecolor);
	$HSL = rgb2hsl($RGB['R'], $RGB['G'], $RGB['B']);
	echo "
		<style id='themecolor_css'>
			:root{
				--themecolor: {$themecolor} ;
				--themecolor-R: {$RGB['R']} ;
				--themecolor-G: {$RGB['G']} ;
				--themecolor-B: {$RGB['B']} ;
				--themecolor-H: {$HSL['H']} ;
				--themecolor-S: {$HSL['S']} ;
				--themecolor-L: {$HSL['L']} ;
			}
		</style>
	";
	if (get_option("argon_enable_immersion_color", "false") == "true"){
		echo "<script> document.documentElement.classList.add('immersion-color'); </script>";
	}
}
add_filter('admin_head', 'argon_admin_themecolor_css');
