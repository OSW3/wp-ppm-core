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
use \Components\Form\Types\Checkbox;
use \Components\Form\Types\Radio;
use \Components\Form\Types\Option;

if (!class_exists('Components\Form\Types\Choices'))
{
    class Choices extends Types 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['id', 'name', 'class', 'multiple'];
        
        /**
         * Override tag pattern
         */
        protected function tag()
        {
            switch ($this->getType())
            {
                case 'choices_checkbox':
                case 'choices_radio':
                    return $this->tagChoices();

                case 'select':
                case 'choices_select':
                default:
                    return $this->tagSelect();
            }
        }

        /**
         * Override defaults parameters
         */
        protected function builder()
        {
            $this->setChoices();
            $this->setExpanded();

            if ($this->getExpanded() && $this->getMultiple()) {
                $this->setType("choices_checkbox");
            }
            elseif ($this->getExpanded() && !$this->getMultiple()) {
                $this->setType("choices_radio");
            }
            else {
                $this->setType("choices_select");
            }
        }




        /**
         * 
         */
        private function tagChoices()
        {
            return '<div class="choices-expanded '.$this->getClass().'">'.$this->options().'</div>';
        }

        /**
         * 
         */
        private function tagSelect()
        {
            return '<select{attributes}>'.$this->options().'</select>';
        }

        /**
         * 
         */
        private function options()
        {
            $tag = '';

            foreach ($this->getChoices() as $value => $label) 
            {
                // Tag options
                $options = array_merge($this->definition,[
                    "label"     => $label,
                    "value"     => $value,
                    // "selected"  => $this->selected === $value,
                    // "default"  => $this->getDefinition('default'),
                    "choices"   => []
                ]);

                switch ($this->getType())
                {
                    case 'choices_checkbox':
                        $tag.= $this->tagOptionChoice(new Checkbox($options));
                        break;

                    case 'choices_radio':
                        $tag.= $this->tagOptionChoice(new Radio($options));
                        break;

                    case 'choices_select':
                        $option = new Option($options);
                        $tag.= $option->render();
                        break;
                }
            }

            return $tag;
        }


        private function tagOptionChoice( $type )
        {
            $tag = '<div class="choices-option"><label>$1 $2</label></div>';

            if ('checkbox' == $type->getType())
            {
                $type->setName( $type->getName().'['.$type->getDefinition('value').']' );
            }

            $tag = preg_replace("/\\$1/", $type->render(), $tag);
            $tag = preg_replace("/\\$2/", $type->getLabel(), $tag);

            return $tag;
        }
    }
}
