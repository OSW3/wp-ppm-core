<?php

namespace Components\Form\Types;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

// use \Components\Form\Types\Choices;
use \Components\Form\Types\Text;

if (!class_exists('Components\Form\Types\Year'))
{
    class Year extends Choices 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['id', 'name', 'class', 'autofocus', 'disabled', 'multiple', 'readonly', 'required'];

        /**
         * Override defaults parameters
         */
        protected function builder()
        {
            $this->setType('choices_select');
            $this->setChoices($this->choices());
        }

        /**
         * Define list of choices dates
         */
        protected function choices()
        {
            // Default dates range
            $default_start = date('Y');
            $default_end = $default_start-100;

            // Define Dates range
            $start = null;
            $end = null;

            // Define choices
            $choices = [];

            // Retrieve Range parameter
            $range = $this->getDefinition('range');
            if ($range)
            {

                // ReDefine Dates range
                $start = isset($range[0]) ? $range[0] : null;
                $end = isset($range[1]) ? $range[1] : null;
            }
            
            if (null == $start)
            {
                $start = $default_start;
                $end = $default_end;
            }
            elseif (null == $end)
            {
                $end = $start-100;
            }

            $start = intval($start);
            $end = intval($end);

            // Check direction
            if ($start > $end)
            {
                for ($i = $start; $i >= $end; $i--) 
                {
                    $choices[$i] = $i;
                }
            }
            else {
                for ($i = $start; $i <= $end; $i++) 
                {
                    $choices[$i] = $i;
                }
            }

            return $choices;
        }
    }
}
