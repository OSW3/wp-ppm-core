<?php

namespace Components\Form;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Config;
use \Kernel\Session;

if (!class_exists('Components\Form\Types'))
{
    abstract class Types
    {
        /**
         * Field Accept
         */
        private $accept;

        /**
         * Autocomplete attribute
         */
        private $autocomplete;

        /**
         * 
         */
        private $autofocus;

        /**
         * Field Algo
         */
        private $algo;

        /**
         * Field Attrs
         */
        private $attrs;
        
        /**
         * 
         */ 
        private $choices;
        
        /**
         * 
         */ 
        private $class;
        
        /**
         * 
         */ 
        private $cols;
        
        /**
         * Field config
         */
        protected $config;

        /**
         * 
         */ 
        private $disabled;
        
        /**
         * 
         */ 
        private $expanded;
        
        /**
         * 
         */ 
        private $helper;
        
        /**
         * 
         */ 
        private $id;
        
        /**
         * 
         */ 
        private $label;
        
        /**
         * Number of loop on collection init
         */ 
        private $loop;
        
        /**
         * 
         */ 
        private $max;
        
        /**
         * 
         */ 
        private $maxLength;
        
        /**
         * 
         */ 
        private $min;
        
        /**
         * 
         */ 
        private $multiple;
        
        /**
         * 
         */ 
        private $name;
        
        /**
         * 
         */ 
        private $placeholder;
        
        /**
         * 
         */ 
        private $readonly;
        
        /**
         * 
         */ 
        private $required;
        
        /**
         * 
         */ 
        private $rows;
        
        /**
         * 
         */ 
        private $rules;
        
        /**
         * 
         */ 
        private $selected;

        /**
         * 
         */
        protected $session;
        
        /**
         * Schema for collection Type
         */ 
        private $schema;
        
        /**
         * 
         */ 
        private $size;
        
        /**
         * 
         */ 
        private $step;

        /**
         * 
         */
        private $template;

        /**
         * 
         */
        private $template_type;
        
        /**
         * 
         */ 
        private $type;
        
        /**
         * 
         */ 
        private $value;
        
        /**
         * 
         */ 
        private $width;

        /**
         * Constructor
         * 
         * @param array $config
         * @param string $template, the type of field template ('metabox' | 'collection')
         */
        public function __construct(array $config, string $template_type = null)
        {
            $this->template_type = $template_type;

            // define Field Type
            $this->setConfig($config);

            // 
            $this->session = new Session($this->getConfig('namespace'));

            // Init the Label
            $this->setLabel();

            // Init the Helper
            $this->setHelper();

            // Call setter methods
            foreach ($this->attributes() as $attribute) 
            {
                $attribute = strtolower($attribute);
                $attribute = ucfirst($attribute);
                $method = 'set'.$attribute;
                $this->$method();
            }

            // Build the field
            $this->builder();

            do_action('admin_head');
        }

        /**
         * Default Field Builder
         */
        public function builder() {}

        /**
         * Render
         * 
         * Rendering the field
         */
        public function render()
        {
            // Init the Output
            $output = '';

            switch ($this->template_type) 
            {
                case 'collection':
                case 'metabox':
                    $output.= '<tr>';
                    $output.= '<th scope="row">';
                    $output.= $this->tagLabel();
                    $output.= '</th>';
                    $output.= '<td>';
                    $output.= $this->tagTemplate();
                    $output.= $this->tagHelper();
                    $output.= '</td>';
                    $output.= '</tr>';
                    break;
                
                /**
                 * Checkbox or Radio
                 * <label><input type="checkbox"> Label</label>
                 * 
                 * Otherwise
                 * <label>label</label> <input type="text">
                 */
                default:
                    if (!in_array($this->getType(), ['checkbox', 'radio']))
                    {
                        $output.= $this->tagLabel();
                    }
                    $output.= $this->tagTemplate();
                    $output.= $this->tagHelper();
                    break;
            }

            return $output;
        }


        /**
         * ----------------------------------------
         * Tag templates
         * ----------------------------------------
         */

        /**
         * Tag Template
         */
        public function tag()
        {
            return $this->tagInput();
        }

        /**
         * Default Tag
         */
        public function tagInput()
        {
            return '<input{{attributes}} />';
        }

        /**
         * Default Attributes list
         */
        public function attributes() 
        {
            return ['type', 'id', 'name', 'value', 'class', 'disabled', 'required', 'readonly'];
        }

        /**
         * Label
         */
        public function tagLabel()
        {
            $tag = '<label$1>$2$3</label>';

            if (!empty($this->getId())) { 
                $tag = preg_replace("/\\$1/", ' for="'.$this->getId().'"', $tag);
            }

            if (!empty($this->getLabel())) {
                $tag = preg_replace("/\\$2/", $this->getLabel(), $tag);
            }

            if ($this->getRequired()) { 
                $tag = preg_replace("/\\$3/", ' <span>*</span>', $tag);
            }

            $tag = preg_replace("/(:?\\$1|\\$2|\\$3)/", null, $tag);

            return $tag;
        }

        /**
         * Helper
         */
        public function tagHelper()
        {
            $tag = null;

            if (!empty($this->getHelper()))
            {
                $helper = $this->getHelper();

                if (!is_array($helper)) {
                    $helper = [$helper];
                }

                foreach ($helper as $item) 
                {
                    if ('notice' == $item[0]) {
                        $tag.= '<p class="description ppm-description has-error">' . $item[1] . '</p>';
                    } else {
                        $tag.= '<p class="description ppm-description">' . $item[1] . '</p>';
                    }
                }
            }

            return $tag;
        }

        public function tagAttributes()
        {
            $attr = '';

            foreach ($this->attributes() as $attribute) 
            {
                $attribute = strtolower($attribute);
                $attribute = ucfirst($attribute);
                $method = 'getAttr'.$attribute;
                $attr.= $this->$method();
            }

            return $attr;
        }

        /**
         * 
         */
        protected function tagTemplate()
        {
            return preg_replace(
                "/{{attributes}}/", 
                $this->tagAttributes(), 
                $this->tag()
            );
        }


        /**
         * ----------------------------------------
         * Retrieve Attribute and config options from config.php
         * ----------------------------------------
         */

        /**
         * Config from config.php
         */
        private function setConfig(array $config)
        {
            $this->config = $config;

            $this->setAttrs();
            $this->setRules();

            return $this;
        }
        protected function getConfig(string $key = '')
        {
            if (isset( $this->config[$key] )) 
            {
                return $this->config[$key];
            }

            return null;
        }

        /**
         * Attributes from this->getConfig()
         */
        private function setAttrs()
        {
            // Default Attrs
            $this->attrs = array();

            $config = $this->config;

            if (isset($config['attr']) && is_array($config['attr']))
            {
                $this->attrs = $config['attr'];
            }

            return $this;
        }
        protected function getAttr(string $key = '')
        {
            if (isset( $this->attrs[$key] )) 
            {
                return $this->attrs[$key];
            }

            return null;
        }

        /**
         * Rules from this->getConfig()
         */
        private function setRules()
        {
            // Default Attrs
            $this->rules = array();

            $config = $this->config;

            if (isset($config['rules']) && is_array($config['rules']))
            {
                $this->rules = $config['rules'];
            }

            return $this;
        }
        protected function getRule(string $key = '')
        {
            if (isset( $this->rules[$key] )) 
            {
                return $this->rules[$key];
            }

            return null;
        }


        /**
         * ----------------------------------------
         * Options and Attribute Getters / Setters
         * ----------------------------------------
         */

        /**
         * Accept
         */
        protected function setAccept()
        {
            // Default class
            $this->accept = null;
            
            // Retrive Class parameters
            $accept = $this->getRule('allowed_types');

            if (is_array($accept))
            {
                $this->accept = implode(",", $accept);
            }
            else
            {
                $this->accept = $accept;
            }

            return $this;
        }
        protected function getAccept()
        {
            return $this->accept;
        }
        protected function getAttrAccept()
        {
            return $this->getAccept() ? ' accept="'.$this->getAccept().'"' : null;
        }

        /**
         * Autocomplete
         */
        protected function setAutocomplete()
        {
            // Default Autocomplete
            $this->autocomplete = false;

            // Retrive Autocomplete parameters
            $autocomplete = $this->getAttr('autocomplete');

            if (is_bool($autocomplete))
            {
                $this->autocomplete = $autocomplete;
            }

            return $this;
        }
        protected function getAutocomplete()
        {
            return $this->autocomplete;
        }
        protected function getAttrAutocomplete()
        {
            $autocomplete = $this->getAutocomplete() ? 'on' : 'off';

            return ' autocomplete="'.$autocomplete.'"';
        }

        /**
         * Autofocus
         */
        protected function setAutofocus()
        {
            // Default Autofocus
            $this->autofocus = false;

            // Retrive Autofocus parameters
            $autofocus = $this->getAttr('autofocus');

            if (is_bool($autofocus))
            {
                $this->autofocus = $autofocus;
            }

            return $this;
        }
        protected function getAutofocus()
        {
            return $this->autofocus;
        }
        protected function getAttrAutofocus()
        {
            return $this->getAutofocus() ? ' autofocus="autofocus"' : null;
        }

        /**
         * Class
         */
        protected function setClass()
        {
            // Default class
            $this->class = 'ppm-control';
            
            if (
                is_admin() && 
                ($this->template_type == 'metabox' || $this->template_type == 'collection')&&
                !in_array($this->getType(), ['color'])
            ){
                $this->class.= ' regular-text';
            }

            // Retrieve value from session (after submission)
            $session = new Session($this->getConfig('namespace'));
            foreach ($session->errors($this->getConfig('post_type')) as $error) 
            {
                if (isset($error['key']) && $error['key'] == $this->getConfig('key')) 
                {
                    $this->class.= ' has-error';
                }
            }

            // Retrive Class parameters
            $class = $this->getAttr('class');

            if (is_string($class))
            {
                $this->class.= ' '.$class;
            }

            return $this;
        }
        protected function getClass()
        {
            return $this->class;
        }
        protected function getAttrClass()
        {
            return $this->getClass() ? ' class="'.$this->getClass().'"' : null;
        }

        /**
         * Choices
         */
        protected function setChoices(array $choices=[])
        {
            // Default choices
            $this->choices = $choices;

            // if (!in_array($this->getType(), ['option']))
            // {
                if ($this->getConfig('choices'))
                {
                    $this->choices = $this->getConfig('choices');
                }
            // }

            return $this;
        }
        protected function getChoices()
        {
            return $this->choices;
        }

        /**
         * Cols
         */
        protected function setCols()
        {
            // Default cols
            $this->cols = null;

            // Retrive cols parameters
            $cols = $this->getAttr('cols');

            if (is_int($cols))
            {
                $this->cols = $cols;
            }

            return $this;
        }
        protected function getCols()
        {
            return $this->cols;
        }
        protected function getAttrCols()
        {
            return $this->getCols() ? ' cols="'.$this->getCols().'"' : null;
        }

        /**
         * Disabled
         */
        protected function setDisabled()
        {
            // Default readonly
            $this->disabled = false;

            // Retrive Disabled parameters
            $disabled = $this->getAttr('disabled');

            if (is_bool($disabled))
            {
                $this->disabled = $disabled;
            }

            return $this;
        }
        protected function getDisabled()
        {
            return $this->disabled;
        }
        protected function getAttrDisabled()
        {
            return $this->getDisabled() ? ' disabled="disabled"' : null;
        }

        /**
         * Expanded
         */
        protected function setExpanded()
        {
            // Default expanded
            $this->expanded = false;

            // Retrive Readonly parameters
            $expanded = $this->getConfig('expanded');

            if (is_bool($expanded))
            {
                $this->expanded = $expanded;
            }

            return $this;
        }
        protected function getExpanded()
        {
            return $this->expanded;
        }

        /**
         * Set Helper
         */
        protected function setHelper()
        {
            // Default helper
            $this->helper = [];

            // Retrieve value from session (after submission)
            $session = new Session($this->getConfig('namespace'));
            foreach ($session->errors($this->getConfig('post_type')) as $error) 
            {

                if (isset($error['key']) && $error['key'] == $this->getConfig('key')) 
                {
                    array_push($this->helper, ["notice", $error['message']]);
                }
            }

            if ($this->getConfig('helper'))
            {
                array_push($this->helper, ["normal", $this->getConfig('helper')]);
            }

            return $this;
        }
        protected function getHelper()
        {
            return $this->helper;
        }

        /**
         * ID
         */
        protected function setId($id = null)
        {
            if (null == $id)
            {
                $id = $this->getAttr('id');
            }

            if (null != $id) 
            {
                $this->id = $id;
            }
            else
            {
                $this->id = $this->getConfig('key');
            }


            if ('collection' == $this->template_type)
            {
                $this->id.= '-{{number}}';
            }

            return $this;
        }
        protected function getId()
        {
            return $this->id;
        }
        protected function getAttrId()
        {
            return $this->getId() ? ' id="'. $this->getId() .'"' : null;
        }

        /**
         * Label
         */
        protected function setLabel()
        {
            // Default label
            $this->label = '';

            if ($this->getConfig('label'))
            {
                $this->label = $this->getConfig('label');
            }

            return $this;
        }
        protected function getLabel()
        {
            return $this->label;
        }

        /**
         * List
         */
        private function setList()
        {
            return $this;
        }
        private function getList()
        {
            return $this->list;
        }
        private function getAttrList()
        {
            return "";
        }

        /**
         * Loop
         */
        protected function setLoop($loop = null)
        {
            // default loop value
            $this->loop = 1;

            if (null === $loop)
            {
                $loop = $this->getRule('init');
            }

            if (is_int($loop) && $loop >= 0 ) 
            {
                $this->loop = $loop;
            }

            return $this;
        }
        protected function getLoop()
        {
            return $this->loop;
        }

        /**
         * Max 
         */
        protected function setMax()
        {
            // Default max
            $this->max = null;

            // Retrive Max parameters
            $max = $this->getAttr('max');

            if (is_int($max))
            {
                $this->max = $max;
            }

            return $this;
        }
        protected function getMax()
        {
            return $this->max;
        }
        protected function getAttrMax()
        {
            return $this->getMax() ? ' max="'.$this->getMax().'"' : null;
        }

        /**
         * Max Length
         */
        protected function setMaxLength()
        {
            // Default maxLength
            $this->maxLength = null;

            // Retrive Max parameters
            $maxLength = $this->getAttr('maxlength');

            if (is_int($maxLength))
            {
                $this->maxLength = $maxLength;
            }

            return $this;
        }
        protected function getMaxLength()
        {
            return $this->maxLength;
        }
        protected function getAttrMaxLength()
        {
            return $this->getMaxLength() ? ' maxlength="'.$this->getMaxLength().'"' : null;
        }

        /**
         * Set Attribute Min
         */
        protected function setMin()
        {
            // Default min
            $this->min = null;

            // Retrive Min parameters
            $min = $this->getAttr('min');

            if (is_int($min))
            {
                $this->min = $min;
            }

            return $this;
        }
        protected function getMin()
        {
            return $this->min;
        }
        protected function getAttrMin()
        {
            return $this->getMin() ? ' min="'.$this->getMin().'"' : null;
        }

        /**
         * Multiple
         */
        protected function setMultiple()
        {
            // Default multiple
            $this->multiple = false;

            // Retrive Readonly parameters
            $multiple = $this->getAttr('multiple');

            if (is_bool($multiple))
            {
                $this->multiple = $multiple;
            }

            return $this;
        }
        protected function getMultiple()
        {
            return $this->multiple;
        }
        protected function getAttrMultiple()
        {
            return $this->getMultiple() ? ' multiple="multiple"' : null;
        }

        /**
         * Name
         */
        protected function setName(string $name = '')
        {
            if (empty($name)) 
            {
                $name = $this->getConfig('post_type');
                $name.= '['.$this->getConfig('key').']';
            }

            $this->name = $name;
            
            return $this;
        }
        protected function getName()
        {
            return $this->name;
        }
        protected function getAttrName()
        {
            return ' name="'.$this->getName().'"';
        }

        /**
         * Pattern
         */
        protected function setPattern()
        {
            // Default placeholder
            $this->pattern = null;

            // Retrive pattern parameters
            $pattern = $this->getRule('pattern');

            $track_errors = ini_get('track_errors');
            ini_set('track_errors', 'on');
            $php_errormsg = '';
            @preg_match($pattern, '');
            ini_set('track_errors', $track_errors);
            
            if (is_string($pattern) && empty($php_errormsg))
            {
                $pattern = substr($pattern, 1, strlen($pattern));
                $pattern = substr($pattern, 0, strlen($pattern)-1);
                $this->pattern = $pattern;
            }

            return $this;
        }
        protected function getPattern()
        {
            return $this->pattern;
        }
        protected function getAttrPattern()
        {
            return $this->getPattern() ? ' pattern="'.$this->getPattern().'"' : null;
        }

        /**
         * Placeholder
         */
        protected function setPlaceholder()
        {
            // Default placeholder
            $this->placeholder = null;

            // Retrive placeholder parameters
            $placeholder = $this->getAttr('placeholder');

            if (is_string($placeholder))
            {
                $this->placeholder = $placeholder;
            }

            return $this;
        }
        protected function getPlaceholder()
        {
            return $this->placeholder;
        }
        protected function getAttrPlaceholder()
        {
            return $this->getPlaceholder() ? ' placeholder="'.$this->getPlaceholder().'"' : null;
        }

        /**
         * Readonly
         */
        protected function setReadonly()
        {
            // Default readonly
            $this->readonly = false;

            // Retrive Readonly parameters
            $readonly = $this->getAttr('readonly');

            if (is_bool($readonly))
            {
                $this->readonly = $readonly;
            }

            return $this;
        }
        protected function getReadonly()
        {
            return $this->readonly;
        }
        protected function getAttrReadonly()
        {
            return $this->getReadonly() ? ' readonly="readonly"' : null;
        }

        /**
         * Required
         */
        protected function setRequired()
        {
            // Default required
            $this->required = false;

            // Retrive Required parameters
            $required = $this->getAttr('required');

            if (is_bool($required))
            {
                $this->required = $required;
            }

            return $this;
        }
        protected function getRequired()
        {
            return $this->required;
        }
        public function getAttrRequired()
        {
            return $this->getRequired() ? ' required="required"' : null;
        }

        /**
         * Rows
         */
        protected function setRows()
        {
            // Default Rows
            $this->rows = null;

            // Retrive rows parameters
            $rows = $this->getAttr('rows');

            if (is_int($rows))
            {
                $this->rows = $rows;
            }

            return $this;
        }
        protected function getRows()
        {
            return $this->rows;
        }
        protected function getAttrRows()
        {
            return $this->getRows() ? ' rows="'.$this->getRows().'"' : null;
        }

        /**
         * Schema
         * 
         * Define schema for Collection Type
         */
        protected function setSchema()
        {
            // Default Schema
            $this->schema = [];

            // Retrive Schema parameters
            $schema = $this->getConfig('schema');

            if (is_string($schema) || is_array($schema))
            {
                if (is_string($schema))
                {
                    $schema = [$schema];
                }

                $this->schema = $schema;
            }

            return $this;
        }
        protected function getSchema()
        {
            return $this->schema;
        }

        /**
         * Size
         */
        protected function setSize()
        {
            // Default Size
            $this->size = null;

            // Retrive Size parameters
            $size = $this->getAttr('size');

            if (is_int($size))
            {
                $this->size = $size;
            }

            return $this;
        }
        protected function getSize()
        {
            return $this->size;
        }
        protected function getAttrSize()
        {
            // TODO: Code Injection for <select>
            // TODO: Code Injection for <input text>
            // $this->bs->codeInjection('head', "<style>.wp-admin select {height: auto;}</style>");

            return $this->getSize() ? ' size="'.$this->getSize().'"' : null;
        }

        /**
         * Step
         */
        protected function setStep()
        {
            // Default Step
            $this->step = null;

            // Retrive Step parameters
            $step = $this->getAttr('step');

            if (is_int($step) || is_float($step))
            {
                $this->step = $step;
            }

            return $this;
        }
        protected function getStep()
        {
            return $this->step;
        }
        protected function getAttrStep()
        {
            return $this->getStep() ? ' step="'.$this->getStep().'"' : null;
        }

        /**
         * Type
         */
        protected function setType($type = null)
        {
            if (null === $type)
            {
                $called_class = get_called_class();
                $called_class = str_replace("\\", "/", $called_class);
                $called_class = basename($called_class);
                $called_class = strtolower($called_class);

                $type = $called_class;
            }

            $this->type = $type;

            return $this;
        }
        protected function getType()
        {
            return $this->type;
        }
        protected function getAttrType()
        {
            return ' type="'. $this->getType() .'"';
        }

        /**
         * Value
         */
        protected function setValue($value = null, $post_id = null)
        {
            // Retrieve value from session (after submission)
            if (null === $value)
            {
                $session = new Session($this->getConfig('namespace'));
                foreach ($session->responses($this->getConfig('post_type')) as $key => $response) 
                {
                    if ($key == $this->getConfig('key')) 
                    {
                        $value = $response;
                    }
                }
            }            

            // Retrieve response in database
            if (null === $value && !empty(get_post()))
            {
                if (null == $post_id)
                {
                    $post_id = get_post()->ID;
                }
                $hide_pwd_value = true;
                $post_field = $this->getConfig('key');
    
                $value = get_post_meta($post_id, $post_field, true);
            }

            if (null == $value) 
            {
                if ($this->getConfig('value'))
                {
                    $value = $this->getConfig('value');
                    
                    // if (is_string($value))
                    // {
                    //     $value = stripslashes($value);
                    // }
                }
            }

            switch ($this->getType()) 
            {
                case 'date':
                    if ('today' == $value) {
                        $value = date('Y-m-d');
                    }
                    break;

                case 'time':
                    if ('now' == $value) {
                        $value = date('H:i');
                    }
                    break;

                case 'password':
                    if ($hide_pwd_value)
                    {
                        $value = '';
                    }
                    break;
            }

            $this->value = $value;

            return $this;
        }
        protected function getValue()
        {
            return $this->value;
        }
        public function getAttrValue()
        {
            return $this->getValue() ? ' value="'.$this->getValue().'"' : null;
        }

        /**
         * Width
         */
        protected function setWidth()
        {
            // Default Width
            $this->width = null;

            // Retrive rows parameters
            $width = $this->getAttr('width');

            if (is_int($width))
            {
                $this->width = $width;
            }

            return $this;
        }
        protected function getWidth()
        {
            return $this->width;
        }
        protected function getAttrWidth()
        {
            return $this->getWidth() ? ' width="'.$this->getWidth().'"' : null;
        }
    }
}
