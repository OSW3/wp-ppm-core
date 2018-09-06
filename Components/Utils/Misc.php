<?php

namespace Components\Utils;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Components\Utils\Misc'))
{
	class Misc
	{
		public static function get_called_class_name(string $called_class)
		{
            $class = explode("\\", $called_class);
            $class = end($class);
            $class = strtolower($class);

			return $class;
		}
	}
}