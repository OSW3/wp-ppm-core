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

if (!class_exists('Components\Form\Types\Month'))
{
    class Month extends Types 
    {
        /**
         * Define attributes of the tag
         */
        // TODO: Step
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'list', 'disabled', 'max', 'min', 'readonly', 'required', 'step'];

        
        // <input type="month" name="thedate" min="2018-03" list="date">
        
        // <datalist id="dates">
        //     <option value="1982-03">
        //     <option value="1982-04">
        // </datalist>
    }
}
