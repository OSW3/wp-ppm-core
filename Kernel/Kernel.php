<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Core;
use \Kernel\Plugin;

// use \Register\Posts;
// use \Register\Settings;
// use \Register\Widgets;

if (!class_exists('Kernel\Kernel'))
{
    abstract class Kernel
    {
		/**
		 * List of file excluded from the update
		 */
        const CORE_UPGRADER_EXCLUSION = ['Kernel/Upgrader.php'];
        
        /**
         * Insance of Assets
         */
        // private $assets;
        
        /**
         * Insance of Config
         */
        private $core;
        
        /**
         * Insance of Filters
         */
        // private $filters;
        
        /**
         * Insance of Hooks
         */
        // private $hooks;

        /**
         * Insance of Plugin
         */
        private $plugin;

        /**
         * Insance of Posts
         */
        private $posts;

        /**
         * Insance of Settings
         */
        private $settings;

        /**
         * Insance of Shortcodes
         */
        // private $shortcodes;

        /**
         * Insance of Widgets
         */
        private $widgets;

        /**
         * Constructor
         */
        public function __construct(string $file = '')
        {
            // Core definition
            $this->core = new Core();
            // $this->setCore();

            // Plugin Definition
            $this->plugin = new Plugin($file);
            // $this->setPlugin($file);

            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->core->getConfig('uri') );
            // echo '</pre>';

            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->plugin->getConfig() );
            // echo '</pre>';

            // exit;


            new \Register\Assets($this);
            new \Register\Filters($this);
            new \Register\Hooks($this);
            // $this->setPosts();
            // $this->setSettings();
            new \Register\Shortcodes($this);
            // $this->setWidgets();

            if (is_admin()) 
            {
                // new \Kernel\Upgrader($this);
            }




            // echo '<pre style="padding-left: 180px;">';
            // // print_r( $this->plugin->getConfig('name') );
            // print_r( $this->plugin->getConfig('hooks') );
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
         * The Core
         */
        public function getCore()
        {
            return $this->core;
        }

        /**
         * Filters
         */
        // private function setFilters()
        // {
        //     $this->filters = new Filters();

        //     return $this;
        // }
        // public function getFilters()
        // {
        //     return $this->filters;
        // }

        /**
         * Hooks
         */
        // private function setHooks()
        // {
        //     $this->hooks = new Hooks();

        //     return $this;
        // }
        // public function getHooks()
        // {
        //     return $this->hooks;
        // }

        /**
         * The Plugin
         */
        public function getPlugin()
        {
            return $this->plugin;
        }

        /**
         * Posts
         */
        private function setPosts()
        {
            $this->posts = new Posts();

            return $this;
        }
        public function getPosts()
        {
            return $this->posts;
        }

        /**
         * Settings
         */
        private function setSettings()
        {
            $this->settings = new Settings();

            return $this;
        }
        public function getSettings()
        {
            return $this->settings;
        }

        /**
         * Shortcodes
         */
        // private function setShortcodes()
        // {
        //     $this->shortcodes = new Shortcodes();

        //     return $this;
        // }
        // public function getShortcodes()
        // {
        //     return $this->shortcodes;
        // }

        /**
         * Widgets
         */
        private function setWidgets()
        {
            $this->widgets = new Widgets;

            return $this;
        }
        public function getWidgets()
        {
            return $this->widgets;
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
