<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Kernel;

if (!class_exists('Kernel\Mapper'))
{
    class Mapper
    {
        /**
         * File Map
         */
        const FILE_MAP = "map.json";

        /**
         * Base directory of the map
         * 
         * @param string
         */
        private $directory;

        /**
         * Final Map
         * 
         * @param array
         */
        private $map;


        public function __construct(string $directory = '')
        {
            $this->setDirectory($directory);
            $this->setMap();
        }

        
        /** 
         * Directory of the map
         */
        private function setDirectory(string $directory = '')
        {
            $this->directory = $directory;

            return $this;
        }
        private function getDirectory()
        {
            return $this->directory;
        }

        /**
         * Map of directory
         */
        private function setMap()
        {
            $this->map = !empty($this->getDirectory())
                ? $this->scandir($this->getDirectory())
                : null;

            return $this;
        }
        public function getMap()
        {
            return $this->map;
        }

        /**
         * ScanDir recursive
         */
		private function scandir(string $target)
		{
			$results = [];
            $excludes = array_merge(
                Kernel::CORE_UPGRADER_EXCLUSION,
                [self::FILE_MAP]
            );

			if (is_dir($target))
			{
                $files = glob( $target . '*', GLOB_MARK );
                $exclusion = [];

				foreach ($files as $abs_file) 
				{
                    foreach ($excludes as $exclude) 
                    {
                        if (preg_match("@".$exclude."$@", $abs_file))
                        {
                            array_push($exclusion, $abs_file);
                        }
                    }
                }

				foreach ($files as $abs_file) 
				{
                    if (!in_array($abs_file, $exclusion))
                    {
                        if (is_dir($abs_file))
                        {
                            $item = [
                                'type' => 'directory',
                                'md5' => md5_file($abs_file),
                                'absolute' => $abs_file,
                                'relative' => str_replace(WP_PLUGIN_DIR, "", $abs_file),
                            ];
                            array_push($results, $item);
                            $results = array_merge($results, $this->scandir( $abs_file ));
                        }
                        else
                        {
                            $item = [
                                'type' => 'file',
                                'md5' => md5_file($abs_file),
                                'absolute' => $abs_file,
                                'relative' => str_replace(WP_PLUGIN_DIR, "", $abs_file),
                            ];
                            array_push($results, $item);
                        }
                    }
				}
			}

			return $results;
        }
        
        /**
         * Sanitize map
         * 
         * Return an associative array 'md5' => 'relative'
         */
        public static function sanitize(array $map, array $type = ['directory', 'file'])
        {
            foreach ($map as $key => $item) 
            {
                if (in_array($item['type'], $type))
                {
                    $map[$item['md5']] = $item['relative'];
                }
                unset($map[$key]);
            }

            return $map;
        }
    }
}
