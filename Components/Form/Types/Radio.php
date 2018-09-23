<?php

namespace Components\Form\Types;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Components\Form\Types\Text;
// use \Components\Form\Types\Checkbox;

if (!class_exists('Components\Form\Types\Radio'))
{
    class Radio extends Text 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'disabled', 'readonly'];

        /**
         * Override defaults parameters
         */
        protected function builder()
        {
            $this->setType('radio');
        }

        /**
         * Override Attr Value
         */
        protected function getAttrValue()
        {
            // Define attribute string
            $attr = ' value="'.$this->getDefinition('value').'"';

            $values = $this->getValue();

            if (!is_array($values)) 
            {
                $values = [$values];
            }

            foreach ($values as $value) 
            {
                if ($this->getDefinition('value') === $value)
                {
                    $attr.= ' checked="checked"';
                }
            }

            return $attr;
        }
    }
}
