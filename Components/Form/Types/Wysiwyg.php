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
use \Components\Utils\Strings;

if (!class_exists('Components\Form\Types\Wysiwyg'))
{
    class Wysiwyg extends Types 
    {
        /**
         * Define attributes of the tag
         */
        // TODO: Placeholder alternative
        // TODO: Maxlength alternative
        // TODO: Autofocus alternative
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'autofocus', 'disabled', 'required', 'readonly', 'rows'];
        
        /**
         * Override tag pattern
         */
        protected function tag()
        {
            // Define Editor Settings
            $settings = array();

            // Display the button "Medias"
            $settings['media_buttons'] = false;

            // Editor class
            $settings['editor_class'] = $this->getClass();

            // Editor Name Attribute
            $settings['textarea_name'] = $this->getName();

            // Editor Height
            $settings['textarea_rows'] = $this->getRows() ? $this->getRows() : 10;

            ob_start();
            wp_editor( $this->getValue(), $this->getId(), $settings );
            return ob_get_clean();
        }

        /**
         * Override defaults parameters
         */
        protected function builder()
        {
            // Retrieve the Type name
            $name = $this->getName();

            // Retrive the Plugin Namespace
            $namespace = $this->getNamespace();

            // Encode the Braket of the Type name and set the Type ID
            $this->setId(Strings::braket_encode($name, $namespace));

            // Set Readonly or Disabled attribute
            add_filter( 'tiny_mce_before_init', function( $args ) {
                $args['readonly'] = $this->getReadonly() || $this->getDisabled();
                return $args;
            });
        }
    }
}
