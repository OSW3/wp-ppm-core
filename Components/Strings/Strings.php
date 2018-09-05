<?php

namespace Components\Strings;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}


if (!class_exists('Components\Strings\Strings'))
{
	class Strings
	{
		/**
		 * Slugify
         * 
         * @param string $text
         * @param string $separator
		 */
        public static function slugify( $text, $separator="-" )
        {
            $text = preg_replace('~[^\pL\d]+~u', $separator, $text);
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
            $text = preg_replace('~[^-\w]+~', '', $text);
            $text = trim($text, $separator);
            $text = preg_replace('~-+~', $separator, $text);
            $text = strtolower($text);

            if (empty($text)) return false;

            return $text;
        }

        /**
         * Random String
         * 
         * @param int $length
         */
        public static function random($params = []) 
        {
            $params = array_merge([
                "length" => 10,
                "integer" => true,
                "lower" => true,
                "upper" => true,
                "startWithAlpha" => true
            ], $params);

            $characters = '';
            $numerical = '0123456789';
            $alphabetical = 'abcdefghijklmnopqrstuvwxyz';

            if ($params['integer']) $characters.= $numerical;
            if ($params['lower']) $characters.= $alphabetical;
            if ($params['upper']) $characters.= strtoupper($alphabetical);

            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $params['length']; $i++) 
            {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            if ($params['startWithAlpha']) {
                if (preg_match("/^\d/", $randomString)) {
                    $randomString = substr_replace(
                        $randomString,
                        $alphabetical[rand(0, strlen($alphabetical) - 1)],
                        0, 1
                    );
                }
            }
            return $randomString;
        }
	}
}