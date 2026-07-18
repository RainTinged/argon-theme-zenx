<?php
/**
 * Argon Theme - functions.php
 * 
 * 此文件为模块加载器，所有功能代码已拆分至 inc/ 目录。
 * 加载顺序有依赖关系，请勿随意调整顺序。
 */

// 1. 主题初始化（版本、资源路径、本地化、迁移、统计、Session、版权检查）
require_once get_template_directory() . '/inc/setup.php';

// 2. 主题更新检测（依赖 $GLOBALS['theme_version']）
require_once get_template_directory() . '/inc/update-checker.php';

// 3. 颜色工具函数（widgets.php 依赖）
require_once get_template_directory() . '/inc/color-utils.php';

// 4. 通用工具函数（评论模块依赖 format_number_in_kilos、array_remove）
require_once get_template_directory() . '/inc/helpers.php';

// 5. 文章特色图片（seo.php 依赖 argon_get_post_thumbnail）
require_once get_template_directory() . '/inc/post-thumbnails.php';

// 6. SEO + Session/Token（调用 session_init()，comment-captcha 依赖 session）
require_once get_template_directory() . '/inc/seo.php';

// 7. 浏览量统计 & 文章过时信息
require_once get_template_directory() . '/inc/post-views.php';

// 8. 字数统计 & 阅读时间 & 文章 Meta
require_once get_template_directory() . '/inc/reading-time.php';

// 9. 内容过滤器（Lazyload、Fancybox、Gravatar CDN、Banner 背景图）
require_once get_template_directory() . '/inc/content-filters.php';

// 10. 小工具注册 & 后台配色（依赖 color-utils 的 hexstr2rgb/rgb2hsl）
require_once get_template_directory() . '/inc/widgets.php';

// 11. 评论核心（UA解析、Token、悄悄话、格式化、点赞、QQ头像）
require_once get_template_directory() . '/inc/comments-core.php';

// 12. 评论验证码（依赖 session_init）
require_once get_template_directory() . '/inc/comment-captcha.php';

// 13. 评论 AJAX（发送、Markdown解析、邮件通知、编辑、置顶、分页、排序）
require_once get_template_directory() . '/inc/comments-ajax.php';

// 14. 说说（点赞、自定义文章类型注册）
require_once get_template_directory() . '/inc/shuoshuo.php';

// 15. 字体管理（区域字体、字体库、Google Fonts、@font-face）
require_once get_template_directory() . '/inc/fonts.php';

// 16. 文章 Meta Box（编辑界面 Meta 框、保存、AJAX 更新）
require_once get_template_directory() . '/inc/meta-box.php';

// 17. 首页查询过滤器（说说显示、隐藏分类）
require_once get_template_directory() . '/inc/query-filters.php';

// 18. Gutenberg 区块注册
require_once get_template_directory() . '/inc/gutenberg.php';

// 19. 短代码（label/progressbar/checkbox/alert/admonition/collapse/friendlinks/timeline/hidden/github/video/ref等）
require_once get_template_directory() . '/inc/shortcodes.php';

// 20. TinyMCE 编辑器按钮
require_once get_template_directory() . '/inc/tinymce.php';

// 21. 主题选项菜单（包含 settings.php）
require_once get_template_directory() . '/inc/theme-options.php';

// 22. 导航菜单注册
require_once get_template_directory() . '/inc/nav-menus.php';

// 23. 搜索过滤器 & 链接管理器
require_once get_template_directory() . '/inc/search-filter.php';

// 24. 登录页样式
require_once get_template_directory() . '/inc/login.php';
