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

if (!class_exists('Components\Form\Types\Color'))
{
    class Color extends Types 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['type', 'id', 'name', 'value', 'list', 'disabled', 'class'];

        /**
         * Override the classe Getter
         */
        protected function getClass()
        {
            // Remove the class 'regular-text'
            $class = preg_replace("/".Types::CLASS_REGULAR_TEXT."/", null, parent::getClass());

            return $class;
        }
    }
}
