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

if (!class_exists('Components\Form\Types\Output'))
{
    class Output extends Types 
    {
        /**
         * Tag Attributes
         */
        public function attributes()
        {
            return ['id', 'name', 'class', 'value', 'disabled', 'required'];
        }

        /**
         * Tag Template
         */
        public function tag()
        {
            return '<output{{attributes}}></output>';
        }

        /**
         * Override Attr Value
         */
        public function getAttrValue()
        {
            return $this->getValue() ? ' for="'.$this->getValue().'"' : null;
        }
    }
}
