<?php
/*
Plugin Name: cubicFUSION Admin Enhancer
Plugin URI: https://portalzine.de/cubicfusion
Description: Tools to enhance your WordPress Administration
Version: 0.2.5.2
Author: portalZINE NMN
Author URI: https://portalzine.de
Text Domain: cubicfusion-admin-enhancer
Domain Path: /languages
*/
use cubicFUSION\Core;
use cubicFUSION\Core\Util;
use cubicFUSION\Plugins;

if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CUBIC_ROOT', str_replace("//","/",rtrim($_SERVER['DOCUMENT_ROOT'],'/\\').'/'));
define('CUBIC_PATH', __DIR__);
define('CUBIC_PATH_ROOT', str_replace("//","/","/".str_replace($_SERVER['DOCUMENT_ROOT'],"",__DIR__)));
define('CUBIC_URL', plugins_url()."/".basename(__DIR__)."/");

require_once( trailingslashit( dirname( __FILE__ ) ) . 'lib/autoloader.php' );

if ( file_exists( dirname( __FILE__ ) . '/inc/cmb2/init.php' ) ) {
	require_once (trailingslashit( dirname( __FILE__ ) ) . 'inc/cmb2/init.php');
} elseif ( file_exists( dirname( __FILE__ ) . '/inc/CMB2/init.php' ) ) {
	require_once (trailingslashit( dirname( __FILE__ ) ) . 'inc/CMB2/init.php');
}

require_once(trailingslashit(dirname( __FILE__ )) . 'inc/closure/autoload.php');

add_action( 'plugins_loaded', 'cubicfusion_admin_enhancer' );

function cubicfusion_admin_enhancer() {
	
	$core 		= new Core\Basics();
	$gui 		= new Core\GUI();
	
	$shortcodes = new Plugins\Shortcodes();
	$shortcodes->init();	
	
	$gutenberg = new Plugins\Dashboard_Gutenberg();
	$gutenberg->init();
	
	$toolbar = new Plugins\Admin_Toolbar();
	$toolbar->init();
}