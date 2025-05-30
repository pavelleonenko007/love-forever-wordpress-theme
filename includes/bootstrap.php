<?php
/**
 *  Bootstrap classes
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/utils/class-logger.php';
require_once __DIR__ . '/sms/interface-sms-provider.php';
require_once __DIR__ . '/sms/class-redsms-provider.php';
require_once __DIR__ . '/sms/class-sms-service.php';
require_once __DIR__ . '/sms/class-sms-scheduler.php';
require_once __DIR__ . '/sms/class-appointment-manager.php';

$sms_provider = RedSmsProvider::get_instance();
$sms_service  = new SmsService( $sms_provider );
new AppointmentManager( $sms_service );
