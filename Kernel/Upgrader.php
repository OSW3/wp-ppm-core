<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Core;
use \Kernel\Kernel;
use \Kernel\Mapper;
use \Components\Files\Files;

if (!class_exists('Kernel\Upgrader'))
{
    class Upgrader
    {
        /**
         * Repository Branch
         */
        // const BRANCH = "master";
        const BRANCH = "alpha";

        /**
         * The instance of Kernel
         * 
         * Content instance of Core & Plugin
         * @param array
         */
        private $kernel;

		/**
		 * Version
		 */
		private $curent_version;
        private $remote_version;
        
        /**
         * Map
         */
        private $local_map;
        private $remote_map;

        /**
         * Constructor
         */
        public function __construct($kernel)
        {
            // Retrieve instance of Kernel
            $this->setKernel($kernel);

            // Define Remote Base
            $this->setRemoteURI();

            $this->setCurentVersion();
            $this->setRemoteVersion();

			// define the alternative API for updating checking
			$this->upgrader( new \StdClass() );
			// add_filter('pre_set_site_transient_update_plugins', array(&$this, 'upgrader'));

            // Files::writeJson( 
            //     $this->getKernel()->getCore()->getAbsoluteDirectory().Mapper::FILE_MAP,
            //     Mapper::sanitize($this->getKernel()->getCore()->getMap(), ['file']),
            //     true
            // );
        }

        /**
         * The Kernel
         */
        private function setKernel($kernel)
        {
            $this->kernel = $kernel;

            return $this;
        }
        private function getKernel()
        {
            return $this->kernel;
        }

        /**
         * Local Map
         */
        private function setLocalMap()
        {
            $this->local_map = Mapper::sanitize($this->getKernel()->getCore()->getMap(), ['file']);

            return $this;
        }
        private function getLocalMap()
        {
            return $this->local_map;
        }

        /**
         * Remote Map
         */
        private function setRemoteMap()
        {
            $remote_url = $this->getRemoteURI(Mapper::FILE_MAP);
            $remote_map = Files::getContents($remote_url);
            $this->remote_map = json_decode($remote_map, true);

            return $this;
        }
        private function getRemoteMap()
        {
            return $this->remote_map;
        }

        /**
         * Curent Version
         */
        private function setCurentVersion()
        {
            $this->curent_version = $this->getKernel()->getCore()->getConfig('version');

            return $this;
        }
        private function getCurentVersion()
        {
            return $this->curent_version;
        }

        /**
         * Remote URI
         */
        private function setRemoteURI()
        {
            $base = $this->getKernel()->getCore()->getConfig('repository');
            $parse = parse_url($base);

            $re = [
                'host' => [$parse['host'], "raw.githubusercontent.com"]
            ];

            $remoteURI = preg_replace("/".$re['host'][0]."/", $re['host'][1], $base);
            $remoteURI.= self::BRANCH.DIRECTORY_SEPARATOR;

            $this->remoteURI = $remoteURI;

            return $this;
        }
        private function getRemoteURI(string $file = '')
        {
            $remote_uri = $this->remoteURI;

            if (!empty($file))
            {
                $remote_uri.= $file;
            }

            return $remote_uri;
        }

        /**
         * Remote Version
         */
        private function setRemoteVersion()
        {
            $remote_url = $this->getRemoteURI(Core::BOOTSTRAP);

            $data = Files::getData($remote_url, ['Version' => 'Version']);

            $this->remote_version = $data['Version'];

            return $this;
        }
        private function getRemoteVersion()
        {
            return $this->remote_version;
        }

		/**
		 * Update
		 */
        public function checkVersion()
        {
            return version_compare( 
                $this->getCurentVersion(), 
                $this->getRemoteVersion(),
                '<'
            );
        }





        
		/**
		 * Update
		 */
        public function upgrader($transient)
        {
            if ($this->checkVersion())
            {
                // Define Maps
                $this->setLocalMap();
                $this->setRemoteMap();

                // Check differences between both maps
				// - Generate the remove list
				$rm = array_diff_assoc($this->getLocalMap(), $this->getRemoteMap());
				// - Generate the Download list
                $dl = array_diff_assoc($this->getRemoteMap(), $this->getLocalMap());
                
				// Remove files ares not in remote repository
				foreach ($rm as $file) 
				{
                    $file = preg_replace("@^".$this->getKernel()->getCore()->getRelativeDirectory()."@", null, $file);
					if (!in_array($file, Kernel::CORE_UPGRADER_EXCLUSION))
					{
						$file = $this->getKernel()->getCore()->getAbsoluteDirectory().$file;
						if (file_exists($file) && !is_dir($file))
						{
                            unlink($file);

                            // echo '<pre style="padding-left: 180px;">';
                            // // print_r( $this->getKernel()->getCore()->getAbsoluteDirectory() );
                            // print_r( $file );
                            // echo '</pre>';
						}
					}
				}

				// Copy files are not already in local
				foreach ($dl as $file) 
				{
                    $file = preg_replace("@^".$this->getKernel()->getCore()->getRelativeDirectory()."@", null, $file);
                    if (!in_array($file, Kernel::CORE_UPGRADER_EXCLUSION))
                    {
						$source = $this->getRemoteURI($file);
						$dest = $this->getKernel()->getCore()->getAbsoluteDirectory().$file;
						
                		copy($source, $dest);
                
                        // echo '<pre style="padding-left: 180px;">';
                        // print_r( $source );
                        // echo '</pre>';
                        // // echo '<pre style="padding-left: 180px;">';
                        // // print_r( $dest );
                        // // echo '</pre>';
					}
                }
                
                // Renew Local Map
                Files::writeJson( 
                    $this->getKernel()->getCore()->getAbsoluteDirectory().Mapper::FILE_MAP,
                    Mapper::sanitize($this->getKernel()->getCore()->getMap(), ['file']),
                    true
                );
            }
        }
    }
}