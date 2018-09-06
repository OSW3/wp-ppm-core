<?php

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}


if (!class_exists('Register\Images'))
{
	class Images
	{
        /**
         * The instance of the bootstrap class
         * 
         * @param object instance
         */
        protected $bs;

        /**
         * Images config defined in the config.php
         */
        private $images = array();

        /**
         * 
         */
        public function __construct($bs)
        {
            // Retrieve the bootstrap class instance
            $this->bs = $bs;

            // Retrieve the Images settings
            $this->setImages();

            // Add thumbnail filters
            $this->WP_ImageSizes();
        }

        /**
         * 
         */
        private function WP_ImageSizes()
        {

            print_r( $this->images );

        }

        /**
         * 
         */
        private function setImages()
        {
            // Retrieve the list of Images Sizes
            $images = $this->bs->getImages();
            $images = is_array($images) ? $images : [];

            $this->images = $images;

            return $this;
        }
    }
}