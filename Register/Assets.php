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
use \Components\Utils\Files;

if (!class_exists('Register\Assets'))
{
	class Assets
	{
        /**
         * Directories
         */
        const DIRECTORY_ASSETS  = "Assets/";
        const DIRECTORY_IMAGES  = self::DIRECTORY_ASSETS."images/";
        const DIRECTORY_SCRIPTS = self::DIRECTORY_ASSETS."scripts/";
        const DIRECTORY_STYLES  = self::DIRECTORY_ASSETS."styles/";

        /**
         * Definition of merged assets from Core & Plugin
         * 
         * @param array
         */
        private $definition;

        /**
         * The instance of Kernel
         * 
         * Content instance of Core & Plugin
         * @param array
         */
        private $kernel;

        /**
         * Constructor
         */
        public function __construct($kernel)
        {
            // Retrieve instance of Kernel
            $this->kernel = $kernel;

            // Assets definition
            $this->setDefinition($this->kernel->getCore());
            $this->setDefinition($this->kernel->getPlugin());

            // Enqueue Assets
            add_action('admin_enqueue_scripts', [$this, 'load_styles']);
            add_action('admin_enqueue_scripts', [$this, 'load_scripts']);
        }

        /**
         * Assets definition
         */
        private function setDefinition($context)
        {
            $definition = array();
            $assets = $context->getConfig('assets');

            if (!is_array($this->definition))
            {
                $this->definition = array();
            }

            // Group by parts (frontend or admin)
            foreach ($assets as $key => $value) 
            {
                if (!isset($definition['frontend']))
                {
                    $definition['frontend'] = array();
                }
                if (!isset($definition['admin']))
                {
                    $definition['admin'] = array();
                }

                if (!empty($value))
                {
                    switch ($key)
                    {
                        case 'frontend':
                        case 'admin':
                            array_push($definition[$key], $value);
                            break;
    
                        case 'both':
                            array_push($definition['frontend'], $value);
                            array_push($definition['admin'], $value);
                            break;
                    }
                }
            }

            // Group by type
            foreach ($definition as $part => $assets) 
            {
                foreach ($assets as $key => $types) 
                {
                    foreach ($types as $type => $items) 
                    {
                        if (!isset($this->definition[$part][$type]))
                        {
                            $this->definition[$part][$type] = array();
                        }

                        foreach ($items as $item) 
                        {
                            if ($item = $this->formatItem($context, $type, $item))
                            {
                                $this->definition[$part][$type][$item['handle']] = $item;
                            }
                        }
                    }
                    unset($this->definition[$part][$key]);
                }
            }

            return $this;
        }
        private function getDefinition(string $type)
        {
            $definition = null;

            if (is_admin() && isset($this->definition['admin']))
            {
                $definition = $this->definition['admin'];
            }
            elseif (isset($this->definition['frontend']))
            {
                $definition = $this->definition['frontend'];
            }

            if (isset($definition[$type]))
            {
                return $definition[$type];
            }

            return $definition;
        }

        /**
         * Check and redefine an asset
         */
        private function formatItem($context, string $type, array $item)
        {
            $extension = null;

            if ($type == 'scripts') 
            {
                $extension = Files::EXTENSION_JS;
            }
            elseif ($type == 'styles') 
            {
                $extension = Files::EXTENSION_CSS;
            }

            // Check Handle
            if (!isset($item['handle']) || empty($item['handle']))
            {
                $itme['handle'] = uniqid();
            }

            // Check dependencies
            if (!isset($item['dependencies']) || empty($item['dependencies']))
            {
                $itme['dependencies'] = [];
            }

            // Check In Header
            if (!isset($item['in_header']) || !is_bool($item['in_header']))
            {
                $itme['in_header'] = false;
            }

            // Check enqueue
            if (!isset($item['enqueue']) || !is_bool($item['enqueue']))
            {
                $itme['enqueue'] = true;
            }

            // Check version
            if (!isset($item['version']))
            {
                $itme['version'] = null;
            }

            // Source not defined
            if (!isset($item['src']) || empty($item['src']))
            {
                return false;
            }

            // Source file is not CDN
            if (!filter_var($item['src'], FILTER_VALIDATE_URL))
            {
                $item['src'] = str_replace($extension, '', $item['src']);
                $item['src'].= $extension;

                // Check file exists
                if ($context->hasFile($item['src']))
                {
                    $item['src'] = $context->getConfig('uri').$item['src'];
                }
                else
                {
                    return false;
                }
            }

            return $item;
        }

        // -- Hooks

        public function load_styles()
        {
            foreach ($this->getDefinition("styles") as $asset) 
            {
                // Add style to register
                wp_register_style(
                    $asset['handle'], 
                    $asset['src'], 
                    $asset['dependencies'], 
                    $asset['version'] 
                );

                // Enqueue style
                if ($asset['enqueue']) 
                {
                    wp_enqueue_style($asset['handle']);
                }
            }
        }

        public function load_scripts()
        {
            foreach ($this->getDefinition("scripts") as $asset) 
            {
                // Add Script to register
                wp_register_script(
                    $asset['handle'], 
                    $asset['src'], 
                    $asset['dependencies'], 
                    $asset['version'],
                    !$asset['in_header'] 
                );

                // Enqueue script
                if ($asset['enqueue']) 
                {
                    wp_enqueue_script($asset['handle']);
                }
            }
        }
    }
}