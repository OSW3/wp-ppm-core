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
            $defaults = $this->getDefinition('default');

            // Make sure $defaults is array (array needed for multiple)
            if (!is_array($defaults))
            {
                $defaults = [$defaults];
            }

            // Selected Values
            $selected_values = $defaults;

            if (is_array($this->getValue()))
            {
                $selected_values = $this->getValue();
            }

            $attr = ' value="'.$this->getDefinition('value').'"';

            foreach ($selected_values as $selected) 
            {
                if ($this->getDefinition('value') === $selected)
                {
                    $attr.= ' selected="selected"';
                }
            }

            return $attr;
        }
    }
}
