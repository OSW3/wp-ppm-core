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
            $this->core = new Core();

            // Plugin Definition
            $this->plugin = new Plugin($file);

            // WP Hooks
            register_activation_hook($file, [$this, 'activation']);
            register_deactivation_hook($file, [$this, 'deactivation']);
            add_action( 'init', [$this, 'init']);
        }

        /**
         * ----------------------------------------
         * Hooks
         * ----------------------------------------
         */

        /**
         * Hook Init the plugin
         */
        public function init()
        {
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
            new \Register\Posts($this);
            // $this->setSettings();
            new \Register\Shortcodes($this);
            // $this->setWidgets();

            if (is_admin()) 
            {
                // new \Kernel\Upgrader($this);
            }
        }


        /**
         * Hook activate the plugin
         */
        public function activation()
        {
        }

        /**
         * Hook deactivate the plugin
         */
        public function deactivation()
        {
        }

        /**
         * ----------------------------------------
         * Core & Plugin
         * ----------------------------------------
         */

        /**
         * The Core
         */
        public function getCore()
        {
            return $this->core;
        }

        /**
         * The Plugin
         */
        public function getPlugin()
        {
            return $this->plugin;
        }
    }
}
