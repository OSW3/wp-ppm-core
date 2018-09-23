<?php

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Register\Medias'))
{
    class Medias
    {
        /**
         * The instance of Kernel
         * 
         * Content instance of Core & Plugin
         * @param array
         */
        private $kernel;

        /**
         * List of responses
         */
        // private $responses;

        /**
         * Constructor
         */
        public function __construct($kernel, int $postid)
        {
            // Retrieve instance of Kernel
            $this->kernel = $kernel;

            $this->postid = $postid;
        }

        public function upload(array $files)
        {

// ici
            echo '<pre style="padding-left: 180px;">';
            print_r( "I AM THE UPLOADER" );
            echo '</pre>';

            echo '<pre style="padding-left: 180px;">';
            print_r( $this->postid );
            echo '</pre>';

            echo '<pre style="padding-left: 180px;">';
            print_r( $files );
            echo '</pre>';

            exit;
        }
    }
}