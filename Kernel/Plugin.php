<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Mapper;
use \Kernel\Config;

if (!class_exists('Kernel\Plugin'))
{
    class Plugin
    {
        const FILE_CONFIG = 'config/config.php';

        /**
         * Bootstrap file
         * 
         * @param string
         */
        private $bootstrap_file;

        /**
         * Config arrays from Config.php
         * 
         * @param array
         */
        private $config;

        /** 
         * Is bootstrap file valid
         * 
         * @param boolean
         */
        private $is_bootstrap_file_valid;

        /**
         * Absolute file path of the plugin
         * 
         * @param string
         */
        private $abs_plugin_filename;

        /**
         * Absolute directory of the plugin
         * 
         * @param string
         */
        private $abs_plugin_directory;

        /**
         * Relative file path of the plugin
         * 
         * @param string
         */
        private $rel_plugin_filename;

        /**
         * Relative directory of the plugin
         * 
         * @param string
         */
        private $rel_plugin_directory;

        /**
         * Map of plugin
         */
        private $map;

        /**
         * Constructor
         */
        public function __construct(string $file = '')
        {
            $this->setBootstrapFile($file);

            if ($this->isBootstrapValid())
            {
                // Plugin base directory and file
                $this->setAbsoluteFilename();
                $this->setAbsoluteDirectory();
                $this->setRelativeFilename();
                $this->setRelativeDirectory();

                // Plugin Map
                $this->setMap();

                // Define the config of the plugin
                $this->setConfig();
            }
        }


        /**
         * Bootstarp File
         */
        private function setBootstrapFile(string $file = '')
        {
            $this->bootstrap_file = $file;

            return $this;
        }
        public function getBootstrapFile()
        {
            return $this->bootstrap_file;
        }

        /**
         * Bootstarp File Validation
         */
        private function isBootstrapValid()
        {
            // Check is not empty string
            if (empty($this->bootstrap_file))
            {
                return false;
            }

            // Check if file exist
            if (!file_exists($this->bootstrap_file))
            {
                return false;
            }

            return true;
        }

        /**
         * Config from Config.php
         */
        private function setConfig()
        {
            $this->config = array();

            if ($this->hasFile(self::FILE_CONFIG))
            {
                include_once $this->getAbsoluteDirectory().self::FILE_CONFIG;

                if (isset($config) && is_array($config))
                {
                    $this->config = array_merge($this->config, $config);
                }
            }

            // Instance of Config
            $iConfig = new Config($this, 'plugin');
            $this->config = $iConfig->getConfig();

            return $this;
        }
        public function getConfig(string $key = '')
        {
            if (!empty($key))
            {
                if (isset($this->config[$key]))
                {
                    return $this->config[$key];
                }

                return null;
            }

            ksort($this->config);
            return $this->config;
        }

        /**
         * Absolute plugin Filename
         */
        private function setAbsoluteFilename()
        {
            $this->abs_plugin_filename = $this->getBootstrapFile();

            return $this;
        }
        public function getAbsoluteFilename()
        {
            return $this->abs_plugin_filename;
        }
        public function getRootFile()
        {
            return $this->getAbsoluteFilename();
        }

        /**
         * Absolute plugin Directory
         */
        private function setAbsoluteDirectory()
        {
            $dirname = dirname($this->getBootstrapFile());
            $dirname.= DIRECTORY_SEPARATOR;
            $this->abs_plugin_directory = $dirname;

            return $this;
        }
        public function getAbsoluteDirectory()
        {
            return $this->abs_plugin_directory;
        }

        /**
         * Relative plugin Filename
         */
        private function setRelativeFilename()
        {
            $filename = str_replace(WP_PLUGIN_DIR, "", $this->getBootstrapFile());
            $this->rel_plugin_filename = $filename;

            return $this;
        }
        public function getRelativeFilename()
        {
            return $this->rel_plugin_filename;
        }

        /**
         * Relative plugin Directory
         */
        private function setRelativeDirectory()
        {
            $filename = str_replace(WP_PLUGIN_DIR, "", $this->getBootstrapFile());
            $dirname = dirname($filename);
            $dirname.= DIRECTORY_SEPARATOR;
            $this->rel_plugin_directory = $dirname;

            return $this;
        }
        public function getRelativeDirectory()
        {
            return $this->rel_plugin_directory;
        }

        /**
         * Map of plugin
         */
        private function setMap()
        {
            $mapper = new Mapper($this->getAbsoluteDirectory());
            $this->map = $mapper->getMap();

            return $this;
        }
        public function getMap()
        {
            return $this->map;
        }

        /**
         * Directory Checking
         * 
         * @param string $directory Relative path of directory you want to search from the plugin root directory
         */
        public function hasDirectory(string $directory = '')
        {
            if (!empty($directory) && is_array($this->getMap()))
            {
                $directory = preg_replace('/\/$/', '', $directory);
                $directory.= DIRECTORY_SEPARATOR;
                $directory = $this->getAbsoluteDirectory().$directory;

                foreach ($this->getMap() as $item) 
                {
                    if ($item['type'] == 'directory' && $item['absolute'] === $directory && is_dir($directory))
                    {
                        return true;
                    }
                }
            }
            
            return false;
        }

        /**
         * File Checking
         * 
         * @param string $file Relative path of file you want to search from the plugin root directory
         */
        public function hasFile(string $file = '')
        {
            if (!empty($file) && is_array($this->getMap()))
            {
                $file = $this->getAbsoluteDirectory().$file;

                foreach ($this->getMap() as $item) 
                {
                    if ($item['type'] == 'file' && $item['absolute'] === $file && file_exists($file))
                    {
                        return true;
                    }
                }
            }

            return false;
        }
    }
}
