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

if (!class_exists('Components\Form\Types\Tel'))
{
    class Tel extends Types 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'list', 'autocomplete', 'autofocus', 'disabled', 'maxlength', 'pattern', 'placeholder', 'readonly', 'required', 'size', 'dirname'];

        /**
         * Override tag pattern
         */
        protected function tag()
        {
            // TODO: Field Tel Expanded (Country index + Tel)
            // if ($this->getExpanded()) 
            // {
            //     return "Tel + Index";
            // }

            // Return default Input Tag
            return $this->tagInput();
        }

        /**
         * Override defaults parameters
         */
        public function builder()
        {
            $this->setExpanded();
        }
    }
}
