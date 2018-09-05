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

if (!class_exists('Components\Form\Types\Text'))
{
    class Text extends Types 
    {
        /**
         * Tag Attributes
         */
        public function attributes()
        {
            // TODO: List
            // TODO: Dirname
            return ['type', 'id', 'name', 'class', 'value', 'autocomplete', 'autofocus', 'disabled', 'maxlength', 'pattern', 'placeholder', 'readonly', 'required', 'size'];
        }
    }
}
