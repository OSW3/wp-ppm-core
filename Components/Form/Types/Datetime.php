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

if (!class_exists('Components\Form\Types\Datetime'))
{
    class Datetime extends Types 
    {
        /**
         * Define attributes of the tag
         */
        // TODO: Step
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'list', 'list', 'disabled', 'max', 'min', 'readonly', 'required', 'step'];

        // <input type="datetime-local" name="thedate" min="2018-08-10T03:23" list="dates">
        
        // <datalist id="dates">
        //     <option value="1985-09-10T22:30">
        //     <option value="1982-03-15T23:10">
        // </datalist>
        
        /**
         * Override defaults parameters
         */
        public function builder()
        {
            $this->setType('datetime-local');
        }
    }
}
