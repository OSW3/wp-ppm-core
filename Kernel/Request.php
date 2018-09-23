<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Register\Posts;
use \Components\Utils\Arrays;

if (!class_exists('Kernel\Request'))
{
    class Request
    {
        /**
         * Action parameter
         */
        private $action;


        public function __construct()
        {
            $this->setAction();
        }


        /**
         * ----------------------------------------
         * Retrieve request data
         * ----------------------------------------
         */
        
        /**
         * Return respons of Custom Post form
         * 
         * @param string $type of the response ('both', 'request' or 'files')
         *      'request' for $_POST data only
         *      'files' for $_FILES data only
         *      'both' for $_POST data and $_FILES
         * @return array
         */
        public function responses(string $type = 'both')
        {
            $posttype   = $this->posttype();
            $request    = $this->request($posttype);
            $files      = $this->files($posttype);
            
            switch ($type) 
            {
                case 'request':
                    $responses = $request;
                    break;
                
                case 'files':
                    $responses = $files;
                    break;
                    
                case 'both':                
                default:
                    $responses = array_merge($request, $files);
                    break;
            }

            return $responses;
        }
        
        /**
         * Retrieve Data from POST
         * 
         * @param string $key of request parameter
         * @return misc
         */
        public function request(string $key = '')
        {
            return $this->getParameters('POST', $key);
        }
        
        /**
         * Retrieve Data from GET
         * 
         * @param string $key of request parameter
         * @return misc
         */
        public function get(string $key = '')
        {
            return $this->getParameters('GET', $key);
        }
        
        /**
         * Retrieve Data from FILES
         * 
         * @param string $key of request parameter
         * @return misc
         */
        public function files(string $posttype = '')
        {
            $files = array();

            foreach ($this->getParameters('FILES', $posttype) as $key => $types) 
            {
                foreach ($types as $type_key => $value) 
                {
                    if (!is_array($value))
                    {
                        $value = [$value];
                    }

                    if (!isset($files[$type_key]))
                    {
                        $files[$type_key] = array();
                    }

                    // If $value is an associative array, it's certainly 
                    // provided by a Collection
                    if (is_array($value) && !Arrays::isNumeric($value))
                    {
                        foreach ($value as $cType => $cValue) 
                        {
                            if (!isset($files[$type_key][$cType]))
                            {
                                $files[$type_key][$cType] = array();
                            }

                            foreach ($cValue as $serial => $itemValue) 
                            {
                                if (!isset($files[$type_key][$cType][$serial]))
                                {
                                    $files[$type_key][$cType][$serial] = array();
                                }

                                foreach ($itemValue as $k => $v) 
                                {
                                    if (!isset($files[$type_key][$cType][$serial][$k]))
                                    {
                                        $files[$type_key][$cType][$serial][$k] = array();
                                    }

                                    $files[$type_key][$cType][$serial][$k] = array_merge($files[$type_key][$cType][$serial][$k],[$key => $v]);

                                    // foreach (Posts::COLLECTION_VPOST as $_data) 
                                    // {
                                    //     $files[$type_key][$cType][$serial][$k] = array_merge($files[$type_key][$cType][$serial][$k],[$_data => $_POST[$posttype][$type_key][$_data][$serial]]);
                                    // }
                                }
                            }
                            
                            foreach (Posts::COLLECTION_VPOST as $_data) 
                            {
                                $files[$type_key][$_data] = $_POST[$posttype][$type_key][$_data];
                            }
                        }
                    }
                    
                    elseif (is_array($value)) 
                    {
                        foreach ($value as $k => $v) 
                        {
                            if (!isset($files[$type_key][$k]))
                            {
                                $files[$type_key][$k] = array();
                            }

                            $files[$type_key][$k] = array_merge($files[$type_key][$k],[$key => $v]);
                        }
                    }
                }
            }

            // echo '<pre style="padding-left: 180px;">';
            // print_r( __FILE__."  - Line: ".__LINE__." " );
            // print_r( $files );
            // echo '</pre>';
            // echo '<hr>';
            return $files;
        }

        /**
         * Retrieve Requests parameters
         */
        private function getParameters(string $method = 'GET', string $key = '')
        {
            $data = [];

            switch ($method) 
            {
                case 'POST':
                    $data = $_POST;
                    break;

                case 'GET':
                    $data = $_GET;
                    break;

                case 'FILES':
                    $data = $_FILES;
                    break;
            }


            if (isset($data[$key]))
            {
                return $data[$key];
            }

            return $data;
        }


        /**
         * ----------------------------------------
         * Method
         * ----------------------------------------
         */

        /**
         * Return the Method of the request
         * 
         * @param bool $tolower True to set response with lower case
         * @return string
         */
        public function method(bool $tolower = false)
        {
            $method = strtoupper($_SERVER['REQUEST_METHOD']);

            if ($tolower)
            {
                $method = strtolower($method);
            }

            return $method;
        }

        /**
         * Return true if the request is POST
         * 
         * @return bool
         */
        public function isPost()
        {
            return 'POST' === $this->method();
        }

        /**
         * Return true if the request is GET
         * 
         * @return bool
         */
        public function isGet()
        {
            return 'GET' === $this->method();
        }


        /**
         * ----------------------------------------
         * Posts
         * ----------------------------------------
         */

        /**
         * Retrieve a post parameter
         * 
         * @return misc
         */
        public function post(string $key = '')
        {
            switch ($key) 
            {
                case 'id':      return $this->postID();
                case 'type':    return $this->postType();
            }
        }

        /**
         * Retrieve the post Type
         */
        public function posttype()
        {
            if (isset($_REQUEST['post_type']))
            {
                return $_REQUEST['post_type'];
            }
            elseif (isset($_GET['post']))
            {
                return get_post_type($_GET['post']);
            }

            return null;
        }

        /**
         * Retrieve the post ID
         */
        public function postID()
        {
            if (isset($_GET['post']))
            {
                return $_GET['post'];
            }

            return null;
        }


        /**
         * ----------------------------------------
         * Redirect
         * ----------------------------------------
         */

        /**
         * Redirect to a destination
         * 
         * @param string $destination
         */
        public function redirect(string $destination = 'home')
        {
            if ($destination === 'home')
            {
                $destination = is_admin() ? admin_url() : home_url();
            }

            header("location: ".$destination);
            exit;
        }


        /**
         * ----------------------------------------
         * Referer
         * ----------------------------------------
         */

        /**
         * @return string
         */
        public function referer()
        {
            // Default referer for Admin or Front
            $referer = is_admin() ? admin_url() : home_url();

            if (isset($_SERVER['HTTP_REFERER']))
            {
                $referer = $_SERVER['HTTP_REFERER'];
            }

            return $referer;
        }


        /**
         * ----------------------------------------
         * Action
         * ----------------------------------------
         */

        private function setAction()
        {
            if (isset($_GET['action']))
            {
                $this->action = $_GET['action'];
            }

            return $this;
        }
        public function getAction()
        {
            return $this->action;
        }

        public function isActionEdit()
        {
            return 'edit' === $this->getAction();
        }
        public function isActionTrash()
        {
            return 'trash' === $this->getAction();
        }
        public function isActionUntrash()
        {
            return 'untrash' === $this->getAction();
        }
        public function isActionDelete()
        {
            return 'delete' === $this->getAction();
        }
        public function isActionUpdate()
        {
            return 'update' === $this->getAction();
        }
    }
}