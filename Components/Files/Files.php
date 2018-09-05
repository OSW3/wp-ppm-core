<?php

namespace Components\Files;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Components\Files\Files'))
{
    class Files
    {
        /**
         * Extensions
         */
        // const EXTENSION_CSS = ".css";
        // const EXTENSION_JS  = ".js";
        // const EXTENSION_PHP = ".php";

        // /**
        //  * Directories
        //  */
        // const DIRECTORY_CONFIG      = "Plugin/Config/";
        // const DIRECTORY_HOOKS       = "Plugin/Hooks/";
        // const DIRECTORY_FILTERS     = "Plugin/Filters/";
        // const DIRECTORY_SHORTCODES  = "Plugin/Shortcodes/";
        // const DIRECTORY_STYLES      = "Plugin/Public/assets/styles/";
        // const DIRECTORY_SCRIPTS     = "Plugin/Public/assets/scripts/";
        // const DIRECTORY_IMAGES      = "Plugin/Public/assets/images/";

        /**
         * Files
         */
        // const FILE_CONFIG = self::DIRECTORY_CONFIG.'config'.self::EXTENSION_PHP;




        /**
         * Get File Contents
         * 
         * File content from a local or a remote file
         */
        public static function getContents(string $file)
        {
            $content = null;

            if (filter_var($file, FILTER_VALIDATE_URL))
            {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $file);
				curl_setopt($curl, CURLOPT_COOKIESESSION, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$content = curl_exec($curl);
				curl_close($curl);
            }
            else 
            {
                if (file_exists($file))
                {
                    $content = file_get_contents($file);
                }
            }

            return $content;
        }

        /**
         * Get File Data
         */
        public static function getData(string $file, array $headers = [])
        {
            $content = self::getContents($file);

            // Make sure we catch CR-only line endings.
            $content = str_replace( "\r", "\n", $content );
            
            foreach ( $headers as $field => $regex ) 
            {
                if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $content, $match ) && $match[1] )
                {
                    $headers[ $field ] = _cleanup_header_comment( $match[1] );
                }
                else
                {
                    $headers[ $field ] = '';
                }
            }

            return $headers;
        }

        /**
         * Write File
         */
        public static function write(string $file, $data, string $type = 'text', bool $force = false)
        {
            if ((!file_exists($file) || $force === true) && is_writable(dirname($file)))
            {
                switch ($type) 
                {
                    case 'json':
                        if (is_array($data) || !self::isJson($data))
                        {
                            $data = json_encode($data);
                        }
                        break;
                }
    
                $fp = fopen($file, 'w');
                fwrite($fp, $data);
                fclose($fp);
            }
        }
        public static function writeJson(string $file, $data, bool $force = false)
        {
            self::write($file, $data, 'json', $force);
        }

        public static function isJson($string) 
        {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }
    }
}