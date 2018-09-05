<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

if (!class_exists('Kernel\Mapper'))
{
    class Mapper
    {
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

			if (is_dir($target))
			{
				$files = glob( $target . '*', GLOB_MARK );

				foreach ($files as $file) 
				{
					if (is_dir($file))
					{
                        $item = [
                            'type' => 'directory',
                            'md5' => md5_file($file),
                            'absolute' => $file,
                            'relative' => str_replace(WP_PLUGIN_DIR, "", $file),
                        ];
						array_push($results, $item);
						$results = array_merge($results, $this->scandir( $file ));
					}
					else
					{
                        $item = [
                            'type' => 'file',
                            'md5' => md5_file($file),
                            'absolute' => $file,
                            'relative' => str_replace(WP_PLUGIN_DIR, "", $file),
                        ];
						array_push($results, $item);
					}
				}
			}

			return $results;
		}
    }
}
