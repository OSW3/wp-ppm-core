<?php
/**
 * Plugin Name: #PPM-Core V2
 * Version:     1.0.0
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











// $ppm_root_file 	 = __FILE__;
// $ppm_root_dir 	 = __DIR__;
// $ppm_folder_name = basename(dirname($ppm_root_file));

// if (!defined('ABSPATH')){
// 	die("The constant \"ABSPATH\" is undefined for the plugin \"$ppm_folder_name\".");
// }

// $ppm_required = [
// 	// ABSPATH.'wp-admin/includes/plugin.php',
// 	// ABSPATH.'wp-admin/includes/screen.php',
// 	// $ppm_root_dir.'/bootstrap.php'
// 	__DIR__.'/bootstrap.php'
// ];

// foreach ($ppm_required as $file) 
// {
// 	file_exists($file) ? include_once $file : die("<strong>Plugin Error</strong>: A required file ($file) is not found for the plugin \"$ppm_folder_name\".");
// }

// -- 

// use \Components\Form\Response;
// use \Components\Notices;
// use \Kernel\Session;

// if (!class_exists('PPM'))
// {
// 	class PPM 
// 	{
// 		private $namespace;
// 		private $posttype;
// 		private $responses;
	
// 		public function __construct(string $namespace, string $posttype)
// 		{
// 			$this->namespace = $namespace; 
// 			$this->posttype = $posttype;
// 		}
	
// 		public function responses( array $posts, bool $asObject = false )
// 		{
// 			$response = new Response( $posts );
// 			$responses = $response->responses();
// 			$this->responses = $responses;
	
// 			return $asObject ? $responses : $responses->sanitizedResponses( $responses->getMetaTypes() );
// 		}
	
// 		public function validate( array $posts = [] )
// 		{
// 			$responses = (!empty($posts))
// 				? $this->responses( $posts )
// 				: $this->responses;
	
// 			return $responses->validate();
// 		}
	
// 		public function clearSession(bool $forced = false)
// 		{
// 			if ($forced)
// 			{
// 				$this->clear_session();
// 			}
	
// 			add_action('wp_footer', [$this, 'clear_session']);
// 		}
// 		public function clear_session()
// 		{
// 			$session = new Session($this->namespace);
// 			$session->clear($this->posttype);
		
// 			$notices = new Notices($this->namespace);
// 			$notices->clear();
// 		}
// 	}
// }