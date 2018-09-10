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
            
            $this->namespace = $namespace;

            // Retrieve the array Post data
            $this->setPost($post);

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
         * Definition
         */
        private function setDefinition()
        {
            $definition = array();
            $ui = $this->getPost('ui');

            if(isset($ui['pages']['edit']['metaboxes']))
            {
                $definition = $ui['pages']['edit']['metaboxes'];
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
         * Current Post configuration
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
         * Generate Metabox ID
         * 
         * @param array $metabox
         */
        private function getID(array $metabox)
        {
            return 'metabox_'.$this->getPost('type').'_'.$metabox['key'];
        }

        /**
         * Get the metabox title
         * 
         * @param array $metabox
         */
        private function getTitle(array $metabox)
        {
            $title = self::DEFAULT_TITLE;

            if (isset($metabox['title'])) 
            {
                $title = $metabox['title'];
            }

            return $title;
        }

        /**
         * Get the metabox Context
         * 
         * @param array $metabox
         */
        private function getContext(array $metabox)
        {
            $context = "normal";

            if (isset($metabox['context']) && in_array($metabox['context'], self::CONTEXT))
            {
                $context = $metabox['context'];
            }

            return $context;
        }

        /**
         * Get the metabox Priority
         * 
         * @param array $metabox
         */
        private function getPriority(array $metabox)
        {
            $priority = "high";

            if (isset($metabox['priority']) && !in_array($metabox['priority'], self::PRIORITY))
            {
                $priority = $metabox['priority'];
            }

            return $priority;
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

            // Array of valid fields
            $schema_fields = array();

            foreach ($this->getDefinition() as $metabox) 
            {
                if (
                    isset($metabox['key']) && 
                    isset($metabox['display']) && is_bool($metabox['display']) && $metabox['display'] == true &&
                    isset($metabox['schema']) && is_array($metabox['schema'])
                ) 
                {
                    foreach ($metabox['schema'] as $field) 
                    {
                        array_push($schema_fields, $field);
                    }
                }
            }

            // Check field settings
            $post_schema = $this->getPost('schema');
            foreach ($post_schema as $field) 
            {
                if (
                    isset($field['key']) && 
                    in_array ($field['key'], $schema_fields) &&
                    isset($field['type']) && $field['type'] == 'file'
                )
                {
                    $enctype = true;
                }
            }

            return $enctype;
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
         * ----------------------------------------
         * Metaboxes Actions / Hooks
         * ----------------------------------------
         */

        /**
         * Add Metaboxes
         */
        public function add_meta_boxes()
        {
            foreach ($this->getDefinition() as $metabox) 
            {
                // Default display
                $display = true;

                $metabox['post_type'] = $this->getPost('type');

                // Define the display
                if (isset($metabox['key']) && isset($metabox['display']) && is_bool($metabox['display'])) 
                {
                    $display = $metabox['display'];
                }

                if ($display)
                {
                    $metabox['id'] = $this->getID($metabox);
                    
                    if (self::DEFAULT_TITLE == $this->getTitle($metabox))
                    {
                        Misc::injection("<style>#".$metabox['id']." .hndle {display: none;}</style>", "head", "admin");
                    }

                    // Add metabox to the register
                    add_meta_box( 
                        $metabox['id'], 
                        $this->getTitle($metabox), 
                        [$this, 'set_meta_box_content'], 
                        $this->getPost('type'), 
                        $this->getContext($metabox), 
                        $this->getPriority($metabox), 
                        $metabox
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

            if (isset($metabox['description']))
            {
                $content.= '<div class="metabox-header">';
                $content.= '<p>'.$metabox['description'].'</p>';
                $content.= '</div>';
            }

            $content.= '<table class="form-table">';
            $content.= '<tbody>';

            foreach ($metabox['schema'] as $key) 
            {
                foreach ($this->getPost('schema') as $type) 
                {
                    if ($type['key'] === $key && in_array($type['type'], Types::ALLOWED))
                    {
                        $type['_posttype'] = $metabox['post_type'];
                        $type['_namespace'] = $this->namespace;
    
                        $classname = ucfirst(strtolower($type['type']));
                        $classname = '\\Components\\Form\\Types\\'.$classname;
                        
                        $type = new $classname($type, 'metabox');
                        $content.= $type->render();
                        continue;
                    }
                }
            }

            $content.= '</tbody>';
            $content.= '</table>';

            echo $content;
        }
    }
}