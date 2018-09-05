<?php

namespace Components;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Session;

if (!class_exists('Components\Notices'))
{
	class Notices
	{
        /**
         * Notices templates
         */
        const TMPL_NOTICE_SUCCESS   = '<div class="ppm-notice notice is-dismissible updated" id="message">$1</div>';
        const TMPL_NOTICE_WARNING   = '<div class="ppm-notice notice is-dismissible update-nag">$1</div>';
        const TMPL_NOTICE_DANGER    = '<div class="ppm-notice notice is-dismissible error">$1</div>';
        const TMPL_NOTICE_INFO      = '<div class="ppm-notice notice is-dismissible notice-info">$1</div>';

        /**
         * Plugin namespace
         * 
         * @param string
         */
        private $namespace;

        public function __construct(string $namespace)
        {
            // Set the plugin namespace
            $this->setNamespace($namespace);
        }


        /**
         * Namespace
         */
        private function setNamespace(string $namespace)
        {
            $this->namespace = $namespace;

            return $this;
        }
        private function getNamespace()
        {
            return $this->namespace;
        }

        /**
         * Set Notice
         * 
         * @param bool $global if True, the notice will be displayed globaly to 
         * WP. if false it displayed only on plugin pages
         */
        public function set(string $level, string $posttype, string $message, bool $global = false)
        {
            // Retrieve the plugin Namespace
            $namespace = $this->getNamespace();

            // type
            $type = !$global ? $posttype : 'global';

            // Plugin Session
            if (!isset($_SESSION[$namespace]))
            {
                $_SESSION[$namespace] = array();
            }

            // Plugins Notices
            if (!isset($_SESSION[$namespace]['notices']))
            {
                $_SESSION[$namespace]['notices'] = array();
            }

            // Notices Globals / Posts
            if (!isset($_SESSION[$namespace]['notices'][$type]))
            {
                $_SESSION[$namespace]['notices'][$type] = array();
            }

            // Notice Level
            if (!isset($_SESSION[$namespace]['notices'][$type][$level]))
            {
                $_SESSION[$namespace]['notices'][$type][$level] = array();
            }

            // Add message
            array_push($_SESSION[$namespace]['notices'][$type][$level], $message);
        }
        /**
         * Set Success Notice
         */
        public function success(string $posttype, string $message, bool $global = false)
        {
            $this->set('success', $posttype, $message, $global);
        }
        /**
         * Set Warning Notice
         */
        public function warning(string $posttype, string $message, bool $global = false)
        {
            $this->set('warning', $posttype, $message, $global);
        }
        /**
         * Set Danger Notice
         */
        public function danger(string $posttype, string $message, bool $global = false)
        {
            $this->set('danger', $posttype, $message, $global);
        }
        /**
         * Set Info Notice
         */
        public function info(string $posttype, string $message, bool $global = false)
        {
            $this->set('info', $posttype, $message, $global);
        }

        /**
         * Print Notices
         */
        public function get()
        {
            // Retrieve the plugin Namespace
            $namespace = $this->getNamespace();

            // Array of notices types we want to read
            $types = ['global'];

            // Define current post
            $screen = get_current_screen();
            if (isset($screen->post_type))
            {
                array_push($types, $screen->post_type);
            }

            // Notices in Session
            $notices = [];

            if (isset($_SESSION[$namespace]['notices'])) 
            {
                $notices = $_SESSION[$namespace]['notices'];
            }

            foreach ($types as $type)
            {
                if (isset($notices[$type]))
                {
                    foreach ($notices[$type] as $level => $messages)
                    {
                        if (!is_array($messages)) 
                        {
                            $messages = [$messages];
                        }

                        $message = "<ul><li>".implode("</li><li>", $messages)."</li></ul>";

                        switch ($level) 
                        {
                            case 'success':
                                echo preg_replace("/\\$1/", $message, self::TMPL_NOTICE_SUCCESS);
                                break;

                            case 'warning':
                                echo preg_replace("/\\$1/", $message, self::TMPL_NOTICE_WARNING);
                                break;

                            case 'danger':
                                echo preg_replace("/\\$1/", $message, self::TMPL_NOTICE_DANGER);
                                break;

                            case 'info':
                                echo preg_replace("/\\$1/", $message, self::TMPL_NOTICE_INFO);
                                break;
                        }
                    }
                }
            }
        }

        /**
         * Clear plugin session
         */
        public function clear()
        {
            // Retrieve the plugin Namespace
            $namespace = $this->getNamespace();

            if (isset($_SESSION[$namespace]['notices'])) 
            {
                unset($_SESSION[$namespace]['notices']);
            }
        }
    }
}