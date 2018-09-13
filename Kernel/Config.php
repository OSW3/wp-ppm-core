<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Components\Utils\Mapper;
use \Components\Utils\Misc;
use \Components\Utils\Strings;
use \Kernel\Session;
use \Register\Actions;

if (!class_exists('Kernel\Config'))
{
    abstract class Config 
    {
        /**
         * Config Array
         * 
         * @param array
         */
        private $config;

        /**
         * Context definition
         * this is the definition in the Bootstrap file
         */
        private $definition;

        /**
         * Constructor
         */
        final public function __construct(string $bootstrap = '')
        {
            // Define the context
            $this->setContext();

            // Define the path of the Bootstrap file
            $this->setBootstrap($bootstrap);

            if ($this->isBootstrapValid())
            {
                $this->setDirectory();
                $this->setURI();
                $this->setMap();
                $this->setDefinition();

                $this->setAssets();
                $this->setAuthor();
                $this->setAuthorHTML();
                $this->setAuthorURI();
                $this->setDescription();
                $this->setEnvironment();
                $this->setDomainPath();
                $this->setFilters();
                $this->setHooks();
                $this->setImages();
                $this->setLicense();
                $this->setName();
                $this->setNamespace();
                $this->setNetwork();
                $this->setOptions();
                $this->setPluginURI();
                $this->setPosts();
                $this->setRepository();
                $this->setSettings();
                $this->setShortcodes();
                $this->setTextDomain();
                $this->setTitle();
                $this->setTitleHTML();
                $this->setVersion();
                $this->setWidgets();

                new Session($this->getConfig('namespace'));
            }
        }

        /**
         * Assets
         */
        public function setAssets()
        {
            $assets = $this->getDefinition('assets');
            $assets = is_array($assets) ? $assets : [];

            $this->addConfig('assets', $assets);

            return $this;
        }

        /**
         * Author
         */
        public function setAuthor()
        {
            $author = $this->getDefinition('Author');
            $author = strip_tags($author);

            $this->addConfig('author', $author);

            return $this;
        }

        /**
         * Author HTML
         */
        public function setAuthorHTML()
        {
            $authorHTML = $this->getDefinition('Author');

            $this->addConfig('authorHTML', $authorHTML);

            return $this;
        }

        /**
         * Author URI
         */
        public function setAuthorURI()
        {
            $authorURI = $this->getDefinition('AuthorURI');

            $this->addConfig('authorURI', $authorURI);

            return $this;
        }

        /**
         * Bootstrap
         */
        public function setBootstrap(string $bootstrap = '')
        {
            $this->addConfig('bootstrap', $bootstrap);

            return $this;
        }
        private function isBootstrapValid()
        {
            $bootstrap = $this->getConfig('bootstrap');

            // Check is not empty string
            if (empty($bootstrap))
            {
                return false;
            }

            // Check if file exist
            if (!file_exists($bootstrap))
            {
                return false;
            }

            return true;
        }

        /**
         * Configuration
         */
        public function addConfig(string $key, $data)
        {
            if (!is_array($this->config))
            {
                $this->config = array();
            }

            if (!isset($this->config[$key]))
            {
                $this->config[$key] = $data;
            }

            return $this;
        }
        public function updateConfig(string $key, $data)
        {
            if (isset($this->config[$key]))
            {
                $this->config[$key] = $data;
            }

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
         * Context
         */
        public function setContext()
        {
            $context = Misc::get_called_class_name(get_called_class());

            // Make sure the called class is an allowed context
            if (!in_array($context, ['core', 'plugin']))
            {
                $context = 'core';
            }

            // Set the context
            $this->addConfig('context', $context);

            return $this;
        }

        /**
         * Definition
         */
        private function setDefinition()
        {
            // Bootsrap Definition
            $xtra_headers = [
                'License' => 'License',
                'Repository' => 'Repository',
            ];

            $base_data = get_plugin_data($this->getConfig('bootstrap'));
            $xtra_data = get_file_data($this->getConfig('bootstrap'), $xtra_headers, 'plugin' );

            $definition = array_merge($base_data, $xtra_data);

            // Extra Config
            if ($this->hasFile(static::CONFIG_FILENAME))
            {
                include_once $this->getConfig('directory').static::CONFIG_FILENAME;

                if (isset($config) && is_array($config))
                {
                    $definition = array_merge($definition, $config);
                }
            }

            // Set Definition
            $this->definition = $definition;

            return $this;
        }
        private function getDefinition(string $key = '')
        {
            if (!empty($key) && isset($this->definition[$key]))
            {
                return $this->definition[$key];
            }

            return null;
        }

        /**
         * Context Directory
         * 
         * Define the directory of Core or Plugin
         */
        public function setDirectory()
        {
            $directory = dirname($this->getConfig('bootstrap'));
            $directory.= DIRECTORY_SEPARATOR;

            $this->addConfig('directory', $directory);

            return $this;
        }

        /**
         * Domain Path
         */
        public function setDomainpath()
        {
            $domainPath = $this->getDefinition('DomainPath');

            $this->addConfig('domainPath', $domainPath);

            return $this;
        }

        /**
         * Description
         */
        public function setDescription()
        {
            $description = $this->getDefinition('Description');

            $this->addConfig('description', $description);

            return $this;
        }

        /**
         * Environment
         */
        public function setEnvironment()
        {
            $environment = $this->getDefinition('environment');

            switch ($environment)
            {
                case 'production':
                case 'development':
                    $environment = $environment;
                    break;
                
                case 'auto':
                default: 
                    if (preg_match("/(127\.0\.Ø\.1|localhost|\.local$)/i", $_SERVER['SERVER_NAME'])) 
                    {
                        $environment = 'development';
                    }
                    else {
                        $environment = 'production';
                    }
            }

            $this->addConfig('environment', $environment);
            
            return $this;
        }

        /**
         * Filters
         */
        public function setFilters()
        {
            $filters = $this->getDefinition('filters');
            $filters = is_array($filters) ? $filters : [];

            $this->addConfig('filters', $filters);

            return $this;
        }

        /**
         * Hooks
         */
        public function setHooks()
        {
            $hooks = $this->getDefinition('hooks');
            $hooks = is_array($hooks) ? $hooks : [];

            $this->addConfig('hooks', $hooks);

            return $this;
        }

        /**
         * Images
         */
        public function setImages()
        {
            $images = $this->getDefinition('images');
            $images = is_array($images) ? $images : [];

            $this->addConfig('images', $images);

            return $this;
        }

        /**
         * License
         */
        public function setLicense()
        {
            $license = $this->getDefinition('License');

            $this->addConfig('license', $license);

            return $this;
        }

        /**
         * Map
         */
        private function setMap()
        {
            $mapper = new Mapper($this->getConfig('directory'));
            $map = $mapper->getMap();

            $this->addConfig('map', $map);

            return $this;
        }

        /**
         *  Name
         */
        public function setName()
        {
            $name = $this->getDefinition('Name');

            $this->addConfig('name', $name);

            return $this;
        }

        /**
         * Namespace
         */
        public function setNamespace()
        {
            $namespace = $this->getDefinition('namespace');

            if (null == $namespace)
            {
                $namespace = $this->getConfig('name');
            }

            $namespace = Strings::slugify($namespace, "_");

            $this->addConfig('namespace', $namespace);

            return $this;
        }

        /**
         * Network
         */
        public function setNetwork()
        {
            $network = $this->getDefinition('Network');

            $this->addConfig('network', $network);

            return $this;
        }

        /**
         * Options
         */
        public function setOptions( $data = null )
        {
            // Retrieve options from Option parameter
            $options = $this->getDefinition('options');
            $options = is_array($options) ? $options : [];

            // Retrieve options from Default values of Custom Post settings
            // TODO: Extract options from Post settings

            $this->addConfig('options', $options);

            return $this;
        }

        /**
         * Plugin URI
         */
        public function setPluginURI()
        {
            $pluginURI = $this->getDefinition('PluginURI');

            $this->addConfig('pluginURI', $pluginURI);

            return $this;
        }

        /**
         * Posts
         */
        public function setPosts($schema = null)
        {
            $posts = $this->getDefinition('posts');
            $posts = is_array($posts) ? $posts : [];

            $this->addConfig('posts', $posts);

            return $this;
        }

        /**
         * Repository
         */
        public function setRepository()
        {
            $repository = $this->getDefinition('Repository');
            $repository.= !empty($repository) ? DIRECTORY_SEPARATOR : null;

            $this->addConfig('repository', $repository);

            return $this;
        }

        /**
         * Settings
         */
        public function setSettings()
        {
            $settings = $this->getDefinition('settings');
            $settings = is_array($settings) ? $settings : [];

            $this->addConfig('settings', $settings);

            return $this;
        }

        /**
         * Shortcodes
         */
        public function setShortcodes()
        {
            $shortcodes = array();
            $_shortcodes = $this->getDefinition('shortcodes');
            $_shortcodes = is_array($_shortcodes) ? $_shortcodes : [];

            foreach ($_shortcodes as $function => $trigger) 
            {
                if (Actions::isValidFunction($function))
                {
                    $shortcodes[$function] = $trigger;
                }
            }

            $this->addConfig('shortcodes', $shortcodes);

            return $this;
        }

        /**
         * Text Domain
         */
        public function setTextDomain()
        {
            $textDomain = $this->getDefinition('TextDomain');

            $this->addConfig('textDomain', $textDomain);

            return $this;
        }

        /**
         * Title
         */
        public function setTitle()
        {
            $title = $this->getDefinition('Title');
            $title = strip_tags($title);

            $this->addConfig('title', $title);

            return $this;
        }

        /**
         * Title HTML
         */
        public function setTitleHTML()
        {
            $titleHTML = $this->getDefinition('Title');

            $this->addConfig('titleHTML', $titleHTML);

            return $this;
        }

        /**
         * Version
         */
        public function setVersion()
        {
            $version = $this->getDefinition('Version');

            $this->addConfig('version', $version);

            return $this;
        }

        /**
         * URI
         */
        public function setURI()
        {
            $uri = $this->getConfig('directory');
            $uri = preg_replace("@".WP_PLUGIN_DIR."@", WP_PLUGIN_URL, $uri);

            $this->addConfig('uri', $uri);

            return $this;
        }

        /**
         * Widgets
         */
        public function setWidgets()
        {
            $widgets = $this->getDefinition('widgets');
            $widgets = is_array($widgets) ? $widgets : [];

            $this->addConfig('widgets', $widgets);

            return $this;
        }

        // --

        /**
         * File Checking
         * 
         * @param string $file Relative path of file you want to search from the plugin root directory
         */
        public function hasFile(string $file = '')
        {
            $map = $this->getConfig('map');

            if (!empty($file) && is_array($map))
            {
                $file = $this->getConfig('directory').$file;

                foreach ($map as $item) 
                {
                    if ($item['type'] == 'file' && $item['absolute'] === $file && file_exists($file))
                    {
                        return true;
                    }
                }
            }

            return false;
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
    }
}
