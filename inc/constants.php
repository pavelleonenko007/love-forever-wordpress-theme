<?php
/**
 * Constants
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

define( 'TEMPLATE_PATH', get_template_directory_uri() );
define( 'DATA_WF_SITE', '6720c60d7d5faf3e7ea1ac86' );

define( 'VK_LINK', get_field( 'vk', 'option' ) );
define( 'TELEGRAM_LINK', get_field( 'telegram', 'option' ) );
define( 'WHATSAPP_LINK', get_field( 'whatsapp', 'option' ) );
define( 'INSTAGRAM_LINK', get_field( 'instagram', 'option' ) );
define( 'YOUTUBE_LINK', get_field( 'youtube', 'option' ) );

define( 'PHONE', get_field( 'phone', 'option' ) );
define( 'EMAIL', get_field( 'email', 'option' ) );
define( 'ADDRESS', get_field( 'address', 'option' ) );
define( 'WORKING_HOURS', get_field( 'working_hours', 'option' ) );
define( 'MAP_LINK', get_field( 'map_link', 'option' ) );

define( 'PRIVACY_POLICY_LINK', ! empty( get_field( 'privacy_policy', 'option' ) ) ? get_field( 'privacy_policy', 'option' )['href'] : get_privacy_policy_url() );
