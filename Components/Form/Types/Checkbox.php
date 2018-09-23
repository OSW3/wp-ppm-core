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

if (!class_exists('Components\Form\Types\Checkbox'))
{
    class Checkbox extends Text 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'autofocus', 'disabled', 'readonly', 'required'];
        
        /**
         * Override defaults parameters
         */
        protected function builder()
        {
            $this->setType('checkbox');
        }

        /**
         * Override Attr Value
         */
        public function getAttrValue()
        {
            // Define attribute string
            $attr = '';

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
