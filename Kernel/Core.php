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

if (!class_exists('Kernel\Core'))
{
    class Core
    {
        /**
         * Bootstrap File of PPM Core
         */
        const BOOTSTRAP = 'ppm-core.php';

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
        public function __construct()
        {
            // PPM base directory and file
            $this->setBootstrapFile();
            $this->setAbsoluteFilename();
            $this->setAbsoluteDirectory();
            $this->setRelativeFilename();
            $this->setRelativeDirectory();

            // Plugin Map
            $this->setMap();

            // Define the config of the plugin
            $this->setConfig();
        }

        /**
         * Bootstarp File
         */
        private function setBootstrapFile()
        {
            $dirname = dirname(__DIR__);
            $dirname.= DIRECTORY_SEPARATOR;

            $bootstrap_file = $dirname.self::BOOTSTRAP;

            $this->bootstrap_file = $bootstrap_file;

            return $this;
        }
        public function getBootstrapFile()
        {
            return $this->bootstrap_file;
        }

        /**
         * Config from Config.php
         */
        private function setConfig()
        {
            $config = new Config($this, 'core');
            $this->config = $config->getConfig();

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
    }
}
