<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Kernel\Config'))
{
    class Config 
    {
        /**
         * Array of plugin assets
         * 
         * @param array
         */
        private $assets;

        /**
         * the plugin author name
         * 
         * @param string
         */
        private $author;

        /**
         * the plugin author HTML
         * 
         * @param string
         */
        private $authorHTML;

        /**
         * the plugin author URI
         * 
         * @param string
         */
        private $authorURI;

        /**
         * full config Definition both Bootstrap file and Config.php
         * 
         * @param array
         */
        private $config;

        /**
         * The context of 'core' or 'plugin'
         * 
         * @param array
         */
        private $context;

        /**
         * Plugin definition
         * thisis the definition in the Bootstrap plugin file
         */
        private $bootstrapDefinition;
        private $pluginDefinition;

        /**
         * the plugin description
         * 
         * @param string
         */
        private $description;

        /**
         * the plugin domain path
         * 
         * @param string
         */
        private $domainPath;

        /**
         * The execution environment
         * 
         * @param string
         */
        private $environment;

        /**
         * Array of plugin filters
         * 
         * @param array
         */
        private $filters;

        /**
         * Array of plugin hooks
         * 
         * @param array
         */
        private $hooks;

        /**
         * Array of plugin images sizes
         * 
         * @param array
         */
        private $images;

        /**
         * The instance of Core or Plugin
         * 
         * @param array
         */
        private $instance;

        /**
         * the plugin License
         * 
         * @param string
         */
        private $license;

        /**
         * The plugin name
         * 
         * @param string
         */
        private $name;

        /**
         * The plugin namespace
         * 
         * @param string
         */
        private $namespace;

        /**
         * the plugin network
         * 
         * @param string
         */
        private $network;

        /**
         * Array of plugin options
         * 
         * @param array
         */
        private $options;

        /**
         * Instance of Plugin
         * 
         * @param array
         */
        private $plugin;

        /**
         * the plugin uri
         * 
         * @param string
         */
        private $pluginURI;

        /**
         * Array of plugin posts
         * 
         * @param array
         */
        private $posts;

        /**
         * Repository address
         * 
         * @param array
         */
        private $repository;

        /**
         * Array of settings
         * 
         * @param array
         */
        private $settings;

        /**
         * Array of plugin shortcodes
         * 
         * @param array
         */
        private $shortcodes;

        /**
         * the plugin text-domain
         * 
         * @param string
         */
        private $textDomain;

        /**
         * the plugin title
         * 
         * @param string
         */
        private $title;

        /**
         * the plugin title html
         * 
         * @param string
         */
        private $titleHTML;

        /**
         * the version number of the plugin
         * 
         * @param string
         */
        private $version;

        /**
         * Array of plugin widgets
         * 
         * @param array
         */
        private $widgets;

        /**
         * Constructor
         */
        public function __construct($instance, string $context = 'core')
        {
            // Retrieve core or plugin instance
            $this->setInstance($instance);

            // Define the context
            $this->setContext($context);

            // Retrive the Bootstrap Definition
            $this->setBootstrapDefinition();
            
            // Retrieve Plugin Config Array
            if ('plugin' == $this->getContext())
            {
                $this->setPluginDefinition();
            }

            // Set full configuration
            $this->setConfig();
        }

        /**
         * Assets
         */
        public function setAssets()
        {
            // TODO: Check assets validity

            $this->assets = $this->getPluginDefinition('assets');

            $this->addConfig('assets', $this->assets);

            return $this;
        }
        public function getAssets()
        {
            return $this->assets;
        }

        /**
         * Author
         */
        public function setAuthor()
        {
            $author = $this->getBootstrapDefinition('Author');

            $this->author = strip_tags($author);

            $this->addConfig('author', $this->author);

            return $this;
        }
        public function getAuthor()
        {
            return $this->author;
        }

        /**
         * Author HTML
         */
        public function setAuthorHTML()
        {
            $this->authorHTML = $this->getBootstrapDefinition('Author');

            $this->addConfig('authorHTML', $this->authorHTML);

            return $this;
        }
        public function getAuthorHTML()
        {
            return $this->authorHTML;
        }

        /**
         * Author URI
         */
        public function setAuthorURI()
        {
            $this->authorURI = $this->getBootstrapDefinition('AuthorURI');

            $this->addConfig('authorURI', $this->authorURI);

            return $this;
        }
        public function getAuthorURI()
        {
            return $this->authorURI;
        }

        /**
         * Configuration
         */
        private function setConfig()
        {
            $this->config = array();

            // Set elements of configuration
            $this->setAuthor();
            $this->setAuthorHTML();
            $this->setAuthorURI();
            $this->setDescription();
            $this->setDomainPath();
            $this->setLicense();
            $this->setName();
            $this->setNetwork();
            $this->setPluginURI();
            $this->setRepository();
            $this->setTextDomain();
            $this->setTitle();
            $this->setTitleHTML();
            $this->setVersion();

            if ('plugin' == $this->getContext())
            {
                $this->setAssets();
                $this->setEnvironment();
                $this->setFilters();
                $this->setHooks();
                $this->setImages();
                $this->setNamespace();
                $this->setOptions();
                $this->setPosts();
                $this->setSettings();
                $this->setShortcodes();
                $this->setWidgets();
            }

            return $this;
        }
        public function addConfig(string $key, $data)
        {
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
        public function setContext(string $context)
        {
            if (!in_array($context, ['core', 'plugin']))
            {
                $context = 'core';
            }

            $this->context = $context;

            return $this;
        }
        public function getContext()
        {
            return $this->context;
        }

        /**
         * Plugin definition
         */
        private function setBootstrapDefinition()
        {
            $this->bootstrapDefinition = array_merge(
                get_plugin_data($this->getInstance()->getRootFile()),
                get_file_data($this->getInstance()->getRootFile(), [
                    'License' => 'License',
                    'Repository' => 'Repository',
                ], 'plugin' )
            );

            return $this;
        }
        private function getBootstrapDefinition(string $key = '')
        {
            if (!empty($key) && isset($this->bootstrapDefinition[$key]))
            {
                return $this->bootstrapDefinition[$key];
            }

            return null;
        }
        private function setPluginDefinition()
        {
            $this->pluginDefinition = $this->getInstance()->getConfig();

            return $this;
        }
        private function getPluginDefinition(string $key = '')
        {
            if (!empty($key) && isset($this->pluginDefinition[$key]))
            {
                return $this->pluginDefinition[$key];
            }

            return null;
        }

        /**
         * Domain Path
         */
        public function setDomainpath()
        {
            $this->domainPath = $this->getBootstrapDefinition('DomainPath');

            $this->addConfig('domainPath', $this->domainPath);

            return $this;
        }
        public function getDomainPath()
        {
            return $this->domainPath;
        }

        /**
         * Description
         */
        public function setDescription()
        {
            $this->description = $this->getBootstrapDefinition('Description');

            $this->addConfig('description', $this->description);

            return $this;
        }
        public function getDescription()
        {
            return $this->description;
        }

        /**
         * Environment
         */
        public function setEnvironment()
        {
            $environment = $this->getPluginDefinition('environment');

            switch ($environment)
            {
                case 'production':
                case 'development':
                    $this->environment = $environment;
                    break;
                
                case 'auto':
                default: 
                    if (preg_match("/(127\.0\.Ø\.1|localhost|\.local$)/i", $_SERVER['SERVER_NAME'])) 
                    {
                        $this->environment = 'development';
                    }
                    else {
                        $this->environment = 'production';
                    }
            }

            $this->addConfig('environment', $this->environment);
            
            return $this;
        }
        public function getEnvironment()
        {
            return $this->environment;
        }

        /**
         * Filters
         */
        public function setFilters()
        {
            $this->filters = array();
            
            $filters = $this->getPluginDefinition('filters');

            if (is_array($filters))
            {
                // TODO: Check if valid filter (array declaration have file)
                $this->filters = array_merge($this->filters, $filters);
            }

            $this->addConfig('filters', $this->filters);

            return $this;
        }
        public function getFilters()
        {
            return $this->filters;
        }

        /**
         * Hooks
         */
        public function setHooks()
        {
            $this->hooks = array();
            
            $hooks = $this->getPluginDefinition('hooks');

            if (is_array($hooks))
            {
                // TODO: Check if valid hook (array declaration have file)
                $this->hooks = array_merge($this->hooks, $hooks);
            }

            $this->addConfig('hooks', $this->hooks);

            return $this;
        }
        public function getHooks()
        {
            return $this->hooks;
        }

        /**
         * Images
         */
        public function setImages()
        {
            $this->images = array();
            
            $images = $this->getPluginDefinition('images');

            if (is_array($images))
            {
                // TODO: Check if valid image filter
                $this->images = array_merge($this->images, $images);
            }

            $this->addConfig('images', $this->images);

            return $this;
        }
        public function getImages()
        {
            return $this->images;
        }

        /**
         * The Instance
         */
        private function setInstance($instance = null)
        {
            $this->instance = $instance;

            return $this;
        }
        private function getInstance()
        {
            return $this->instance;
        }

        /**
         * License
         */
        public function setLicense()
        {
            $this->license = $this->getBootstrapDefinition('License');

            $this->addConfig('license', $this->license);

            return $this;
        }
        public function getLicense()
        {
            return $this->license;
        }

        /**
         * Plugin Name
         */
        public function setName()
        {
            $this->name = $this->getBootstrapDefinition('Name');

            $this->addConfig('name', $this->name);

            return $this;
        }
        public function getName()
        {
            return $this->name;
        }

        /**
         * Namespace
         */
        public function setNamespace()
        {
            $namespace = null;
            
            // Namespce from Config.php
            $namespace = $this->getPluginDefinition('namespace');

            // Default Namespce from Name definition
            if (null == $namespace)
            {
                $namespace = $this->getName();
            }

            $this->namespace = \Components\Strings\Strings::slugify($namespace, "_");

            $this->addConfig('namespace', $this->namespace);

            return $this;
        }
        public function getNamespace()
        {
            return $this->namespace;
        }

        /**
         * Network
         */
        public function setNetwork()
        {
            $this->network = $this->getBootstrapDefinition('Network');

            $this->addConfig('network', $this->network);

            return $this;
        }
        public function getNetwork()
        {
            return $this->network;
        }

        /**
         * Posts
         */
        public function setPosts($schema = null)
        {
            $this->posts = array();
            
            $posts = $this->getPluginDefinition('posts');

            if (is_array($posts))
            {
                // TODO: Check if valid posts declaration
                $this->posts = array_merge($this->posts, $posts);
            }

            $this->addConfig('posts', $this->posts);

            return $this;
        }
        public function getPosts()
        {
            return $this->posts;
        }

        /**
         * Options
         */
        public function setOptions( $data = null )
        {
            $this->options = array();

            // Retrieve options from Option parameter
            $options = $this->getPluginDefinition('options');

            if (is_array($options))
            {
                $this->options = array_merge($this->options, $options);
            }

            // Retrieve options from Default values of Custom Post settings
            // TODO: Extract options from Post settings

            $this->addConfig('options', $this->options);

            return $this;
        }
        public function getOptions()
        {
            return $this->options;
        }

        /**
         * Plugin URI
         */
        public function setPluginURI()
        {
            $this->pluginURI = $this->getBootstrapDefinition('PluginURI');

            $this->addConfig('pluginURI', $this->pluginURI);

            return $this;
        }
        public function getPluginURI()
        {
            return $this->pluginURI;
        }

        /**
         * Repository
         */
        public function setrepository()
        {
            $repository = $this->getBootstrapDefinition('Repository');
            $repository.= DIRECTORY_SEPARATOR;

            $this->repository = $repository;

            $this->addConfig('repository', $this->repository);

            return $this;
        }
        public function getrepository()
        {
            return $this->repository;
        }

        /**
         * Settings
         */
        public function setSettings()
        {
            $this->settings = array();
            
            $settings = $this->getPluginDefinition('settings');

            if (is_array($settings))
            {
                // TODO: Check if valid settings declaration
                $this->settings = array_merge($this->settings, $settings);
            }

            $this->addConfig('settings', $this->settings);

            return $this;
        }
        public function getSettings()
        {
            return $this->settings;
        }

        /**
         * Shortcodes
         */
        public function setShortcodes()
        {
            $this->shortcodes = array();
            
            $shortcodes = $this->getPluginDefinition('shortcodes');

            if (is_array($shortcodes))
            {
                // TODO: Check if valid shortcodes (array declaration have file)
                $this->shortcodes = array_merge($this->shortcodes, $shortcodes);
            }

            $this->addConfig('shortcodes', $this->shortcodes);

            return $this;
        }
        public function getShortcodes()
        {
            return $this->shortcodes;
        }

        /**
         * Text Domain
         */
        public function setTextdomain()
        {
            $this->textDomain = $this->getBootstrapDefinition('TextDomain');

            $this->addConfig('textDomain', $this->textDomain);

            return $this;
        }
        public function getTextDomain()
        {
            return $this->textdomain;
        }

        /**
         * Define the plugin Title
         */
        public function setTitle()
        {
            $title = $this->getBootstrapDefinition('Title');

            $this->title = strip_tags($title);

            $this->addConfig('title', $this->title);

            return $this;
        }
        public function getTitle()
        {
            return $this->title;
        }

        /**
         * Define the plugin Title HTML
         */
        public function setTitleHTML()
        {
            $this->titleHTML = $this->getBootstrapDefinition('Title');

            $this->addConfig('titleHTML', $this->titleHTML);

            return $this;
        }
        public function getTitleHTML()
        {
            return $this->titlehtml;
        }

        /**
         * Define the plugin version number
         */
        public function setVersion()
        {
            $this->version = $this->getBootstrapDefinition('Version');

            $this->addConfig('version', $this->version);

            return $this;
        }
        public function getVersion()
        {
            return $this->version;
        }

        /**
         * Widgets
         */
        public function setWidgets()
        {
            $this->widgets = array();
            
            $widgets = $this->getPluginDefinition('widgets');

            if (is_array($widgets))
            {
                // TODO: Check if valid widgets (array declaration have file)
                $this->widgets = array_merge($this->widgets, $widgets);
            }

            $this->addConfig('widgets', $this->widgets);

            return $this;
        }
        public function getWidgets()
        {
            return $this->widgets;
        }
    }
}
