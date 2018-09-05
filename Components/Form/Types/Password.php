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

if (!class_exists('Components\Form\Types\Password'))
{
    class Password extends Types 
    {
        /**
         * Available Encryption engine
         */
        const ALGO = [
            'PASSWORD_BCRYPT','PASSWORD_ARGON2I',
            'PASSWORD_ARGON2_DEFAULT_MEMORY_COST',
            'PASSWORD_ARGON2_DEFAULT_TIME_COST',
            'PASSWORD_ARGON2_DEFAULT_THREADS','PASSWORD_DEFAULT'
        ];
        
        /**
         * Tag Attributes
         */
        public function attributes()
        {
            // TODO: Dirname
            // TODO: Pattern
            return ['type', 'id', 'name', 'class', 'value', 'autocomplete', 'autofocus', 'disabled', 'maxlength', 'placeholder', 'readonly', 'required', 'size'];
        }
    }
}
