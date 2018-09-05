<?php

namespace Components;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}


if (!class_exists('Components\Arrays'))
{
	class Arrays
	{
		/**
		 * isNumeric
         * 
         * @param array $array
		 */
        public static function isNumeric( array $array )
        {
            foreach ($array as $a => $b) 
            {
                if (!is_int($a)) 
                {
                    return false;
                }
            }

            return true;
        }

	}
}