<?php

namespace Components\Form\Types;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Components\Form\Types;

if (!class_exists('Components\Form\Types\File'))
{
    class File extends Types 
    {
        /**
         * Tag Attributes
         */
        public function attributes()
        {
            return ['type', 'id', 'name', 'class', 'value', 'accept', 'autocomplete', 'autofocus', 'disabled', 'multiple', 'placeholder', 'readonly', 'required', 'size'];
        }

        /**
         * Tag Template
         */
        public function tag()
        {
            if ($this->getConfig('preview'))
            {
                return $this->tagWithPreview();
            }

            return $this->tagInput();            
        }

        /**
         * 
         */
        private function tagWithPreview()
        {
            $tag = "<table>";
            $tag.=  "<tr>";
            $tag.=      "<td>";
            // $tag.=          '<img src="'..'Framework/Assets/images/default.svg'.'">';
            $tag.=      "</td>";
            $tag.=      "<td>";
            $tag.=          $this->tagInput();
            $tag.=      "</td>";
            $tag.=  "</tr>";
            $tag.= "</table>";

            return $tag;
        }
    }
}
