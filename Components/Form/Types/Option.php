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

if (!class_exists('Components\Form\Types\Option'))
{
    class Option extends Types 
    {
        /**
         * Define attributes of the tag
         */
        // TODO: Disabled
        // TODO: OptGroup
        const ATTRIBUTES = ['value', 'disabled'];
        
        /**
         * Override tag pattern
         */
        protected function tag()
        {
            return '<option{attributes}>'.$this->getLabel().'</option>';
        }
        
        /**
         * Override defaults parameters
         */
        protected function builder()
        {
            $this->setType('option');
        }

        /**
         * Override Attr Value
         */
        protected function getAttrValue()
        {
            // Retrieve default value
            $defaults = $this->getdefinition('default');

            // Make sure $defaults is array (array needed for multiple)
            if (!is_array($defaults))
            {
                $defaults = [$defaults];
            }

            $attr = parent::getAttrValue();

            foreach ($defaults as $default) 
            {
                if ($this->getValue() === $default)
                {
                    $attr.= ' selected="selected"';
                }
            }

            return $attr;
        }
    }
}
