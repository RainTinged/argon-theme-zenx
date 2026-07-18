<?php
// =============== 字体管理模块 ===============

// 区域配置定义：每个区域包含 字体(font_id) / 字号(size) / 字重(weight) / CSS 选择器(selectors)
function argon_get_region_defaults(){
	return array(
		'body_global' => array(
			'label'    => __('正文/全局', 'argon'),
			'desc'     => __('页面全局基础样式', 'argon'),
			'selectors'=> 'body',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'heading' => array(
			'label'    => __('标题 H1-H6', 'argon'),
			'desc'     => __('文章内所有标题 h1 至 h6', 'argon'),
			'selectors'=> 'h1, h2, h3, h4, h5, h6, .article-title',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'navbar' => array(
			'label'    => __('顶部导航栏', 'argon'),
			'desc'     => __('导航栏菜单链接与品牌名称', 'argon'),
			'selectors'=> '#navbar-main, .navbar-brand, .navbar-nav .nav-link, .navbar-toggler, .dropdown-item, .dropdown-menu',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'banner' => array(
			'label'    => __('Banner 横幅', 'argon'),
			'desc'     => __('Banner 横幅标题与副标题文字', 'argon'),
			'selectors'=> '.banner-title, .banner-subtitle, .banner-title-inner',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'page_info' => array(
			'label'    => __('页面信息卡片', 'argon'),
			'desc'     => __('文章页与分类页的信息描述卡片', 'argon'),
			'selectors'=> '.page-information-card, .page-information-card h3',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'post_title' => array(
			'label'    => __('文章列表标题', 'argon'),
			'desc'     => __('首页与归档页中的文章标题', 'argon'),
			'selectors'=> '.post-title',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'post_meta' => array(
			'label'    => __('文章元信息', 'argon'),
			'desc'     => __('文章日期、分类、标签等附属信息', 'argon'),
			'selectors'=> '.post-meta, .post-meta-detail, .post-meta-detail-categories, .post-meta-devide, .tag.post-meta-detail-tag',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'post_content' => array(
			'label'    => __('文章正文', 'argon'),
			'desc'     => __('文章正文段落与页面内容', 'argon'),
			'selectors'=> '.post-content, .post-content p, .page-content, article .wp-block-image figcaption, .wp-caption-text, .reference-list li',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'sidebar' => array(
			'label'    => __('左侧边栏', 'argon'),
			'desc'     => __('侧栏作者信息、菜单、标签、公告、目录、日历、友链等', 'argon'),
			'selectors'=> '.leftbar-banner-title, .leftbar-banner-subtitle, .leftbar-menu-item > a, #leftbar_overview_author_name, #leftbar_overview_author_description, .site-state-item-count, .site-state-item-name, .tag.badge, .tag-num, .site-author-links-item > a, .leftbar-announcement-title, .leftbar-announcement-content, #leftbar_tab_tools h6, .sidebar-tab-switcher, #leftbar_catalog .index-link, #leftbar_catalog .index-item, .wp-calendar-table caption, .wp-calendar-nav a, .site-friend-links-title, .site-friend-links-item > a',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'footer' => array(
			'label'    => __('页脚', 'argon'),
			'desc'     => __('页面底部版权与页脚文字', 'argon'),
			'selectors'=> '#footer, #footer .copyright, .site-footer',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'comments' => array(
			'label'    => __('评论区域', 'argon'),
			'desc'     => __('评论区标题、昵称、正文、编辑历史、回复预览等', 'argon'),
			'selectors'=> '.comments-title, .comment-item-title, .comment-info, .comment-name, .comment-useragent, .comment-item-text, .comment-item-text p, .comment-post, .post-comment-title, .comment-item-text h1, .comment-item-text h2, .comment-item-text h3, .comment-item-text h4, .comment-item-text h5, .comment-item-text h6, .comment-edit-history-id, .comment-edit-history-time, .comment-edit-history-title, .post-comment-reply, #post_comment_content_hidden, .comment-post-checkbox .custom-control-label',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'post_navigation' => array(
			'label'    => __('文章上下篇导航', 'argon'),
			'desc'     => __('文章底部上一篇/下一篇导航文字', 'argon'),
			'selectors'=> '.post-navigation, .post-navigation-item, .post-navigation-pre, .post-navigation-next, .page-navigation-extra-text',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'related_posts' => array(
			'label'    => __('相关文章', 'argon'),
			'desc'     => __('文章底部相关推荐文章区域', 'argon'),
			'selectors'=> '.related-posts, .related-post-title, .related-post-card, .related-post-card-container',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'post_extra' => array(
			'label'    => __('文章附属信息', 'argon'),
			'desc'     => __('过期提示、密码保护、文末附加内容等', 'argon'),
			'selectors'=> '.post-outdated-info, .additional-content-after-post, .post-password-form-text, .post-password-hint, .post-donate .donate-btn',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'ui_controls' => array(
			'label'    => __('界面控件', 'argon'),
			'desc'     => __('博客设置弹窗按钮、表情键盘标签等', 'argon'),
			'selectors'=> '.blog-setting-filter-btn, .blog-setting-item button, .emotion-group-name, .emotion-group-description, .search-filter-wrapper .custom-control-label',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'shuoshuo' => array(
			'label'    => __('说说', 'argon'),
			'desc'     => __('说说模块的日期、标题与描述', 'argon'),
			'selectors'=> '.shuoshuo-meta, .shuoshuo-date-date, .shuoshuo-date-month, .shuoshuo-title, .shuoshuo-comments, .shuoshuo-comment-item-title, .shuoshuo-preview-meta, .shuoshuo-preview-container, .shuoshuo-preview-title',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'code_blocks' => array(
			'label'    => __('代码块', 'argon'),
			'desc'     => __('代码块与内联代码文字', 'argon'),
			'selectors'=> 'pre, code, .hljs-codeblock, code[hljs-codeblock-inner], .comment-item-text pre',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
		'widgets' => array(
			'label'    => __('短代码/挂件', 'argon'),
			'desc'     => __('友链、时间线、归档、GitHub卡片等', 'argon'),
			'selectors'=> '.friend-links-simple, .friend-links-style1, .friend-links-style2, .argon-timeline-time, .argon-timeline-title, .admonition-title, .collapse-block-title, .github-info-card, .archive-timeline-title, .archive-timeline-year, .archive-timeline-month',
			'font'     => '',
			'size'     => '',
			'weight'   => '',
		),
	);
}

// 字体库（用户添加的自定义字体）相关
function argon_get_font_library(){
	$raw = get_option('argon_font_library', '');
	if (empty($raw)) return array();
	$arr = json_decode($raw, true);
	if (!is_array($arr)) return array();
	return $arr;
}

function argon_get_font_library_item($id){
	if (empty($id)) return null;
	foreach (argon_get_font_library() as $f){
		if ($f['id'] === $id) return $f;
	}
	return null;
}

function argon_get_region_fonts(){
	$defaults = argon_get_region_defaults();
	$raw = get_option('argon_region_fonts', '');
	$arr = json_decode($raw, true);
	if (!is_array($arr)) $arr = array();
	$result = array();
	foreach ($defaults as $key => $meta){
		$cfg = isset($arr[$key]) ? $arr[$key] : array();
		$result[$key] = array(
			'label'    => $meta['label'],
			'desc'     => isset($meta['desc']) ? $meta['desc'] : '',
			'selectors'=> $meta['selectors'],
			'font'     => isset($cfg['font']) ? $cfg['font'] : '',
			'size'     => isset($cfg['size']) ? $cfg['size'] : '',
			'weight'   => isset($cfg['weight']) ? $cfg['weight'] : '',
		);
	}
	return $result;
}

// 字体选项到 CSS font-family 字符串的映射（系统字体 + Google Fonts 预置）
function argon_get_font_css_by_id($font_id){
	if (empty($font_id)) return '';
	// 字体库中的字体
	$lib = argon_get_font_library_item($font_id);
	if ($lib){
		$family = $lib['family'];
		$url = $lib['url'];
		$weight = !empty($lib['weight']) ? $lib['weight'] : '400';
		$format = isset($lib['type']) ? $lib['type'] : 'woff2';
		// 返回 font-family，@font-face 由 header.php 统一输出
		return "'" . $family . "'";
	}
	// 旧版预置字体 key
	$map = array(
		'google-open-sans'              => '"Open Sans", sans-serif',
		'google-noto-sans-sc'           => '"Noto Sans SC", sans-serif',
		'google-noto-serif-sc'          => '"Noto Serif SC", serif',
		'google-roboto'                 => '"Roboto", sans-serif',
		'google-lora'                   => '"Lora", serif',
		'google-inter'                  => '"Inter", sans-serif',
		'google-merriweather'           => '"Merriweather", serif',
		'google-jetbrains-mono'         => '"JetBrains Mono", monospace',
		'google-playfair-display'       => '"Playfair Display", serif',
		'google-montserrat'             => '"Montserrat", sans-serif',
		'google-source-han-sans-sc'     => '"Source Han Sans SC", sans-serif',
		'google-source-han-serif-sc'    => '"Source Han Serif SC", serif',
		'google-zcool-kuaile'           => '"ZCOOL KuaiLe", sans-serif',
		'google-zcool-qingke-huangyou'  => '"ZCOOL QingKe HuangYou", sans-serif',
		'google-Ma-Shan-Zheng-sc'       => '"Ma Shan Zheng", serif',
		'google-zhi-mang-xing'          => '"Zhi Mang Xing", sans-serif',
		'google-liu-jian-mao-cao'       => '"Liu Jian Mao Cao", sans-serif',
		'google-long-cang'              => '"Long Cang", sans-serif',
		'google-fira-code'              => '"Fira Code", monospace',
		'google-source-code-pro'        => '"Source Code Pro", monospace',
		'google-roboto-mono'            => '"Roboto Mono", monospace',
		'system-sans'                   => '-apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Helvetica, Arial, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", sans-serif',
		'system-serif'                  => '"Noto Serif SC", "Source Han Serif SC", Georgia, "Times New Roman", "SimSun", "STSong", serif',
		'system-mono'                   => 'SF Mono, "Cascadia Code", Consolas, "Courier New", monospace',
		'sans-serif'                    => '"Open Sans", -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", sans-serif',
		'serif'                         => '"Noto Serif SC", serif, system-ui',
	);
	return isset($map[$font_id]) ? $map[$font_id] : '';
}

function argon_get_google_font_name($font_id){
	$map = array(
		'google-open-sans'              => 'Open+Sans:wght@300;400;600;700',
		'google-noto-sans-sc'           => 'Noto+Sans+SC:wght@300;400;500;700',
		'google-noto-serif-sc'          => 'Noto+Serif+SC:wght@300;400;600;700',
		'google-roboto'                 => 'Roboto:wght@300;400;500;700',
		'google-lora'                   => 'Lora:wght@400;500;600;700',
		'google-inter'                  => 'Inter:wght@300;400;500;600;700',
		'google-merriweather'           => 'Merriweather:wght@300;400;700',
		'google-jetbrains-mono'         => 'JetBrains+Mono:wght@400;500;600',
		'google-playfair-display'       => 'Playfair+Display:wght@400;500;600;700',
		'google-montserrat'             => 'Montserrat:wght@300;400;500;600;700',
		'google-source-han-sans-sc'     => 'Noto+Sans+SC:wght@300;400;500;700',
		'google-source-han-serif-sc'    => 'Noto+Serif+SC:wght@300;400;600;700',
		'google-zcool-kuaile'           => 'ZCOOL+KuaiLe',
		'google-zcool-qingke-huangyou'  => 'ZCOOL+QingKe+HuangYou',
		'google-Ma-Shan-Zheng-sc'       => 'Ma+Shan+Zheng',
		'google-zhi-mang-xing'          => 'Zhi+Mang+Xing',
		'google-liu-jian-mao-cao'       => 'Liu+Jian+Mao+Cao',
		'google-long-cang'              => 'Long+Cang',
		'google-fira-code'              => 'Fira+Code:wght@400;500;600',
		'google-source-code-pro'        => 'Source+Code+Pro:wght@400;500;600',
		'google-roboto-mono'            => 'Roboto+Mono:wght@400;500',
	);
	return isset($map[$font_id]) ? $map[$font_id] : '';
}

// 生成 Google Fonts 加载 URL（从区域配器 + 旧选项 + 字体库 google 类型 + 自定义 URL 中聚合）
function argon_build_google_fonts_url(){
	if (get_option('argon_disable_googlefont') == 'true') return '';

	$custom_url = get_option('argon_custom_google_font_url', '');
	if (!empty($custom_url)) return $custom_url;

	$fonts = array();

	// 旧版 sans-serif/serif 兜底
	$argon_font = get_option('argon_font', 'sans-serif');
	if ($argon_font == 'sans-serif'){
		$fonts['Open+Sans:wght@300;400;600;700'] = true;
		$fonts['Noto+Serif+SC:wght@300;600']     = true;
	} else {
		$fonts['Noto+Serif+SC:wght@300;400;600;700'] = true;
	}

	// 字体库中 type=google 的字体（以其 URL 作为 CSS 链接）
	foreach (argon_get_font_library() as $f){
		if (isset($f['type']) && $f['type'] === 'google' && !empty($f['url'])){
			$fonts[$f['url']] = true;
		}
	}

	// 区域配器中使用 Google 字体 key 的
	foreach (argon_get_region_fonts() as $cfg){
		$fid = $cfg['font'];
		if (strpos($fid, 'google-') === 0){
			$name = argon_get_google_font_name($fid);
			if (!empty($name)) $fonts[$name] = true;
		}
	}

	if (empty($fonts)) return '';
	return '//fonts.googleapis.com/css?family=' . implode('|', array_keys($fonts)) . '&display=swap';
}

// 生成 @font-face 声明（仅来自字体库）
function argon_build_font_face_css(){
	$lib = argon_get_font_library();
	if (empty($lib)) return '';
	$out = '';
	foreach ($lib as $f){
		if (empty($f['family']) || empty($f['url'])) continue;
		$font_type = isset($f['type']) ? $f['type'] : 'woff2';
		if ($font_type === 'google') continue; // Google Fonts 不走 @font-face
		$family  = $f['family'];
		$url     = $f['url'];
		$weight  = !empty($f['weight']) ? $f['weight'] : '400';
		$format  = $font_type;
		$display = isset($f['display']) ? $f['display'] : 'swap';
		$out .= "@font-face {\n";
		$out .= "  font-family: '" . esc_attr($family) . "';\n";
		$out .= "  src: url('" . esc_url($url) . "') format('" . esc_attr($format) . "');\n";
		$out .= "  font-weight: " . esc_attr($weight) . ";\n";
		$out .= "  font-display: " . esc_attr($display) . ";\n";
		$out .= "}\n";
	}
	return $out;
}
