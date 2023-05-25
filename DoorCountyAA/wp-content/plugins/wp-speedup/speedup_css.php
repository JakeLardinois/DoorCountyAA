<?php if ( ! defined( 'ABSPATH' ) ) exit; 

// this file is the interface to the combined CSS file that is written in the WP upload directories

$tmp_dir = 'tmp/';
if ( ! is_writable( dirname( $tmp_dir ) ) ) $tmp_dir = sys_get_temp_dir() . '/';
$settings_path = $tmp_dir . $_SERVER['HTTP_HOST'] . '-settings.dat';
if ( file_exists( $settings_path ) && strlen( $_GET['token'] ) == 32 ) {
    $settings = file_get_contents( $settings_path );
    $settings = unserialize( $settings );
    $css_file = $settings['upload_path'] . $_GET['token'] . '.css';
    if ( file_exists(  $css_file ) ) {
        if ( $settings['compress'] == 'Yes' && extension_loaded( 'zlib' ) ) ob_start( 'ob_gzhandler' );
        header( "Content-type: text/css" );
        readfile( $css_file );
        if ( $settings['compress'] == 'Yes' && extension_loaded( 'zlib' ) ) ob_end_flush();
    }
}