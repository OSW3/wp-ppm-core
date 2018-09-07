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
		/**
		 * Define the called class
		 */
		public static function get_called_class_name(string $called_class)
		{
            $class = explode("\\", $called_class);
            $class = end($class);
            $class = strtolower($class);

			return $class;
		}

		/**
		 * HTML Injection
		 * 
		 * @param string $source — The source code
		 * @param string $node — 'head' or 'footer', The target node <head> or <body> (footer)
		 * @param string $side — 'front', 'admin' or 'both'
		 */
		public static function injection(string $source, string $node = 'head', string $side = 'both')
		{
			if (in_array($side, ['both', 'front']))
			{
				// Front Footer
				if ($node == 'footer')
				{
					add_action('wp_footer', function() use ($source) { echo $source."\n"; });
				}

				// Front Head
				else
				{
					add_action('wp_head', function() use ($source) { echo $source."\n"; });
				}
			}
			if (in_array($side, ['both', 'admin']))
			{
				// Admin Footer
				if ($node == 'footer')
				{
					add_action('admin_footer', function() use ($source) { echo $source."\n"; });
				}

				// Admin Head
				else
				{
					add_action('admin_head', function() use ($source) { echo $source."\n"; });
				}
			}
		}

	}
}