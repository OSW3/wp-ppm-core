<?php

namespace Components\Utils;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Components\Utils\Arrays'))
{
	class Arrays
	{
        /**
         * In Array recursive
         */
        public static function inArray(array $haystack, $needle, $key = null, bool $recursive = true, bool $strict = false)
        {
            foreach ($haystack as $index => $item)
            {
                if ($key === null ? ($strict ? $item === $needle : $item == $needle) : ($strict ? $index === $key && $item === $needle : $index == $key && $item == $needle))
                {
                    return true;
                }
                elseif (is_array($item))
                {
                    return self::inArray($item, $needle, $key, $recursive, $strict);
                }
            }

            return false;
        }

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