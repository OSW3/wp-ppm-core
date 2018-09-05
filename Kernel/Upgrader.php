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
use \Components\Files\Files;

if (!class_exists('Kernel\Upgrader'))
{
    class Upgrader
    {
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
         * Constructor
         */
        public function __construct($kernel)
        {
            // Retrieve instance of Kernel
            $this->setKernel($kernel);

            $this->setCurentVersion();
            $this->setRemoteVersion();

			// define the alternative API for updating checking
			$this->checkUpdate( new \StdClass() );
			// add_filter('pre_set_site_transient_update_plugins', array(&$this, 'checkUpdate'));


            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->getKernel()->getCore()->getConfig('repository') );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->getCurentVersion() );
            // echo '</pre>';
            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->getRemoteVersion() );
            // echo '</pre>';
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
         * Remote Version
         */
        private function setRemoteVersion()
        {
            // TODO: define the good repo
            // $remote = $this->getKernel()->getCore()->getConfig('repository');
            // $remote.= 'ppm.php';
            // // $repository.= Core::BOOTSTRAP;

            $remote_url = "https://raw.githubusercontent.com/OSW3/wp-please-plug-me/develop/ppm.php";

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
        public function checkUpdate($transient)
        {
            $update = version_compare( 
                $this->getCurentVersion(), 
                $this->getRemoteVersion(),
                '<'
            );



            echo '<pre style="padding-left: 180px;">';
            var_dump( $update );
            var_dump( $this->getRemoteVersion() );
            echo '</pre>';
        }
    }
}