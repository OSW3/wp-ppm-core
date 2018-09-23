<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Components\Utils\Arrays;

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

        /**
         * Constructor
         * 
         * @param string $namespace of the Plugin
         */
        public function __construct(string $namespace)
        {
            $this->setNamespace($namespace);
            $this->start();
        }


        /**
         * ----------------------------------------
         * Start / Stop
         * ----------------------------------------
         */

        /**
         * Add the plugin to the session
         */
        private function start()
        {
            if (!headers_sent() && empty(session_id()))
            {
                session_start();
            }

            if (!isset($_SESSION[$this->getNamespace()]))
            {
                $_SESSION[$this->getNamespace()] = array();
            }
        }

        /**
         * Unset the plugin from the session
         */
        public function stop()
        {
            if (isset($_SESSION[$this->getNamespace()]))
            {
                unset($_SESSION[$this->getNamespace()]);
            }
        }


        /**
         * ----------------------------------------
         * Write and Read
         * ----------------------------------------
         */

        /**
         * Add data to the plugin session
         */
        public function add(string $index, $data)
        {
            if (isset($_SESSION[$this->getNamespace()]))
            {
                if (!isset($_SESSION[$this->getNamespace()][$index]))
                {
                    $_SESSION[$this->getNamespace()][$index] = $dat;
                }
            }
        }

        /**
         * 
         */
        public function push(string $index, $data)
        {
            if (isset($_SESSION[$this->getNamespace()]))
            {
                if (!isset($_SESSION[$this->getNamespace()][$index]))
                {
                    $_SESSION[$this->getNamespace()][$index] = array();
                }

                array_push($_SESSION[$this->getNamespace()][$index], $data);
            }
        }

        /**
         * 
         */
        public function pushAssoc(string $index, string $key, $data)
        {
            if (isset($_SESSION[$this->getNamespace()]))
            {
                if (!isset($_SESSION[$this->getNamespace()][$index]))
                {
                    $_SESSION[$this->getNamespace()][$index] = array();
                }

                if (!isset($_SESSION[$this->getNamespace()][$index][$key]))
                {
                    $_SESSION[$this->getNamespace()][$index][$key] = array();
                }

                $_SESSION[$this->getNamespace()][$index][$key] = array_merge(
                    $_SESSION[$this->getNamespace()][$index][$key],
                    $data
                );
            }
        }

        /**
         * Read the plugin from the session
         */
        public function read($dimensions = null)
        {
            if (isset($_SESSION[$this->getNamespace()]))
            {
                if ($dimensions === null)
                {
                    return $_SESSION[$this->getNamespace()];
                }
                else
                {
                    if (!is_array($dimensions))
                    {
                        $dimensions = [$dimensions];
                    }

                    return Arrays::search($_SESSION[$this->getNamespace()], $dimensions);
                }
            }
        }

        /**
         * ----------------------------------------
         * 
         * ----------------------------------------
         */

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
         * ----------------------------------------
         * Responses / Errors / Shortcodes
         * ----------------------------------------
         */

        /**
         * Responses
         */
        public function addResponses(string $posttype, array $responses)
        {
            $this->pushAssoc(
                'responses', 
                $posttype,
                $responses
            );
        }
        public function readResponses(string $posttype)
        {
            if ($responses = $this->read([ 'responses', $posttype ]))
            {
                return $responses;
            }

            return array();
        }

        /**
         * Errors
         */
        public function addErrors(string $posttype, array $errors)
        {
            $this->pushAssoc(
                'errors', 
                $posttype,
                $errors
            );
        }
        public function readErrors(string $posttype)
        {
            if ($responses = $this->read([ 'errors', $posttype ]))
            {
                return $responses;
            }

            return array();
        }

        /**
         * Shortcodes
         */
        public function addShortcode(string $posttype, array $type)
        {
            $this->pushAssoc(
                'shortcodes', 
                $posttype,
                [$type['key'] => $type]
            );
        }
        public function readShortcode($dimensions)
        {
            if (!is_array($dimensions))
            {
                $dimensions = [$dimensions];
            }

            return $this->read(array_merge(['shortcodes'], $dimensions));
        }

        /**
         * Getter / Setter for Post Responses
         */
        // public function responses(string $posttype, $data = null)
        // {
        //     if (null != $data) {
        //         // $this->set_post_data('responses', $posttype, $data);
        //         $this->pushAssoc('posts', $posttype, ['responses' => $data]);
        //     }
        //     else {
        //         return $this->get_post_data('responses', $posttype);
        //     }
        // }


        /**
         * ----------------------------------------
         * Flashbag messages
         * ----------------------------------------
         */













        // public function __construct(string $namespace)
        // {
        //     // Set the plugin namespace
        //     $this->setNamespace($namespace);
        // }


        // /**
        //  * Retrieve the PHP Session ID
        //  */
        // public function id()
        // {
        //     return session_id();
        // }

        // /**
        //  * Destroys the plugin session
        //  */
        // public function destroy()
        // {
        //     // Retrieve the plugin Namespace
        //     $namespace = $this->getNamespace();

            // if (isset($_SESSION[$namespace])) 
        //     {
        //         unset($_SESSION[$namespace]);
        //     }
        // }

        // /**
        //  * Getter / Setter for Post Responses
        //  */
        // public function responses(string $posttype, $data = null)
        // {
        //     if (null != $data) {
        //         $this->set_post_data('responses', $posttype, $data);
        //     }
        //     else {
        //         return $this->get_post_data('responses', $posttype);
        //     }
        // }

        // /**
        //  * Getter / Setter for Post Errors
        //  */
        // public function errors(string $posttype, $data = null)
        // {
        //     if (null != $data) {
        //         $this->set_post_data('errors', $posttype, $data);
        //     }
        //     else {
        //         return $this->get_post_data('errors', $posttype);
        //     }
        // }

        /**
         * Setter for Post data
         */
        // private function set_post_data(string $type, string $posttype, $data)
        // {
        //     // Retrieve the plugin Namespace
        //     $namespace = $this->getNamespace();

        //     // Plugin Session
        //     if (!isset($_SESSION[$namespace]))
        //     {
        //         $_SESSION[$namespace] = array();
        //     }

        //     // Plugins Posts
        //     if (!isset($_SESSION[$namespace]['posts']))
        //     {
        //         $_SESSION[$namespace]['posts'] = array();
        //     }
        //     if (!isset($_SESSION[$namespace]['posts'][$posttype]))
        //     {
        //         $_SESSION[$namespace]['posts'][$posttype] = array();
        //     }

        //     // Posts Responses
        //     if (!isset($_SESSION[$namespace]['posts'][$posttype][$type]))
        //     {
        //         $_SESSION[$namespace]['posts'][$posttype][$type] = array();
        //     }

        //     // Add the response
        //     $_SESSION[$namespace]['posts'][$posttype][$type] += $data;
        // }

        // /**
        //  * Getter for post data
        //  */
        // private function get_post_data(string $type, string $posttype)
        // {
        //     // Retrieve the plugin Namespace
        //     $namespace = $this->getNamespace();

            // if (isset($_SESSION[$namespace]['posts'][$posttype][$type])) 
        //     {
        //         return $_SESSION[$namespace]['posts'][$posttype][$type];
        //     }

        //     return array();
        // }

        // /**
        //  * Clear plugin session
        //  */
        // public function clear(string $posttype = '')
        // {
        //     // Retrieve the plugin Namespace
        //     $namespace = $this->getNamespace();

        //     if (!empty($posttype))
        //     {
                // if (isset($_SESSION[$namespace]['posts'][$posttype])) 
        //         {
        //             unset($_SESSION[$namespace]['posts'][$posttype]);
        //         }
        //     }
        //     else
        //     {
        //         unset($_SESSION[$namespace]);
        //     }
        // }
    }
}