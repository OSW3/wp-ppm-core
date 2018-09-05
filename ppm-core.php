<?php
/**
 * Plugin Name: #PPM-Core V2
 * Version:     2.0.0
 * Plugin URI:  http://osw3.net/wordpress/plugins/please-plug-me/
 * Description: This is the core of PPM Plugins. It must be activate to use all PPM Plugins.
 * Author:      OSW3
 * Author URI:  http://osw3.net/
 * Text Domain: wp-ppm
 * Domain Path: /Languages/
 * License:     GPL v3
 * Repository:	https://github.com/OSW3/wp-ppm-core
 */

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

include_once __DIR__.'/bootstrap.php';