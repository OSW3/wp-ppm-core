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

if (!class_exists('Register\Assets'))
{
	class Assets
	{
        /**
         * The instance of the bootstrap class
         * 
         * @param object instance
         */
        private $bs;

        /**
         * 
         */
        public function __construct($bs)
        {
            // Retrieve the bootstrap class instance
            $this->bs = $bs;

            // Add assets to WP Register
            $this->WP_Assets();
        }

        /**
         * 
         */
        private function WP_Assets()
        {
            // Define Assets list
            $_assets  = is_admin() ? $this->getAdminAssets() : $this->getFrontendAssets();
            $_scripts = (isset($_assets['scripts']) && is_array($_assets['scripts'])) ? $_assets['scripts'] : [];
            $_styles  = (isset($_assets['styles']) && is_array($_assets['styles'])) ? $_assets['styles'] : [];

            foreach ($_scripts as $script) 
            {
                $this->WP_Register('script', $script);
            }
            
            foreach ($_styles as $style) 
            {
                $this->WP_Register('style', $style);
            }
        }

        /**
         * 
         */
        private function WP_Register(string $type, array $asset)
        {
            $add = false;

            // Define File Extension, Path and URI for Scripts
            if ($type == 'script') 
            {
                $extension  = FS::EXTENSION_JS;
                $directory  = $this->bs->getRoot().FS::DIRECTORY_SCRIPTS;
                $uri        = $this->bs->getUri().FS::DIRECTORY_SCRIPTS;
            }

            // Define File Extension, Path and URI for Styles
            elseif ($type == 'style') 
            {
                $extension  = FS::EXTENSION_CSS;
                $directory  = $this->bs->getRoot().FS::DIRECTORY_STYLES;
                $uri        = $this->bs->getUri().FS::DIRECTORY_STYLES;
            }

            $filename   = isset($asset['src']) ? $asset['src'] : null;
            $handle     = isset($asset['handle']) ? $asset['handle'] : uniqid();
            $version    = isset($asset['version']) ? $asset['version'] : null;
            $deps       = isset($asset['dependencies']) ? $asset['dependencies'] : [];
            $in_footer  = isset($asset['in_header']) ? !$asset['in_header'] : true;
            $enqueue    = isset($asset['enqueue']) ? $asset['enqueue'] : true;
            
            if (!empty($filename)) 
            {
                $filename = str_replace($extension, '', $filename);
                $filename.= $extension;
                
                $directory.= $filename;
                $uri.= $filename;
                
                // Add a local file to the WP Register
                if (file_exists($directory) && is_file($directory))
                {
                    $add = true;
                }
                // Add a CDN URL to the WP Register
                elseif (preg_match("/^http(s)?/i", $filename)) 
                {
                    $add = true;
                    $directory = null;
                    $uri = $filename;
                }
            }
            
            if ($add) 
            {
                if ($type == 'script') 
                {
                    wp_register_script( $handle, $uri, $deps, $version, $in_footer );
                } 
                elseif ($type == 'style') 
                {
                    wp_register_style( $handle, $uri, $deps, $version );
                }

                if ($enqueue) 
                {
                    $this->enqueue($handle, $type);
                }
            }
        }

        /**
         * Return assets list form Admin part
         */
        private function getAdminAssets()
        {
            $_assets = $this->bs->getAssets();
            $_scripts = [];
            $_styles = [];

            // Retrieve specific Admin assets
            if (isset($_assets['admin'])) 
            {
                if (isset($_assets['admin']['styles'])) {
                    $_styles = array_merge($_styles, $_assets['admin']['styles']);
                }
                if (isset($_assets['admin']['scripts'])) {
                    $_scripts = array_merge($_scripts, $_assets['admin']['scripts']);
                }
            }

            // Retrieve Both assets
            if (isset($_assets['both'])) 
            {
                if (isset($_assets['both']['styles'])) {
                    $_styles = array_merge($_styles, $_assets['both']['styles']);
                }
                if (isset($_assets['both']['scripts'])) {
                    $_scripts = array_merge($_scripts, $_assets['both']['scripts']);
                }
            }

            // Framework Assets
            $framework_conf = $this->bs->getRoot().'Framework/config.php';
            if (file_exists($framework_conf)) 
            {
                // Default frmwrk_cnf
                $frmwrk_cnf = [];
                $plugin_uri = $this->bs->getUri();

                include_once $framework_conf;

                if (isset($frmwrk_cnf['assets']['admin'])) {
                    if (isset($frmwrk_cnf['assets']['admin']['styles'])) {
                        $_styles = array_merge($_styles, $frmwrk_cnf['assets']['admin']['styles']);
                    }
                    if (isset($frmwrk_cnf['assets']['admin']['scripts'])) {
                        $_scripts = array_merge($_scripts, $frmwrk_cnf['assets']['admin']['scripts']);
                    }
                }
            }

            return [
                "scripts" => $_scripts,
                "styles" => $_styles
            ];
        }

        /**
         * Return assets list form Frontend part
         */
        private function getFrontendAssets()
        {
            $_assets = $this->bs->getAssets();
            $_scripts = [];
            $_styles = [];

            // Retrieve specific Frontend assets
            if (isset($_assets['frontend'])) 
            {
                if (isset($_assets['frontend']['styles'])) {
                    $_styles = array_merge($_styles, $_assets['frontend']['styles']);
                }
                if (isset($_assets['frontend']['scripts'])) {
                    $_scripts = array_merge($_scripts, $_assets['frontend']['scripts']);
                }
            }

            // Retrieve Both assets
            if (isset($_assets['both'])) 
            {
                if (isset($_assets['both']['styles'])) {
                    $_styles = array_merge($_styles, $_assets['both']['styles']);
                }
                if (isset($_assets['both']['scripts'])) {
                    $_scripts = array_merge($_scripts, $_assets['both']['scripts']);
                }
            }

            return [
                "scripts" => $_scripts,
                "styles" => $_styles
            ];
        }

        /**
         * 
         */
        public function enqueue(string $handle, string $type = '')
        {
            if ($type == 'script') 
            {
                wp_enqueue_script($handle);
            } 
            elseif ($type == 'style') 
            {
                wp_enqueue_style($handle);
            }
        }


        

        // public function get_style(string $file = '')
        // {
        //     # code...
        // }
        // public function get_script(string $file = '')
        // {
        //     # code...
        // }
    }
}