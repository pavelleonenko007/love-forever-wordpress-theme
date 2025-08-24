<?php
/**
 * Functions
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/bootstrap.php';

require_once __DIR__ . '/inc/constants.php';
require_once __DIR__ . '/inc/utils.php';
require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/scripts.php';
require_once __DIR__ . '/inc/post-types.php';
require_once __DIR__ . '/inc/options.php';
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/inc/hooks.php';

require_once __DIR__ . '/includes/class-dress-order.php';
require_once __DIR__ . '/includes/class-promo-order.php';
require_once __DIR__ . '/includes/class-story-order.php';
require_once __DIR__ . '/includes/class-fitting-slots.php';
require_once __DIR__ . '/inc/class-loveforever-dress-importer.php';
require_once __DIR__ . '/inc/cli/reupdate-posts.php';
require_once __DIR__ . '/inc/class-loveforever-review-importer.php';
// require_once __DIR__ . '/inc/dress-categories-importer.php'