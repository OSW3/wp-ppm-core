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
        public function builder()
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

            // Retrieve default value
            $defaults = $this->getDefinition('default');

            // Make sure $defaults is array (array needed for multiple)
            if (!is_array($defaults))
            {
                $defaults = [$defaults];
            }

            foreach ($defaults as $default) 
            {
                if ($this->getDefinition('value') === $default || 'on' === strtolower($this->getDefinition('value')))
                {
                    $attr.= ' checked="checked"';
                }
            }

            return $attr;
        }
    }
}
