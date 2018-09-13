<?php

namespace Components\Form;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

// use \Kernel\Config;
use \Components\Utils\Misc;
use \Components\Utils\Strings;
use \Kernel\Session;

if (!class_exists('Components\Form\Types'))
{
    abstract class Types
    {
        /**
         * Base Namespace
         */
        const BASE = '\\Components\\Form\\Types\\';

        /**
         * List of allowed Types
         */
        const ALLOWED = [ 'checkbox', 'choices', 'collection', 'color', 'date', 'datetime', 'email', 'file', 'hidden', 'month', 'number', 'option', 'output', 'password', 'radio', 'range', 'captcha', 'search', 'tel', 'text', 'textarea', 'time', 'url', 'week', 'wysiwyg', 'year' ];

        /**
         * Define default attributes of the tag
         */
        const ATTRIBUTES = ['type', 'id', 'name', 'class', 'value', 'readonly', 'disabled', 'required'];

        /**
         * Default WP Class of Admin Field
         */
        const CLASS_REGULAR_TEXT = 'regular-text';
        const CLASS_ERROR = 'has-error';
        const CLASS_REQUIRED = 'is-required';
        const CLASS_READONLY = 'is-readonly';
        const CLASS_DISABLED = 'is-disabled';
        
        /**
         * Attribute Accept
         * 
         * @param string|array
         */
        private $accept;

        /**
         * Attributes definition
         * 
         * @param array
         */
        private $attrs;

        /**
         * Attriblute Autocomplete
         * 
         * @param boolean
         */
        private $autocomplete;

        /**
         * Attriblute Autofocus
         * 
         * @param boolean
         */
        private $autofocus;
        
        /**
         * Choices definition
         * 
         * @param array
         */ 
        private $choices;
        
        /**
         * Attribute Class
         * 
         * @param string
         */ 
        private $class;
        
        /**
         * Attribute Cols
         * 
         * @param integer
         */ 
        private $cols;

        /**
         * Attribute Disabled
         * 
         * @param boolean
         */ 
        private $disabled;

        /**
         * Attribute Dirnmae
         * 
         * @param boolean
         */ 
        private $dirname;

        /**
         * Formated definition
         * 
         * @param array
         */
        protected $definition;
        
        /**
         * To define if Choices appear like <select> or checkbox or radio
         * 
         * @param boolean
         */ 
        private $expanded;
        
        /**
         * Helper definition
         * 
         * @param array
         */ 
        private $helper;
        
        /**
         * Attribute ID
         * 
         * @param string
         */ 
        private $id;
        
        /**
         * Value for the tag <label>
         * 
         * @param string
         */ 
        private $label;
        
        /**
         * Attribute List
         * 
         * @param string
         */ 
        private $list;
        
        /**
         * Attribute Max
         * 
         * @param integer
         */ 
        private $max;
        
        /**
         * Attribute MaxLength
         * 
         * @param integer
         */ 
        private $maxLength;
        
        /**
         * Attribute Min
         * 
         * @param integer
         */ 
        private $min;
        
        /**
         * Attribute Multiple
         * 
         * @param boolean
         */ 
        private $multiple;
        
        /**
         * Attribute Name
         * 
         * @param string
         */ 
        private $name;

        /**
         * Plugin namespace
         * 
         * @param string
         */
        private $namespace;
        
        /**
         * Attribute Pattern
         * 
         * @param RegExp
         */ 
        private $pattern;
        
        /**
         * Attribute Placeholder
         * 
         * @param string
         */ 
        private $placeholder;

        /**
         * Custom Post Type
         * 
         * @param string
         */
        private $posttype;
        
        /**
         * To define if File have a preview
         * 
         * @param boolean
         */ 
        private $preview;
        
        /**
         * Attribute Readonly
         * 
         * @param boolean
         */ 
        private $readonly;

        /**
         * Render Pattern name
         * 
         * @param string
         */
        private $renderPattern;
        
        /**
         * Attribute Required
         * 
         * @param boolean
         */ 
        private $required;
        
        /**
         * Attribute Rows
         * 
         * @param integer
         */ 
        private $rows;
        
        /**
         * Rules definition
         * 
         * @param array
         */ 
        private $rules;
        
        /**
         * Schema definition (for Collection Type)
         */ 
        private $schema;

        /**
         * Session
         * 
         * @param object Instance of Session
         */
        private $session;
        
        /**
         * Attribute size
         * 
         * @param integer
         */ 
        private $size;
        
        /**
         * Attribute Step
         * 
         * @param integer|float|double
         */ 
        private $step;
        
        /**
         * The type of Typefield
         * 
         * @param string
         */ 
        private $type;
        
        /**
         * Attribute Value
         * 
         * @param string
         */ 
        private $value;















        // /**
        //  * Field Algo
        //  */
        // private $algo;
        
        // /**
        //  * Field config
        //  */
        // protected $config;
        
        // /**
        //  * Number of loop on collection init
        //  */ 
        // private $loop;
        
        // /**
        //  * 
        //  */ 
        // private $selected;

        // /**
        //  * 
        //  */
        // private $template;
        
        // /**
        //  * 
        //  */ 
        // private $width;









        /**
         * Constructor
         * 
         * @param array $type, the type
         * @param string $renderPattern, the type of field template ('metabox' | 'collection')
         */
        public function __construct(array $type, string $renderPattern = '')
        {
            // Set the Render Pattern name
            $this->renderPattern = $renderPattern;

            // Type definition
            $this->setDefinition($type);

            // Retrieve the Plugin Namespace
            $this->setNamespace();

            // Retrive the Post Type
            $this->setPosttype();

            // Init Session
            $this->setSession();

            // Init the Label
            $this->setLabel();

            // Init the Helper
            $this->setHelper();

            // Attributes definition
            $this->setAttrs();

            // Rules definition
            $this->setRules();

            // Define Attributes
            foreach (static::ATTRIBUTES as $attribute) 
            {
                $method = 'set'.Strings::ucfirst($attribute);
                $this->$method();
            }

            // Build the field
            $this->builder();
        }

        /**
         * ----------------------------------------
         * Config
         * ----------------------------------------
         */

        /**
         * Default Builder
         * 
         * Use to reset / redefine some parameters
         */
        protected function builder() {}

        /**
         * Render the HTLM field
         */
        public function render()
        {
            // Init the Output
            $output = '';

            switch ($this->renderPattern) 
            {
                case 'collection':
                case 'metabox':
                    $output.= '<tr>';
                    $output.= '<th scope="row">';
                    $output.= $this->tagLabel();
                    $output.= '</th>';
                    $output.= '<td>';
                    $output.= $this->tagRender();
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
                    // if (in_array(Misc::get_called_class_name(get_called_class()), ['checkbox', 'radio']))

            // echo '<pre style="padding-left: 180px;">';
            // print_r( $this->getType() );
            // echo '</pre>';


                    // if (!in_array($this->getType(), ['checkbox', 'radio', 'option']))
                    {
                        $output.= $this->tagLabel();
                    }
                    $output.= $this->tagRender();
                    $output.= $this->tagHelper();
                    break;
            }

            return $output;
        }


        /**
         * ----------------------------------------
         * Type & Config Definition
         * ----------------------------------------
         */

        /**
         * Definition
         */
        private function setDefinition(array $type)
        {
            // Default definition
            $definition = array();

            if (!empty($type))
            {
                $definition = $type;
            }

            // Set Definition
            $this->definition = $definition;

            return $this;
        }
        protected function getDefinition(string $key = '')
        {
            if (!empty($key) && isset($this->definition[$key]))
            {
                return $this->definition[$key];
            }

            return null;
        }

        /**
         * Attribute definition
         */
        private function setAttrs()
        {
            $attrs = $this->getDefinition('attr');

            if ($attrs == null)
            {
                $attrs = array();
            }

            $this->attrs = $attrs;

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
         * Rules definition
         */
        private function setRules()
        {
            $rules = $this->getDefinition('rules');

            if ($rules == null)
            {
                $rules = array();
            }

            $this->rules = $rules;

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
         * Plugin Namespace
         */
        private function setNamespace()
        {
            $this->namespace = $this->getDefinition('_namespace');

            return $this;
        }
        protected function getNamespace()
        {
            return $this->namespace;
        }

        /**
         * Plugin Posttype
         */
        private function setPosttype()
        {
            $this->posttype = $this->getDefinition('_posttype');

            return $this;
        }
        protected function getPosttype()
        {
            return $this->posttype;
        }

        /**
         * Session Instance
         */
        private function setSession()
        {
            $this->session = new Session($this->getNamespace());

            return $this;
        }
        public function getSession()
        {
            return $this->session;
        }

        /**
         * ----------------------------------------
         * Tags Definition (label, helper, ...)
         * ----------------------------------------
         */

        /**
         * Default Tag pattern
         */
        protected function tag()
        {
            return $this->tagInput();
        }

        /**
         * Get the string of attributes of a tag
         */
        private function attributes()
        {
            $attr = '';

            foreach (static::ATTRIBUTES as $attribute) 
            {
                $method = 'getAttr'.Strings::ucfirst($attribute);

                $attr.= $this->$method();
            }

            return $attr;
        }

        /**
         * Tag rendering
         */
        public function tagRender()
        {
            return preg_replace("/{attributes}/", $this->attributes(), $this->tag());
        }

        /**
         * Default Input
         */
        protected function tagInput()
        {
            return '<input{attributes} />';
        }

        /**
         * Formated tag Helper
         */
        protected function tagHelper()
        {
            return $this->getHelper();
        }

        /**
         * Formated tag <label>
         */
        private function tagLabel()
        {
            if (in_array(Misc::get_called_class_name(get_called_class()), ['checkbox', 'radio', 'option']))
            {
                return false;
            }

            $tag = '<label{attributes}>{label}{required}</label>';

            if (!empty($this->getId())) { 
                $tag = preg_replace("/{attributes}/", ' for="'.$this->getId().'"', $tag);
            }

            if (!empty($this->getLabel())) {
                $tag = preg_replace("/{label}/", $this->getLabel(), $tag);
            }

            if ($this->getRequired()) { 
                $tag = preg_replace("/{required}/", ' <span>*</span>', $tag);
            }

            $tag = preg_replace("/{(:?\attributes|label|required)}/", null, $tag);

            return $tag;
        }

        /**
         * ----------------------------------------
         * Attributes Definition
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
         * Choices
         */
        protected function setChoices(array $choices = [])
        {
            $definition = $this->getDefinition('choices');



            if (is_array($definition))
            {
                $choices = array_merge($choices, $definition);
            }

            $this->choices = $choices;

            return $this;
        }
        protected function getChoices()
        {
            return $this->choices;
        }

        /**
         * Class
         */
        protected function setClass()
        {
            // Default class
            $_class = 'ppm-control';
            
            if (is_admin())
            // ($this->template_type == 'metabox' || $this->template_type == 'collection')&&
            {
                $_class.= ' '.self::CLASS_REGULAR_TEXT;
            }

            // Is Required
            if ($this->getRequired())
            {
                $_class.= ' '.self::CLASS_REQUIRED;
            }

            // Is Readonly
            if ($this->getReadonly())
            {
                $_class.= ' '.self::CLASS_READONLY;
            }

            // Is Disabled
            if ($this->getDisabled())
            {
                $_class.= ' '.self::CLASS_DISABLED;
            }

            // Add class error
            // $session = new Session($this->getConfig('namespace'));
            // foreach ($session->errors($this->getConfig('post_type')) as $error) 
            // {
            //     if (isset($error['key']) && $error['key'] == $this->getConfig('key')) 
            //     {
            //         $this->class.= ' '.self::CLASS_ERROR;
            //     }
            // }

            // Retrive Class parameters
            $class = $this->getAttr('class');

            if (is_string($class))
            {
                $_class.= ' '.$class;
            }

            $this->class = trim($_class);

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
         * Dirname
         */
        protected function setDirname()
        {
            // Default readonly
            $this->dirname = false;

            // Retrive Disabled parameters
            $dirname = $this->getAttr('dirname');

            if (is_bool($dirname))
            {
                $this->dirname = $dirname;
            }

            return $this;
        }
        protected function getDirname()
        {
            return $this->dirname;
        }
        protected function getAttrDirname()
        {
            return $this->getDirname() ? ' dirname="'.$this->getName().'.dir"' : null;
        }

        /**
         * Expanded
         */
        protected function setExpanded()
        {
            // Default expanded
            $this->expanded = false;

            // Retrive Readonly parameters
            $expanded = $this->getDefinition('expanded');

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
         * Helper
         */
        protected function setHelper()
        {
            // Default helper
            $this->helper = [];

            // // Retrieve value from session (after submission)
            // $session = new Session($this->getConfig('namespace'));
            // foreach ($session->errors($this->getConfig('post_type')) as $error) 
            // {
            //     if (isset($error['key']) && $error['key'] == $this->getConfig('key')) 
            //     {
            //         array_push($this->helper, ["notice", $error['message']]);
            //     }
            // }

            // -- Helper defined in config.php
            $helper = $this->getDefinition('helper');

            if (is_string($helper))
            {
                array_push($this->helper, ["normal", $helper]);
            }

            return $this;
        }
        protected function getHelper(bool $asString = true)
        {
            $helper = $this->helper;

            if ($asString)
            {
                $helperHTML = '';

                foreach ($helper as $item) 
                {
                    $helperHTML.= '<p class="description ppm-description';
                    $helperHTML.= ('notice' == $item[0]) ? ' '.self::CLASS_ERROR : '';
                    $helperHTML.= '">'.$item[1].'</p>';
                }

                $helper = $helperHTML;
            }

            return $helper;
        }

        /**
         * ID
         */
        protected function setId(string $id = '')
        {
            // Id is defined on config.php
            if (null == $id)
            {
                $id = $this->getAttr('id');
            }

            // Generate id from the config Key
            if (null == $id)
            {
                $id = $this->getDefinition('key');
            }

            // if ('collection' == $this->template_type)
            // {
            //     $id.= '-{{number}}';
            // }

            $this->id = $id;

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
        private function setLabel()
        {
            $this->label = $this->getDefinition('label');

            return $this;
        }
        protected function getLabel()
        {
            return $this->label;
        }

        /**
         * List
         * 
         * TODO: generate the List & Datalist
         */
        private function setList()
        {
            $this->list = null;

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
         * Min
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
                $name = $this->getPosttype();
                $name.= '['.$this->getDefinition('key').']';
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
            return $this->getName() ? ' name="'. $this->getName() .'"' : null;

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

            // Check if rule is a Regular Expression
            if (is_string($pattern) && Strings::isRegEx($pattern))
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
         * Preview
         */
        protected function setPreview()
        {
            // Default preview
            $this->preview = false;

            // Retrive Readonly parameters
            $preview = $this->getDefinition('preview');

            if (is_bool($preview))
            {
                $this->preview = $preview;
            }

            return $this;
        }
        protected function getPreview()
        {
            return $this->preview;
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
         */
        protected function setSchema()
        {
            // Default Schema
            $this->schema = [];

            // Retrive Schema parameters
            $schema = $this->getDefinition('schema');

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
            if (in_array($this->getType(), ['select', 'text']))
            {
                Misc::injection("<style>.wp-admin select {height: auto;}</style>", "head", "admin");
            }

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
        protected function setType(string $type = '')
        {
            if (empty($type))
            {
                $type = Misc::get_called_class_name(get_called_class());
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
        protected function setValue($value = null, $postID = null)
        {
            // Retrieve value from session (after submission)
            if (null === $value)
            {
            //     $session = new Session($this->getConfig('namespace'));
            //     foreach ($session->responses($this->getConfig('post_type')) as $key => $response) 
            //     {
            //         if ($key == $this->getConfig('key')) 
            //         {
            //             $value = $response;
            //         }
            //     }
            }            

            // Retrieve response in database
            if (null === $value && !empty(get_post()))
            {
                if (null == $postID)
                    $postID = get_post()->ID;
    
                $value = get_post_meta($postID, $this->getDefinition('key'), true);
            }

            // Retrieve Value from Config
            if (null == $value) 
            {
                $value = $this->getDefinition('default');

                if (is_string($value))
                {
                    $value = stripslashes($value);
                }
            }
    
            $this->value = $value;

            return $this;
        }
        protected function getValue()
        {
            return $this->value;
        }
        protected function getAttrValue()
        {
            $value = $this->getValue();

            if (!is_array($value) && $value)
            {
                return ' value="'.$this->getValue().'"';
            }

            return null;
        }





















    //     /**
    //      * ----------------------------------------
    //      * Options and Attribute Getters / Setters
    //      * ----------------------------------------
    //      */

    //     /**
    //      * Loop
    //      */
    //     protected function setLoop($loop = null)
    //     {
    //         // default loop value
    //         $this->loop = 1;

    //         if (null === $loop)
    //         {
    //             $loop = $this->getRule('init');
    //         }

    //         if (is_int($loop) && $loop >= 0 ) 
    //         {
    //             $this->loop = $loop;
    //         }

    //         return $this;
    //     }
    //     protected function getLoop()
    //     {
    //         return $this->loop;
    //     }

    //     /**
    //      * Width
    //      */
    //     protected function setWidth()
    //     {
    //         // Default Width
    //         $this->width = null;

    //         // Retrive rows parameters
    //         $width = $this->getAttr('width');

    //         if (is_int($width))
    //         {
    //             $this->width = $width;
    //         }

    //         return $this;
    //     }
    //     protected function getWidth()
    //     {
    //         return $this->width;
    //     }
    //     protected function getAttrWidth()
    //     {
    //         return $this->getWidth() ? ' width="'.$this->getWidth().'"' : null;
    //     }
    }
}
