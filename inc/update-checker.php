<?php
//检测更新
require_once(get_template_directory() . '/theme-update-checker/plugin-update-checker.php');
$argon_update_source = get_option('argon_update_source');
switch ($argon_update_source) {
	case "stop":
		break;
    case "fastgit":
	    $argonThemeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://api.solstice23.top/argon/info.json?source=fastgit',
			get_template_directory() . '/functions.php',
			'argon'
		);
        break;
    case "cfworker":
	    $argonThemeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://api.solstice23.top/argon/info.json?source=cfworker',
			get_template_directory() . '/functions.php',
			'argon'
		);
        break;
	case "solstice23top":
		$argonThemeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://api.solstice23.top/argon/info.json?source=0',
			get_template_directory() . '/functions.php',
			'argon'
		);
		break;
	case "github":
    default:
		$argonThemeUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://raw.githubusercontent.com/solstice23/argon-theme/master/info.json',
			get_template_directory() . '/functions.php',
			'argon'
		);
}
