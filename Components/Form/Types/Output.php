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
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['id', 'name', 'class', 'value', 'disabled', 'required'];

        /**
         * Override tag pattern
         */
        protected function tag()
        {
            return '<output{attributes}></output>';
        }

        /**
         * Override Attr Value
         */
        // protected function setValue($value = null, $postID = null)
        // {
        //     return $this->getDefinition('default');
        // }
        protected function getAttrValue()
        {
            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->getValue() );
            // echo '</pre>';
            return $this->getValue() ? ' for="'.$this->getValue().'"' : null;
        }
    }
}
