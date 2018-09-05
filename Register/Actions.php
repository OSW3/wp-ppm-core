<?php

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Config;
use \Components\FileSystem as FS;

if (!class_exists('Register\Actions'))
{
	abstract class Actions
	{
        /**
         * The instance of the bootstrap class
         * 
         * @param object instance
         */
        protected $bs;

        /**
         * Formated list of Hooks
         * 
         * @param array
         */
        private $actions = array();

        /**
         * List of types
         */
        protected $types = array();

        /**
         * 
         */
        public function __construct($bs)
        {
            // Retrieve the bootstrap class instance
            $this->bs = $bs;

            // Define the type of the action
            $this->setType();

            // Set custom Posts to $this->posts register
            // $this->setPosts();

            // Add Hooks to WP Register
            $this->WP_Actions();
        }
        
        /**
         * Action Type
         */
        private function setType()
        {
            $class = get_called_class();
            $class = explode("\\", $class);

            $this->type = strtolower(end($class));
            
            return $this;
        }
        private function getType(Type $var = null)
        {
            return $this->type;
        }

        /**
         * 
         */
        protected function WP_Actions()
        {
            // Define an empty array to store formated Actions
            $_actions = array();

            // Retrieve Actions definition in Config
            $actions = $this->getActions();
            $actions = null != $actions ? $actions : [];

            // Define Actions directory
            $directory = $this->bs->getRoot().$this->getDirectory();

            // Define and format the list of Actions
            foreach ($actions as $function => $trigger) 
            {
                // Define the File name
                $filename = str_replace(FS::EXTENSION_PHP, '', $function);
                $filename.= FS::EXTENSION_PHP;

                // Define the filepath
                $filepath = $directory.$filename;

                if (file_exists($filepath) && is_file($filepath))
                {
                    $header     = $this->header($filepath);
                    $priority   = isset($header['priority']) ? $this->priority($header['priority']) : 10;
                    $args       = isset($header['params']) ? $this->args($header['params']) : 1;

                    array_push($_actions, array_merge($header, [
                        "trigger"   => $trigger,
                        "function"  => $function,
                        "filename"  => $filename,
                        "filepath"  => $filepath,
                        "priority"  => $priority,
                        "params"    => $args,
                    ]));
                }
            }

            // Add Actions to the register
            foreach ($_actions as $action)
            {
                // Include Action file
                if (file_exists($action['filepath']) && is_file($action['filepath'])) {
                    include_once $action['filepath'];
                }

                // Add Action to the register
                if (function_exists($action['function'])) {
                    
                    switch ($this->type) 
                    {
                        case 'filters':
                            add_filter(
                                $action['trigger'], 
                                $action['function'], 
                                $action['priority']
                            );
                            break;
                        
                        case 'hooks':
                            add_action(
                                $action['trigger'], 
                                $action['function'], 
                                $action['priority']
                                // TODO: add the last parameter $accepted_args to add_action
                            );
                            break;

                        case 'shortcodes':
                            add_shortcode(
                                $action['trigger'], 
                                $action['function']
                            );
                            break;
                    }
                }
            }
        }

        /**
         * 
         */
        protected function header(string $file)
        {
            $header = get_file_data($file, $this->getHeaders());

            return $header;
        }

        /**
         * 
         */
        protected function priority(string $priority)
        {
            $priority = !empty($priority) ? $priority : 10;
            $priority = is_int($priority) ? $priority : 10;

            return $priority;
        }

        /**
         * 
         */
        protected function args(string $args)
        {
            $args = explode(";", $args);

            foreach ($args as $key => $value) {
                $args[$key] = trim($value);
            }

            return $args;
        }
    }
}