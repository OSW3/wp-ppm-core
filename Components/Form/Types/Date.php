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

if (!class_exists('Components\Form\Types\Date'))
{
    class Date extends Types 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'list', 'disabled', 'max', 'min', 'readonly', 'required', 'step'];
        // <input type="date" name="thedate" list="dates">
        // <datalist id="dates">
        //     <option value="1985-09-10">
        //     <option value="1982-03-15">
        // </datalist>

        /**
         * Override Get Value
         */
        protected function getValue()
        {
            $value = parent::getValue();

            if ($value === 'today')
            {
                $value = date("Y-m-d");
            }

            return $value;
        }
    }
}
