<?php
//主题文章短代码解析
function shortcode_content_preprocess($attr, $content = ""){
	if ( isset( $attr['nested'] ) ? $attr['nested'] : 'true' != 'false' ){
		return do_shortcode($content);
	}else{
		return $content;
	}	
}
add_shortcode('br','shortcode_br');
function shortcode_br($attr,$content=""){
	return "</br>";
}
add_shortcode('label','shortcode_label');
function shortcode_label($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$out = "<span class='badge";
	$color = isset( $attr['color'] ) ? $attr['color'] : 'indigo';
	switch ($color){
		case 'green': $out .= " badge-success"; break;
		case 'red': $out .= " badge-danger"; break;
		case 'orange': $out .= " badge-warning"; break;
		case 'blue': $out .= " badge-info"; break;
		case 'indigo':
		default: $out .= " badge-primary"; break;
	}
	$shape = isset( $attr['shape'] ) ? $attr['shape'] : 'square';
	if ($shape=="round"){ $out .= " badge-pill"; }
	$out .= "'>" . $content . "</span>";
	return $out;
}
add_shortcode('progressbar','shortcode_progressbar');
function shortcode_progressbar($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$out = "<div class='progress-wrapper'><div class='progress-info'>";
	if ($content != ""){ $out .= "<div class='progress-label'><span>" . $content . "</span></div>"; }
	$progress = isset( $attr['progress'] ) ? $attr['progress'] : 100;
	$out .= "<div class='progress-percentage'><span>" . $progress . "%</span></div>";
	$out .= "</div><div class='progress'><div class='progress-bar";
	$color = isset( $attr['color'] ) ? $attr['color'] : 'indigo';
	switch ($color){
		case 'indigo': $out .= " bg-primary"; break;
		case 'green': $out .= " bg-success"; break;
		case 'red': $out .= " bg-danger"; break;
		case 'orange': $out .= " bg-warning"; break;
		case 'blue': $out .= " bg-info"; break;
		default: $out .= " bg-primary"; break;
	}
	$out .= "' style='width: " . $progress . "%;'></div></div></div>";
	return $out;
}
add_shortcode('checkbox','shortcode_checkbox');
function shortcode_checkbox($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$checked = isset( $attr['checked'] ) ? $attr['checked'] : 'false';
	$inline = isset($attr['inline']) ? $attr['checked'] : 'false';
	$out = "<div class='shortcode-todo custom-control custom-checkbox";
	if ($inline == 'true'){ $out .= " inline"; }
	$out .= "'>
			<input class='custom-control-input' type='checkbox'" . ($checked == 'true' ? ' checked' : '') . ">
			<label class='custom-control-label'>
				<span>" . $content . "</span>
			</label>
		</div>";
	return $out;
}
add_shortcode('alert','shortcode_alert');
function shortcode_alert($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$out = "<div class='alert";
	$color = isset( $attr['color'] ) ? $attr['color'] : 'indigo';
	switch ($color){
		case 'indigo': $out .= " alert-primary"; break;
		case 'green': $out .= " alert-success"; break;
		case 'red': $out .= " alert-danger"; break;
		case 'orange': $out .= " alert-warning"; break;
		case 'blue': $out .= " alert-info"; break;
		case 'black': $out .= " alert-default"; break;
		default: $out .= " alert-primary"; break;
	}
	$out .= "'>";
	if (isset($attr['icon'])){ $out .= "<span class='alert-inner--icon'><i class='fa fa-" . $attr['icon'] . "'></i></span>"; }
	$out .= "<span class='alert-inner--text'>";
	if (isset($attr['title'])){ $out .= "<strong>" . $attr['title'] . "</strong> "; }
	$out .= $content . "</span></div>";
	return $out;
}
add_shortcode('admonition','shortcode_admonition');
function shortcode_admonition($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$out = "<div class='admonition shadow-sm";
	$color = isset( $attr['color'] ) ? $attr['color'] : 'indigo';
	switch ($color){
		case 'indigo': $out .= " admonition-primary"; break;
		case 'green': $out .= " admonition-success"; break;
		case 'red': $out .= " admonition-danger"; break;
		case 'orange': $out .= " admonition-warning"; break;
		case 'blue': $out .= " admonition-info"; break;
		case 'black': $out .= " admonition-default"; break;
		case 'grey': $out .= " admonition-grey"; break;
		default: $out .= " admonition-primary"; break;
	}
	$out .= "'>";
	if (isset($attr['title'])){
		$out .= "<div class='admonition-title'>";
		if (isset($attr['icon'])){ $out .= "<i class='fa fa-" . $attr['icon'] . "'></i> "; }
		$out .= $attr['title'] . "</div>";
	}
	if ($content != ''){ $out .= "<div class='admonition-body'>" . $content . "</div>"; }
	$out .= "</div>";
	return $out;
}
add_shortcode('collapse','shortcode_collapse_block');
add_shortcode('fold','shortcode_collapse_block');
function shortcode_collapse_block($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$collapsed = isset( $attr['collapsed'] ) ? $attr['collapsed'] : 'true';
	$show_border_left = isset( $attr['showleftborder'] ) ? $attr['showleftborder'] : 'false';
	$out = "<div " ;
	$out .= " class='collapse-block shadow-sm";
	$color = isset( $attr['color'] ) ? $attr['color'] : 'none';
	$title = isset( $attr['title'] ) ? $attr['title'] : '';
	switch ($color){
		case 'indigo': $out .= " collapse-block-primary"; break;
		case 'green': $out .= " collapse-block-success"; break;
		case 'red': $out .= " collapse-block-danger"; break;
		case 'orange': $out .= " collapse-block-warning"; break;
		case 'blue': $out .= " collapse-block-info"; break;
		case 'black': $out .= " collapse-block-default"; break;
		case 'grey': $out .= " collapse-block-grey"; break;
		case 'none':
		default: $out .= " collapse-block-transparent"; break;
	}
	if ($collapsed == 'true'){ $out .= " collapsed"; }
	if ($show_border_left != 'true'){ $out .= " hide-border-left"; }
	$out .= "'>";
	$out .= "<div class='collapse-block-title'>";
	if (isset($attr['icon'])){ $out .= "<i class='fa fa-" . $attr['icon'] . "'></i> "; }
	$out .= "<span class='collapse-block-title-inner'>" . $title . "</span><i class='collapse-icon fa fa-angle-down'></i></div>";
	$out .= "<div class='collapse-block-body'";
	if ($collapsed != 'false'){ $out .= " style='display:none;'"; }
	$out .= ">" . $content . "</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode('friendlinks','shortcode_friend_link');
function shortcode_friend_link($attr,$content=""){
	$sort = isset( $attr['sort'] ) ? $attr['sort'] : 'name';
	$order = isset( $attr['order'] ) ? $attr['order'] : 'ASC';
	$friendlinks = get_bookmarks( array(
		'orderby' => $sort ,
		'order'   => $order
	));
	$style = isset( $attr['style'] ) ? $attr['style'] : '1';
	switch ($style) {
		case '1': $class = "friend-links-style1"; break;
		case '1-square': $class = "friend-links-style1 friend-links-style1-square"; break;
		case '2': $class = "friend-links-style2"; break;
		case '2-big': $class = "friend-links-style2 friend-links-style2-big"; break;
		default: $class = "friend-links-style1"; break;
	}
	$out = "<div class='friend-links " . $class . "'><div class='row'>";
	foreach ($friendlinks as $friendlink){
		$out .= "<div class='link mb-2 col-lg-6 col-md-6'>
			<div class='card shadow-sm friend-link-container" . ($friendlink -> link_image == "" ? " no-avatar" : "") . "'>";
		if ($friendlink -> link_image != ''){
			$out .= "<img src='" . $friendlink -> link_image . "' class='friend-link-avatar bg-gradient-secondary'>";
		}
		$out .= "<div class='friend-link-content'>
						<div class='friend-link-title title text-primary'>
							<a target='_blank' href='" . esc_url($friendlink -> link_url) . "'>" . esc_html($friendlink -> link_name) . "</a>
						</div>
						<div class='friend-link-description'>" . esc_html($friendlink -> link_description) . "</div>";
		$out .= "<div class='friend-link-links'>";
		foreach (explode("\n", $friendlink -> link_notes) as $line){
			$item = explode("|", trim($line));
			if(stripos($item[0], "fa-") !== 0){ continue; }
			$out .= "<a href='" . esc_url($item[1]) . "' target='_blank'><i class='fa " . sanitize_html_class($item[0]) . "'></i></a>";
		}
		$out .= "<a href='" . esc_url($friendlink -> link_url) . "' target='_blank' style='float:right; margin-right: 10px;'><i class='fa fa-angle-right' style='font-weight: bold;'></i></a>";
		$out .= "</div></div></div></div>";
	}
	$out .= "</div></div>";
	return $out;
}
add_shortcode('sfriendlinks','shortcode_friend_link_simple');
function shortcode_friend_link_simple($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$content = trim(strip_tags($content));
	$entries = explode("\n" , $content);
	$shuffle = isset( $attr['shuffle'] ) ? $attr['shuffle'] : 'false';
	if ($shuffle == "true"){
		mt_srand();
		$group_start = 0;
		foreach ($entries as $index => $value){
			$now = explode("|" , $value);
			if ($now[0] == 'category'){
				for ($i = $index - 1; $i >= $group_start; $i--){
					$tar = mt_rand($group_start , $i);
					$tmp = $entries[$tar];
					$entries[$tar] = $entries[$i];
					$entries[$i] = $tmp;
				}
				$group_start = $index + 1;
			}
		}
		for ($i = count($entries) - 1; $i >= $group_start; $i--){
			$tar = mt_rand($group_start , $i);
			$tmp = $entries[$tar];
			$entries[$tar] = $entries[$i];
			$entries[$i] = $tmp;
		}
	}
	$row_tag_open = False;
	$out = "<div class='friend-links-simple'>";
	foreach($entries as $index => $value){
		$now = explode("|" , $value);
		if ($now[0] == 'category'){
			if ($row_tag_open == True){ $row_tag_open = False; $out .= "</div>"; }
			$out .= "<div class='friend-category-title text-black'>" . $now[1] . "</div>";
		}
		if ($now[0] == 'link'){
			if ($row_tag_open == False){ $row_tag_open = True; $out .= "<div class='row'>"; }
			$out .= "<div class='link mb-2 col-lg-4 col-md-6'>
				<div class='card shadow-sm'><div class='d-flex'><div class='friend-link-avatar'>
					<a target='_blank' href='" . $now[1] . "'>";
			if (!ctype_space($now[4]) && $now[4] != '' && isset($now[4])){
				$out .= "<img src='" . $now[4] . "' class='icon bg-gradient-secondary rounded-circle text-white' style='pointer-events: none;'></img>";
			}else{
				$out .= "<div class='icon icon-shape bg-gradient-primary rounded-circle text-white'>" . mb_substr($now[2], 0, 1) . "</div>";
			}
			$out .= "</a></div><div class='pl-3'><div class='friend-link-title title text-primary'><a target='_blank' href='" . $now[1] . "'>" . $now[2] . "</a></div>";
			if (!ctype_space($now[3]) && $now[3] != ''  && isset($now[3])){
				$out .= "<p class='friend-link-description'>" . $now[3] . "</p>";
			}
			$out .= "<a target='_blank' href='" . $now[1] . "' class='text-primary opacity-8'>前往</a></div></div></div></div>";
		}
	}
	if ($row_tag_open == True){ $row_tag_open = False; $out .= "</div>"; }
	$out .= "</div>";
	return $out;
}
add_shortcode('timeline','shortcode_timeline');
function shortcode_timeline($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$content = trim(strip_tags($content));
	$entries = explode("\n" , $content);
	$out = "<div class='argon-timeline'>";
	foreach($entries as $index => $value){
		$now = explode("|" , $value);
		$now[0] = str_replace("/" , "</br>" , $now[0]);
		$out .= "<div class='argon-timeline-node'>
					<div class='argon-timeline-time'>" . $now[0] . "</div>
					<div class='argon-timeline-card card bg-gradient-secondary shadow-sm'>";
		if ($now[1] != ''){ $out .= "<div class='argon-timeline-title'>" . $now[1] . "</div>"; }
		$out .= "<div class='argon-timeline-content'>";
		foreach($now as $index => $value){
			if ($index < 2){ continue; }
			if ($index > 2){ $out .= "</br>"; }
			$out .= $value;
		}
		$out .= "</div></div></div>";
	}
	$out .= "</div>";
	return $out;
}
add_shortcode('hidden','shortcode_hidden');
add_shortcode('spoiler','shortcode_hidden');
function shortcode_hidden($attr,$content=""){
	$content = shortcode_content_preprocess($attr, $content);
	$out = "<span class='argon-hidden-text";
	$tip = isset( $attr['tip'] ) ? $attr['tip'] : '';
	$type = isset( $attr['type'] ) ? $attr['type'] : 'blur';
	if ($type == "background"){ $out .= " argon-hidden-text-background"; }
	else{ $out .= " argon-hidden-text-blur"; }
	$out .= "'";
	if ($tip != ''){ $out .= " title='" . $tip ."'"; }
	$out .= ">" . $content . "</span>";
	return $out;
}
add_shortcode('github','shortcode_github');
function shortcode_github($attr,$content=""){
	$github_info_card_id = mt_rand(1000000000 , 9999999999);
	$author = isset( $attr['author'] ) ? $attr['author'] : '';
	$project = isset( $attr['project'] ) ? $attr['project'] : '';
	$getdata = isset( $attr['getdata'] ) ? $attr['getdata'] : 'frontend';
	$size = isset( $attr['size'] ) ? $attr['size'] : 'full';
	$description = "";
	$stars = "";
	$forks = "";
	if ($getdata == "backend"){
		set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
			if (error_reporting() === 0) { return false; }
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		});
		try{
			$contexts = stream_context_create(array('http' => array('method'=>"GET",'header'=>"User-Agent: ArgonTheme\r\n")));
			$json = file_get_contents("https://api.github.com/repos/" . $author . "/" . $project, false, $contexts);
			if (empty($json)){ throw new Exception(""); }
			$json = json_decode($json);
			$description = esc_html($json -> description);
			if (!empty($json -> homepage)){
				$description .= esc_html(" <a href='" . $json -> homepage . "' target='_blank' no-pjax>" . $json -> homepage . "</a>");
			}
			$stars = $json -> stargazers_count;
			$forks = $json -> forks_count;
		}catch (Exception $e){
			$getdata = "frontend";
		}
		restore_error_handler();
	}
	$out = "<div class='github-info-card github-info-card-" . $size . " card shadow-sm' data-author='" . $author . "' data-project='" . $project . "' githubinfo-card-id='" . $github_info_card_id . "' data-getdata='" . $getdata . "' data-description='" . $description . "' data-stars='" . $stars . "' data-forks='" . $forks . "'>";
	$out .= "<div class='github-info-card-header'><a href='https://github.com/' ref='nofollow' target='_blank' title='Github' no-pjax><span><i class='fa fa-github'></i>";
	if ($size != "mini"){ $out .= " GitHub"; }
	$out .= "</span></a></div>";
	$out .= "<div class='github-info-card-body'>
			<div class='github-info-card-name-a'>
				<a href='https://github.com/" . $author . "/" . $project . "' target='_blank' no-pjax>
					<span class='github-info-card-name'>" . $author . "/" . $project . "</span>
				</a>
				</div>
			<div class='github-info-card-description'></div>
		</div>";
	$out .= "<div class='github-info-card-bottom'>
				<span class='github-info-card-meta github-info-card-meta-stars'>
					<i class='fa fa-star'></i> <span class='github-info-card-stars'></span>
				</span>
				<span class='github-info-card-meta github-info-card-meta-forks'>
					<i class='fa fa-code-fork'></i> <span class='github-info-card-forks'></span>
				</span>
			</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode('video','shortcode_video');
function shortcode_video($attr,$content=""){
	$url = isset( $attr['mp4'] ) ? $attr['mp4'] : '';
	$url = isset( $attr['url'] ) ? $attr['url'] : $url;
	$width = isset( $attr['width'] ) ? $attr['width'] : '';
	$height = isset( $attr['height'] ) ? $attr['height'] : '';
	$autoplay = isset( $attr['autoplay'] ) ? $attr['autoplay'] : 'false';
	$out = "<video";
	if ($width != ''){ $out .= " width='" . $width . "'"; }
	if ($height != ''){ $out .= " height='" . $height . "'"; }
	if ($autoplay == 'true'){ $out .= " autoplay"; }
	$out .= " controls>";
	$out .= "<source src='" . $url . "'>";
	$out .= "</video>";
	return $out;
}
add_shortcode('hide_reading_time','shortcode_hide_reading_time');
function shortcode_hide_reading_time($attr,$content=""){
	return "";
}
add_shortcode('post_time','shortcode_post_time');
function shortcode_post_time($attr,$content=""){
	$format = isset( $attr['format'] ) ? $attr['format'] : 'Y-n-d G:i:s';
	return get_the_time($format);
}
add_shortcode('post_modified_time','shortcode_post_modified_time');
function shortcode_post_modified_time($attr,$content=""){
	$format = isset( $attr['format'] ) ? $attr['format'] : 'Y-n-d G:i:s';
	return get_the_modified_time($format);
}
add_shortcode('noshortcode','shortcode_noshortcode');
function shortcode_noshortcode($attr,$content=""){
	return $content;
}
//Reference Footnote
add_shortcode('ref','shortcode_ref');
$post_references = array();
$post_reference_keys_first_index = array();
$post_reference_contents_first_index = array();
function argon_get_ref_html($content, $index, $subIndex){
	$index++;
	return "<sup class='reference' id='ref_" . $index . "_" . $subIndex . "' data-content='" . esc_attr($content) . "' tabindex='0'><a class='reference-link' href='#ref_" . $index . "'>[" . $index . "]</a></sup>";
}
function shortcode_ref($attr,$content=""){
	global $post_references;
	global $post_reference_keys_first_index;
	global $post_reference_contents_first_index;
	$content = preg_replace('/<p>(.*?)<\/p>/is', '</br>$1', $content);
	$content = wp_kses($content, array(
		'a' => array('href' => array(), 'title' => array(), 'target' => array()),
		'br' => array(),
		'em' => array(),
		'strong' => array(),
		'b' => array(),
		'sup' => array(),
		'sub' => array(),
		'small' => array()
	));
	if (isset($attr['id'])){
		if (isset($post_reference_keys_first_index[$attr['id']])){
			$post_references[$post_reference_keys_first_index[$attr['id']]]['count']++;
		}else{
			array_push($post_references, array('content' => $content, 'count' => 1));
			$post_reference_keys_first_index[$attr['id']] = count($post_references) - 1;
		}
		$index = $post_reference_keys_first_index[$attr['id']];
		return argon_get_ref_html($post_references[$index]['content'], $index, $post_references[$index]['count']);
	}else{
		if (isset($post_reference_contents_first_index[$content])){
			$post_references[$post_reference_contents_first_index[$content]]['count']++;
			$index = $post_reference_contents_first_index[$content];
			return argon_get_ref_html($post_references[$index]['content'], $index, $post_references[$index]['count']);
		}else{
			array_push($post_references, array('content' => $content, 'count' => 1));
			$post_reference_contents_first_index[$content] = count($post_references) - 1;
			$index = count($post_references) - 1;
			return argon_get_ref_html($post_references[$index]['content'], $index, $post_references[$index]['count']);
		}
	}
}
function get_reference_list(){
	global $post_references;
	if (count($post_references) == 0){
		return "";
	}
	$res = "<div class='reference-list-container'>";
	$res .= "<h3>" . (get_option('argon_reference_list_title') == "" ? __('参考', 'argon') : get_option('argon_reference_list_title')) . "</h3>";
	$res .= "<ol class='reference-list'>";
		foreach ($post_references as $index => $ref) {
			$res .= "<li id='ref_" . ($index + 1)  . "'><div>";
			if ($ref['count'] == 1){
				$res .= "<a class='reference-list-backlink' href='#ref_" . ($index + 1) . "_1' aria-label='back'>^</a>";
			}else{
				$res .= "<span class='reference-list-backlink'>^</span>";
				for ($i = 1, $j = 'a'; $i <= $ref['count']; $i++, $j++){
					$res .= "<sup><a class='reference-list-backlink' href='#ref_" . ($index + 1) . "_" . $i . "' aria-label='back'>" . $j . "</a></sup>";
				}
			}
			$res .= "<span>" . $ref['content'] . "</span>";
			$res .= "<div class='space' tabindex='-1'></div>";
			$res .= "</div></li>";
		}
	$res .= "</ol>";
	$res .= "</div>";
	return $res;
}
