<?php

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Request;
use \Components\Form\Types;
use \Components\Utils\Arrays;
use \Components\Utils\Misc;
use \Components\Utils\Strings;

if (!class_exists('Register\Metaboxes'))
{
	class Metaboxes
	{
        /**
         * Available Context
         */
        const CONTEXT = ['normal', 'side', 'advanced'];

        /**
         * Default Title of metaboxes
         */
        const DEFAULT_TITLE = '_no_title_';

        /**
         * Available Priority
         */
        const PRIORITY = ['high', 'low'];
        
        /**
         * Supports definition
         */
        const SUPPORTS = ['title', 'editor', 'comments', 'revisions', 'trackbacks', 'author', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', 'post-formats'];

        /**
         * Metabox definition
         * 
         * this is the definition of metaboxes
         */
        private $definition;

        /**
         * Plugin namespace
         */
        private $namespace;

        /**
         * Metaboxes Options 
         */
        private $options;

        /**
         * The post data
         * 
         * @param array
         */
        private $post;

        /**
         * Insance of Request
         */
        private $request;

        /**
         * Post Schemas definirion
         */
        private $schemas;

        /**
         * Supports fields
         */
        private $supports;

        /**
         * Constructor
         */
        public function __construct(array $post, string $namespace)
        {
            // Request data
            $this->request = new Request;
            
            // Set the Plugin Namespace
            $this->namespace = $namespace;

            // Retrieve the array Post data
            $this->setPost($post);

            // List of defined Post Schema
            $this->setSchemas();

            // Define definition
            $this->setDefinition();

            // Retrieve Supports
            $this->setSupports();

            // Retrieve Options for all Metaboxes
            $this->setOptions();

            if ($this->request->post('type') == $this->getPost('type'))
            {
                // Add metaboxes to the WP register
                add_action('add_meta_boxes',[$this, 'add_meta_boxes']);
        
                // Form tag
                add_action('post_edit_form_tag', [$this, 'post_edit_form_tag']);
        
                // Metabox Options
                add_action( 'admin_init', [$this, 'set_meta_boxes_options']);
            }
        }

        /**
         * ----------------------------------------
         * Metabox Definition
         * ----------------------------------------
         */

        /**
         * Definition
         */
        private function setDefinition()
        {
            $definition = array();
            $ui = $this->getPost('ui');

            if(isset($ui['pages']['edit']['metaboxes']) && is_array($ui['pages']['edit']['metaboxes']))
            {
                $definition = $ui['pages']['edit']['metaboxes'];
            }

            foreach ($definition as $key => $item) 
            {
                // Define the key
                $item = $this->setKey($item);

                // Define the context
                $item = $this->setContext($item);

                // Define the priority
                $item = $this->setPriority($item);

                // Define the Description
                $item = $this->setDescription($item);

                // Define if is displayed
                $item = $this->setDisplay($item);

                // Define the ID
                $item = $this->setID($item);

                // Define the PostType
                $item = $this->setPostType($item);

                // Define the title
                $item = $this->setTitle($item);

                // Define the schema
                $item = $this->setSchema($item);

                // Define if Metabox has a file field type
                $item = $this->setEnctype($item);

                $definition[$key] = $item;
            }

            $this->definition = $definition;

            return $this;
        }
        private function getDefinition(string $key = '')
        {
            if (!empty($key) && isset($this->definition[$key]))
            {
                return $this->definition[$key];
            }

            // return null;
            return $this->definition;
        }
        public function unsetDefinition(int $key)
        {
            if (isset($this->definition[$key]))
            {
                unset($this->definition[$key]);
            }
        }

        /**
         * Supports
         */
        private function setSupports()
        {
            if (!is_array($this->supports))
            {
                $this->supports = array();
            }

            foreach ($this->getDefinition() as $key => $definition) 
            {
                if (isset($definition['key']) && in_array($definition['key'], self::SUPPORTS))
                {
                    array_push( $this->supports, $definition );
                    $this->unsetDefinition($key);
                }
            }

            return $this;
        }
        public function getSupports()
        {
            return $this->supports;
        }


        /**
         * ----------------------------------------
         * Metabox Definition
         * ----------------------------------------
         */

        /**
         * Context
         */
        private function setContext(array $item)
        {
            $context = "normal";

            if (isset($item['context']) && in_array($item['context'], self::CONTEXT))
            {
                $context = $item['context'];
            }

            $item['context'] = $context;

            return $item;
        }

        /**
         * Display
         */
        private function setDisplay(array $item)
        {
            $display = true;

            if (isset($item['display']) && is_bool($item['display']))
            {
                $display = $item['display'];
            }

            $item['display'] = $display;

            return $item;
        }

        /**
         * Description
         */
        private function setDescription(array $item)
        {
            $description = null;

            if (isset($item['description']))
            {
                $description = $item['description'];
            }

            $item['description'] = $description;

            return $item;
        }

        /**
         * 
         */
        private function setEnctype(array $item)
        {
            $enctype = false;

            $schemas = $this->getSchemas();

            foreach ($item['schema'] as $key => $name) 
            {
                if (isset($schemas[$name]) && $schemas[$name]['type'] === 'file')
                {
                    $enctype = true;
                }
            }

            $item['enctype'] = $enctype;

            return $item;
        }

        /**
         * ID
         */
        private function setID(array $item)
        {
            $id = null;

            if ($item['key'] != null)
            {
                $id = 'metabox_'.$this->getPost('type').'_'.$item['key'];
            }
            $item['id'] = $id;
            
            return $item;
        }

        /**
         * Key
         */
        private function setKey(array $item)
        {
            $key = null;

            if (isset($item['key']))
            {
                $key = $item['key'];
            }

            $item['key'] = $key;

            return $item;
        }

        /**
         *  Metaboxes Options
         */
        private function setOptions()
        {
            $options = array();
            $ui = $this->getPost('ui');

            if (isset($ui['pages']['edit']['metaboxes_options']))
            {
                $options = $ui['pages']['edit']['metaboxes_options'];
            }

            $this->options = $options;

            return $this;
        }
        private function getOptions(string $key = '')
        {
            if (!empty($key) && isset($this->options[$key]))
            {
                return $this->options[$key];
            }

            return null;
        }

        /**
         * Post
         */
        private function setPost(array $post)
        {
            $this->post = $post;

            return $this;
        }
        private function getPost(string $key = '')
        {
            if (isset($this->post[$key])) 
            {
                return $this->post[$key];
            }

            return null;
        }

        /**
         * Post Type
         */
        private function setPostType(array $item)
        {
            $item['posttype'] = $this->getPost('type');

            return $item;
        }

        /**
         * Priority
         */
        private function setPriority(array $item)
        {
            $priority = "high";

            if (isset($item['priority']) && in_array($item['priority'], self::PRIORITY))
            {
                $priority = $item['priority'];
            }

            $item['priority'] = $priority;

            return $item;
        }

        /**
         * Title
         */
        private function setTitle(array $item)
        {
            $title = self::DEFAULT_TITLE;

            if (isset($item['title']))
            {
                $title = $item['title'];
            }

            $item['title'] = $title;

            return $item;
        }

        /**
         * Schema
         */
        private function setSchema(array $item)
        {
            $schema = [];

            if (isset($item['schema']))
            {
                if (!is_array($item['schema']))
                {
                    $item['schema'] = [$item['schema']];
                }

                $schema = $item['schema'];
            }

            $schemas = $this->getSchemas();

            foreach ($schema as $key => $name) 
            {
                if (!isset($schemas[$name]))
                {
                    unset($schema[$key]);
                }
            }

            $item['schema'] = $schema;

            return $item;
        }
        private function setSchemas()
        {
            $schemas = [];

            foreach ($this->getPost('schema') as $type) 
            {
                $schemas[$type['key']] = $type;
            }

            $this->schemas = $schemas;

            return $this;
        }
        private function getSchemas()
        {
            return $this->schemas;
        }


        /**
         * ----------------------------------------
         * Form is? / has?
         * ----------------------------------------
         */

        /**
         * Is Metabox Sortable
         * 
         * @param array $metabox
         */
        private function isSortable()
        {
            // Default Sortable
            $sortable = true;

            if (is_bool($this->getOptions('sortable')))
            {
                $sortable = $this->getOptions('sortable');
            }

            return $sortable;
        }

        /**
         * Is NoValidate
         * 
         * @return boolean
         */
        private function isNoValidate()
        {
            // Default No Validate
            $novalidate = true;
            
            // Define NoValidate
            $ui = $this->getPost('ui');

            if (isset($ui['pages']['edit']['form']['novalidate']) && is_bool($ui['pages']['edit']['form']['novalidate']))
            {
                $novalidate = $ui['pages']['edit']['form']['novalidate'];
            }
            
            return $novalidate;
        }

        /**
         * Has Enctype
         * 
         * @return boolean
         */
        public function hasEnctype()
        {
            // Default Enctype
            $enctype = false;

            foreach ($this->getDefinition() as $item) 
            {
                if (true === $item['enctype'])
                {
                    $enctype = true;
                }
            }

            return $enctype;
        }


        /**
         * ----------------------------------------
         * Metaboxes Actions / Hooks
         * ----------------------------------------
         */

        /**
         * Add Metaboxes
         */
        public function add_meta_boxes()
        {
            foreach ($this->getDefinition() as $item) 
            {
                if (!empty($item['id']) && !empty($item['key']) && $item['display'] === true)
                {
                    if (self::DEFAULT_TITLE == $item['title'])
                    {
                        Misc::injection("<style>#".$item['id']." .hndle {display: none;}</style>", "head", "admin");
                    }

                    // Add metabox to the register
                    add_meta_box( 
                        $item['id'],
                        $item['title'],
                        [$this, 'set_meta_box_content'],
                        $item['posttype'],
                        $item['context'],
                        $item['priority'],
                        $item
                    );
                }
            }
        }

        /**
         * Edit Form Tag
         * 
         * add NoValidate
         * add Enctype
         */
        public function post_edit_form_tag()
        {
            // Add No Validate
            if ($this->isNoValidate())
            {
                echo ' novalidate="novalidate"';
            }

            // Add Enctype
            if ($this->hasEnctype())
            {
                echo ' enctype="multipart/form-data"';
            }
        }

        /**
         * Metaboxes Options
         */
        public function set_meta_boxes_options()
        {
            // Unset metabox sortable
            if (!$this->isSortable())
            {
                wp_deregister_script('postbox');

                // Reset Cursor as default
                Misc::injection("<style>.js .postbox .hndle, .js .widget .widget-top {cursor: default;}</style>", "head", "admin");

                // Remove arrow for toggle show/hide the metabox
                Misc::injection("<style>.js .postbox .handlediv {display: none;}</style>", "head", "admin");
            }
        }

        /**
         * Content of metabox
         */
        public function set_meta_box_content($wp_post, $args)
        {
            // Define the output
            $content = '';

            // Retrieve the Metabox data
            $metabox = $args['args'];

            // retrieve the Defined Post Schema
            $schemas = $this->getSchemas();

            // -- Print  Metabox Description
            if (isset($metabox['description']))
            {
                $content.= '<div class="metabox-header">';
                $content.= '<p>'.$metabox['description'].'</p>';
                $content.= '</div>';
            }

            // -- Print Metabox Fields
            $content.= '<table class="form-table">';
            $content.= '<tbody>';
            foreach ($metabox['schema'] as $name) 
            {
                if (isset($schemas[$name]))
                {
                    $type = $schemas[$name];

                    $type['_posttype'] = $metabox['posttype'];
                    $type['_namespace'] = $this->namespace;

                    $classname = Strings::ucfirst($type['type']);
                    $classname = Types::BASE.$classname;
                    
                    $type = new $classname($type, 'metabox');
                    $content.= $type->render();
                    continue;
                }
            }
            $content.= '</tbody>';
            $content.= '</table>';

            echo $content;
        }
    }
}