<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

// use \Kernel\Config;
use \Kernel\Core;
use \Kernel\Plugin;
use \Kernel\Upgrader;

if (!class_exists('Kernel\Kernel'))
{
    abstract class Kernel
    {
		/**
		 * List of file excluded from the update
		 */
        // const CORE_UPGRADER_EXCLUSION = ['Kernel/Upgrader.php'];
        const CORE_UPGRADER_EXCLUSION = [];
        
        /**
         * Insance of Config
         */
        private $core;

        /**
         * Insance of Plugin
         */
        private $plugin;

        /**
         * Constructor
         */
        public function __construct(string $file = '')
        {
            // Core definition
            $this->setCore();
            // $this->core = new Core();

            // Plugin Definition
            $this->setPlugin($file);
            // $this->plugin = new Plugin($file);


            if (is_admin()) 
            {
                // Start Upgrader
                new Upgrader($this);
            }




            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->core->getConfig() );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->plugin->getConfig() );
            // echo '</pre>';

            // // $this->config->updateConfig('name', 'truc');
            // print_r( $this->config->getConfig() );
            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->getConfig('title') );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // var_dump( $this->getPlugin()->getAbsoluteFilename() );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // var_dump( $this->getPlugin()->getAbsoluteDirectory() );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // var_dump( $this->getPlugin()->getRelativeFilename() );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // var_dump( $this->getPlugin()->getRelativeDirectory() );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // var_dump( $this->getPlugin()->hasDirectory('config') );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // var_dump( $this->getPlugin()->hasFile('config/config.php') );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->getPlugin()->getMap() );
            // echo '</pre>';
        }

        // --

        /**
         * The Plugin
         */
        private function setPlugin(string $file = '')
        {
            $this->plugin = new Plugin($file);

            return $this;
        }
        public function getPlugin()
        {
            return $this->plugin;
        }

        /**
         * The Config
         */
        private function setCore()
        {
            $this->core = new Core();

            return $this;
        }
        public function getCore()
        {
            return $this->core;
        }























        // /**
        //  * String of code we need to inject in WP
        //  */
        // private $_codeInjection;

        // /**
        //  * 
        //  */
        // public function start($bs)
        // {
        //     if (empty(session_id()))
        //     {
        //         session_start();
        //     }
            
        //     // new \Kernel\Session($bs->getNamespace());
        //     new \Register\Posts($bs);
        //     new \Register\Assets($bs);
        //     new \Register\Filters($bs);
        //     new \Register\Hooks($bs);
        //     new \Register\Shortcodes($bs);
        //     // new \Register\Settings($bs);
        //     // new \Register\Widgets($bs);

        //     // Do on Admin
        //     if (is_admin()) 
        //     {
        //         new \Kernel\Updater($bs);

        //         // add_action('admin_notices', [$this, 'wp_upe_display_install_notice']);

        //     }

        //     // Do on Front
        //     else 
        //     {

        //     }
        // }


        // /**
        //  * Internationalization
        //  * 
        //  * @param array $pattern Array of WP labels index
        //  * @param array $suject Array of label index + original values
        //  * @return array of translated array $subject
        //  */
        // public function i18n($pattern, $subject)
        // {
        //     // retrieve the TextDomain identifier
        //     $textdomain = $this->getTextDomain();

        //     // Define output
        //     $output = array();

        //     foreach ($pattern as $index) {
        //         if (isset($subject[$index]) && is_string($subject[$index])) 
        //         {
        //             $output[$index] = __($subject[$index], $textdomain);
        //         }
        //     }

        //     return $output;
        // }

        // /**
        //  * Admin Header code injection
        //  */
        // public function codeInjection(string $part, string $code)
        // {
        //     $this->_codeInjection.= $code."\n";

        //     switch ($part) 
        //     {
        //         case 'head':
        //             add_action('admin_head', [$this, 'admin_head']);
        //             break;
            
        //         case 'foot':
        //             break;
        //     }
        // }

        // public function admin_head()
        // {
        //     echo $this->_codeInjection;
        // }



        // // public function wp_upe_display_install_notice() {
        // //     // Check the transient to see if we've just activated the plugin
        // //     if (get_transient($this->getNamespace())) {
        // //         echo '<div class="notice notice-success">Thanks for installing</div>';
        // //         // Delete the transient so we don't keep displaying the activation message
        // //         delete_transient($this->getNamespace());
        // //     }
        // // }
    }
}
