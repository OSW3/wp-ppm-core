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
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'accept', 'autocomplete', 'autofocus', 'disabled', 'multiple', 'placeholder', 'readonly', 'required', 'size'];
        
        /**
         * Override tag pattern
         */
        protected function tag()
        {
            if ($this->getPreview())
            {
                return $this->previewPattern();
            }

            return $this->tagInput();
        }

        /**
         * Override defaults parameters
         */
        protected function builder()
        {
            $this->setPreview();
        }

        /**
         * Define pattern for Has Preview
         */
        private function previewPattern()
        {
            $tag = "<table>";
            $tag.=  "<tr>";
            $tag.=      "<td>";
            // $tag.=          '<img src="'..'Framework/Assets/images/default.svg'.'">';
            $tag.=          "Preview Img";
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
