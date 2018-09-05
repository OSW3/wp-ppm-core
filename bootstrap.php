<?php

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

/**
 * Required files
 */
if (!function_exists('get_plugin_data'))
{
    require_once ABSPATH.'wp-admin/includes/plugin.php';
}


/**
 * Autoloader
 */
if ( !function_exists('ppm_auoload') ) {
    function ppm_auoload($class_name) 
    {
        $ppm_basedir = __DIR__.DIRECTORY_SEPARATOR;

        $class_relpath = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
        $class_relpath = $class_relpath.'.php';
        $class_filename = basename($class_relpath);

        $class_abspath = $ppm_basedir.$class_relpath;

        if (file_exists($class_abspath)) { 
            include_once $class_abspath; 
        }
    };
}
if (function_exists('spl_autoload_register')) 
{
	spl_autoload_register( 'ppm_auoload' );
}



/**
 * Required files
 */
if (!class_exists('PPM_V2'))
{
    class PPM_V2 extends \Kernel\Kernel {}
}














/* *************************** PPM PLUGINS REGISTER ************************* */

// Define the PPM Register if is not defined as an array
// if (!isset($ppm_register) || !is_array($ppm_register)) {
//     $ppm_register = array();
// }

// // Add this plugin to the PPM Register
// array_push($ppm_register, [
//     "root_file" => $ppm_root_file,
//     "root_dir" => $ppm_root_dir,
// ]);

// echo '<pre>';
// print_r($ppm_register);
// echo '</pre>';


/* ***************************** CLASS AUTOLOADING *************************** */

/**
 * Auto load PPM class files
 *
 * @param string $class Class name.
 *
 * @return void
 */
// if ( !function_exists('ppm_auoload') ) {
//     function ppm_auoload($class_name) 
//     {
//         $ppm_basedir = __DIR__.DIRECTORY_SEPARATOR;

//         $class_relpath = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
//         $class_relpath = $class_relpath.'.php';
//         $class_filename = basename($class_relpath);

//         $class_abspath = $ppm_basedir.$class_relpath;


//         if (file_exists($class_abspath)) { 
//             include_once $class_abspath; 
//         }
//         // echo '<pre>';
//         // print_r($ppm_basedir.$class_relpath);
//         // echo '</pre>';


//         // // Retrieve the relative path of the file from the plugin directory
    
//         // // Retrive the filename with extension
    
//         // // Retrive relative directory from the plugin directory
//         // $class_reldir = str_replace(basename($class_filename), '', $class_relpath);
        
//         // // Retrieve the plugin absolute directory
//         // $plugin_absdir = str_replace($class_reldir, '', dirname(__DIR__).DIRECTORY_SEPARATOR);
    
//         // // Class absolute path
//         // $class_abspath = $plugin_absdir.$class_relpath;
    
//         // if (file_exists($class_abspath)) { 
//         //     include_once $class_abspath; 
//         // }
//     };
// }
// if ( function_exists( 'spl_autoload_register' ) ) {
// 	spl_autoload_register( 'ppm_auoload' );
// }


/* *********************************** HOOKS ******************************** */

// register_activation_hook( $ppm_root_file, function()
// {
//     $ppm_root_dir = dirname(__DIR__);
//     $ppm_root_file = dirname($ppm_root_dir).DIRECTORY_SEPARATOR;
//     $ppm_root_file.= $_GET['plugin'];

//     $state = new \Kernel\State([
//         "root_file" => $ppm_root_file,
//         "root_dir" => $ppm_root_dir,
//     ]);
//     $state->activate();
// });

// register_deactivation_hook( $ppm_root_file, function()
// {
//     $ppm_root_dir = dirname(__DIR__);
//     $ppm_root_file = dirname($ppm_root_dir).DIRECTORY_SEPARATOR;
//     $ppm_root_file.= $_GET['plugin'];

//     $state = new \Kernel\State([
//         "root_file" => $ppm_root_file,
//         "root_dir" => $ppm_root_dir,
//     ]);
//     $state->deactivate();
// });

// add_action( 'init', function(){

//     global $ppm_register;

//     // Read the PPM Register
//     foreach ($ppm_register as $key => $plugin) {
//         unset($ppm_register[$key]);
        
//         // Define the plugin Bootstrap Class
//         $bs_classname = \Kernel\Kernel::getStaticNamespace($plugin);
//         $bs_class = 'class %1$s extends \Kernel\Kernel {}';
        
//         // Declare the Bootstrap Class
//         !class_exists($bs_classname) ? eval(sprintf($bs_class, $bs_classname)) : null;
        
//         // Instantiate the bootstrap class
//         $bs = new $bs_classname($plugin);
//         $bs->start($bs);
//     }

// });

