<?php

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Components\Utils\Files;
use \Components\Utils\Misc;

if (!class_exists('Register\Actions'))
{
	abstract class Actions
	{
        /**
         * The instance of Kernel
         * 
         * Content instance of Core & Plugin
         * @param array
         */
        private $kernel;

        /**
         * Path of directory how stored Actions
         * 
         * @param string
         */
        private $directory;

        /**
         * Definition of merged Actions from Core & Plugin
         * 
         * @param array
         */
        private $definition;

        /**
         * 
         */
        public function __construct($kernel)
        {
            // Retrieve instance of Kernel
            $this->kernel = $kernel;

            // Define the type of the action
            $this->setType();

            // Define the directory of the action
            $this->setDirectory();

            // Action definition
            $this->setDefinition($this->kernel->getCore());
            $this->setDefinition($this->kernel->getPlugin());

            // Add Actions to the register
            $this->load();
        }

        private function load()
        {
            foreach ($this->getDefinition() as $action) 
            {
                // Include Action file
                if (file_exists($action['filepath']) && is_file($action['filepath'])) 
                {
                    include_once $action['filepath'];
                }

                // Add Action to the register
                if (function_exists($action['function'])) 
                {
                    switch ($this->getType())
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
         * Assets definition
         */
        private function setDefinition($context)
        {
            $type = $this->getType();
            $actions = $context->getConfig($type);

            if (!is_array($this->definition))
            {
                $this->definition = array();
            }

            foreach ($actions as $function => $trigger) 
            {
                // Define the Function Filename
                $filename = str_replace(Files::EXTENSION_PHP, '', $function);
                $filename.= Files::EXTENSION_PHP;

                // Define the Function relative path (from $context)
                $filename = $this->getDirectory($filename);

                // Check if function file exists
                if ($context->hasFile($filename))
                {
                    $filepath   = $context->getConfig('directory').$filename;
                    $header     = $this->header( $filepath );
                    $priority   = isset($header['priority']) ? $this->priority($header['priority']) : 10;
                    $args       = isset($header['params']) ? $this->args($header['params']) : 1;

                    array_push($this->definition, array_merge($header, [
                        "trigger"   => $trigger,
                        "function"  => $function,
                        "filename"  => $filename,
                        "filepath"  => $filepath,
                        "priority"  => $priority,
                        "params"    => $args,
                    ]));
                }
            }

            return $this;
        }
        private function getDefinition()
        {
            return $this->definition;
        }

        /**
         * Action Directory
         */
        private function setDirectory()
        {
            $directory = $this->getType();
            $directory.= DIRECTORY_SEPARATOR;

            $this->directory = $directory;
            
            return $this;
        }
        private function getDirectory(string $file = '')
        {
            if (!empty($file))
            {
                return $this->directory.$file;
            }

            return $this->directory;
        }
        
        /**
         * Action Type
         */
        private function setType()
        {
            $type = Misc::get_called_class_name(get_called_class());
            
            $this->type = $type;
            
            return $this;
        }
        private function getType()
        {
            return $this->type;
        }

        // --

        /**
         * 
         */
        private function header(string $file)
        {
            $header = get_file_data($file, static::HEADERS);

            return $header;
        }

        /**
         * 
         */
        private function priority(string $priority)
        {
            $priority = !empty($priority) ? $priority : 10;
            $priority = is_int($priority) ? $priority : 10;

            return $priority;
        }

        /**
         * 
         */
        private function args(string $args)
        {
            $args = explode(";", $args);

            foreach ($args as $key => $value) {
                $args[$key] = trim($value);
            }

            return $args;
        }
    }
}