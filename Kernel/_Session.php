<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Kernel\Session'))
{
	class Session
	{
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
         * Define the plugin namespace
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
         * Retrieve the PHP Session ID
         */
        public function id()
        {
            return session_id();
        }

        /**
         * Destroys the plugin session
         */
        public function destroy()
        {
            // Retrieve the plugin Namespace
            $namespace = $this->getNamespace();

            if (isset($_SESSION[$namespace])) 
            {
                unset($_SESSION[$namespace]);
            }
        }

        /**
         * Getter / Setter for Post Responses
         */
        public function responses(string $posttype, $data = null)
        {
            if (null != $data) {
                $this->set_post_data('responses', $posttype, $data);
            }
            else {
                return $this->get_post_data('responses', $posttype);
            }
        }

        /**
         * Getter / Setter for Post Errors
         */
        public function errors(string $posttype, $data = null)
        {
            if (null != $data) {
                $this->set_post_data('errors', $posttype, $data);
            }
            else {
                return $this->get_post_data('errors', $posttype);
            }
        }

        /**
         * Setter for Post data
         */
        private function set_post_data(string $type, string $posttype, $data)
        {
            // Retrieve the plugin Namespace
            $namespace = $this->getNamespace();

            // Plugin Session
            if (!isset($_SESSION[$namespace]))
            {
                $_SESSION[$namespace] = array();
            }

            // Plugins Posts
            if (!isset($_SESSION[$namespace]['posts']))
            {
                $_SESSION[$namespace]['posts'] = array();
            }
            if (!isset($_SESSION[$namespace]['posts'][$posttype]))
            {
                $_SESSION[$namespace]['posts'][$posttype] = array();
            }

            // Posts Responses
            if (!isset($_SESSION[$namespace]['posts'][$posttype][$type]))
            {
                $_SESSION[$namespace]['posts'][$posttype][$type] = array();
            }

            // Add the response
            $_SESSION[$namespace]['posts'][$posttype][$type] += $data;
        }

        /**
         * Getter for post data
         */
        private function get_post_data(string $type, string $posttype)
        {
            // Retrieve the plugin Namespace
            $namespace = $this->getNamespace();

            if (isset($_SESSION[$namespace]['posts'][$posttype][$type])) 
            {
                return $_SESSION[$namespace]['posts'][$posttype][$type];
            }

            return array();
        }

        /**
         * Clear plugin session
         */
        public function clear(string $posttype = '')
        {
            // Retrieve the plugin Namespace
            $namespace = $this->getNamespace();

            if (!empty($posttype))
            {
                if (isset($_SESSION[$namespace]['posts'][$posttype])) 
                {
                    unset($_SESSION[$namespace]['posts'][$posttype]);
                }
            }
            else
            {
                unset($_SESSION[$namespace]);
            }
        }
    }
}