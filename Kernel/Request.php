<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Kernel\Request'))
{
    class Request
    {
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
        public function postType()
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
         * Action
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

        /**
         * Retrieve parameter
         */
        public function get(string $param = '')
        {
            return $this->getParameter('GET', $param);
        }
        // public function post(string $param = '')
        // {
        //     return $this->getParameter('POST', $param);
        // }
        private function getParameter(string $method = 'GET', string $param = '')
        {
            if ($method === $this->getMethod() && isset($_REQUEST[$param]))
            {
                return $_REQUEST[$param];
            }

            return null;
        }

        /**
         * Retrieve request responses
         */
        public function responses()
        {
            if (isset($_REQUEST[$this->getPostType()]))
            {
                return $_REQUEST[$this->getPostType()];
            }
            //     foreach ($_REQUEST as $key => $value) 
            //     {
            //         if (preg_match("/^".$_REQUEST['post_type']."____(.+)____$/", $key, $m))
            //         {
            //             $responses += [$m[1] => $value];
            //         }
            //     }
        }

        /**
         * Retrieve request files
         */
        public function files()
        {
            if (isset($_FILES[$this->getPostType()]))
            {
                return $_FILES[$this->getPostType()];
            }
        }
    }
}