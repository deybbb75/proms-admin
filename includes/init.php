<?php
// Disable error reporting
// error_reporting(1);

// Report runtime errors
// error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Report all errors
// error_reporting(E_ALL);

session_name('PromsAdminSession');
session_set_cookie_params(0, '/proms-admin/', $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']), true);
session_start();

set_time_limit(36000);
define('__SITE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/proms-admin');
date_default_timezone_set('Asia/Kuala_Lumpur');
define('IS_DEV', true);  // set to false in production
require_once __DIR__ . '/../assets/plugins/dotenv/vendor/autoload.php';

$GLOBALS['INF_CONFIG']['sitehost']   = 'http://localhost/proms-admin';
$GLOBALS['INF_CONFIG']['root']       = '/proms-admin/';
$GLOBALS['INF_CONFIG']['dbUser']     = 'root';
$GLOBALS['INF_CONFIG']['dbPass']     = '';
$GLOBALS['INF_CONFIG']['dbHost']     = 'localhost';
$GLOBALS['INF_CONFIG']['dbDatabase'] = 'proms';
$GLOBALS['INF_CONFIG']['showErrors'] = true;

$inf_dbhost  = $GLOBALS['INF_CONFIG']['dbHost'];
$inf_dbname  = $GLOBALS['INF_CONFIG']['dbDatabase'];
$inf_dbpass  = $GLOBALS['INF_CONFIG']['dbPass'];
$inf_dblogin = $GLOBALS['INF_CONFIG']['dbUser'];

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

/* include the controller class */
/* MODEL */
// include __SITE_PATH . '/model/' . 'db.class.php';
include __SITE_PATH . '/model/' . 'pdo.class.php';
include __SITE_PATH . '/model/' . 'alert.class.php';
include __SITE_PATH . '/model/' . 'fileValidator.class.php';
// include __SITE_PATH . '/model/' . 'pagination.class.php';
// include __SITE_PATH . '/model/' . 'ajaxpagination.class.php';
// include __SITE_PATH . '/model/' . 'datatablehandler.class.php';
// include __SITE_PATH . '/model/' . 'template.class.php';
// include __SITE_PATH . '/model/' . 'ResponseBuilder.php';

/* INCLUDES */
include __SITE_PATH . '/includes/' . 'common.functions.php';
// include __SITE_PATH . '/includes/' . 'chez.functions.php';
// include __SITE_PATH . '/includes/' . 'db.utility.php';

// $template = new Template;

?>