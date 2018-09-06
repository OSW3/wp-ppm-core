<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
};

if (!class_exists('Kernel\Core'))
{
    class Core extends \Kernel\Config
    {
        /**
         * Name of The Core Bootstrap File
         */
        const BOOTSTRAP_FILE = 'ppm-core.php';

        /**
         * Path of the extra config file
         */
        const CONFIG_FILENAME = 'Config/Config.php';

        /**
         * The Master Branch of reprository
         */
        // const REPOSITORY_MASTER = "master";
        const REPOSITORY_MASTER = "alpha";

        /**
         * Bootstarp File
         */
        public function setBootstrap(string $bootstrap = '')
        {
            $dirname = dirname(__DIR__);
            $dirname.= DIRECTORY_SEPARATOR;

            $bootstrap = $dirname.self::BOOTSTRAP_FILE;

            $this->addConfig('bootstrap', $bootstrap);

            return $this;
        }
    }
}
