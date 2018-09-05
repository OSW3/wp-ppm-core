<?php

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Register\Options'))
{
	class Options
	{
        /**
         * Add options to database
         * 
         * @param array $options list of option to add
         */
        static function add(array $options)
        {
            foreach ($options as $key => $value) 
            {
                add_option($key, $value);
            }
        }

        /**
         * Retrieve an option by $key
         * 
         * @param string $key of the option you want to retrieve
         * @param mixed $default
         */
        static function get(string $key, $default = false)
        {
            get_option($key, $default);
        }

        /**
         * dalete an option by $key
         * 
         * @param string $key of the option you want to delete
         */
        static function delete(string $key)
        {
            delete_option($key);
        }
    }
}