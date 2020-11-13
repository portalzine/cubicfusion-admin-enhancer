<?php

if (!defined('WP_UNINSTALL_PLUGIN') ) {
    die;
}


function cubicfusion_delete_options_prefixed( $prefix ) {
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'" );
}

cubicfusion_delete_options_prefixed( 'cf_plugins_' );