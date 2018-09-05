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
use \Register\Actions;
use \Components\FileSystem as FS;

if (!class_exists('Register\Shortcodes'))
{
	class Shortcodes extends Actions
	{
        /**
         * Retrieve list of shortcodes
         */
        public function getActions()
        {
            return $this->bs->getShortcodes();
        }

        /**
         * 
         */
        public function getHeaders()
        {
            return [];
        }

        /**
         * Define the Shortcodes directory
         */
        public function getDirectory()
        {
            return FS::DIRECTORY_SHORTCODES;
        }
    }
}