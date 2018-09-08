<?php

// TODO : Gestion des effet si PostType : 'post' ou 'page'

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Components\Form\Types;
use \Components\Utils\Arrays;
use \Components\Utils\Misc;
use \Components\Utils\Strings;
use \Kernel\Request;
use \Kernel\Session;
use \Register\Assets;
use \Register\Categories;
use \Register\Tags;

if (!class_exists('Register\Posts'))
{
	class Posts
	{
        /**
         * Capapility Types
         */
        const CAPABILITY_TYPE = ['post', 'page'];

        /**
         * Default supports
         */
        const DEFAULT_SUPPORTS = ['title', 'editor'];

        /**
         * Available EndPoint Mask
         */
        const ENDPOINT_MASK = ["EP_NONE", "EP_PERMALINK", "EP_ATTACHMENT",  "EP_DATE", "EP_YEAR", "EP_MONTH", "EP_DAY", "EP_ROOT",  "EP_COMMENTS", "EP_SEARCH", "EP_CATEGORIES", "EP_TAGS",  "EP_AUTHORS", "EP_PAGES", "EP_ALL_ARCHIVES", "EP_ALL"];

        /**
         * Max size for the custom post type identifier
         */
        const POSTTYPE_SIZE = 20;

        /**
         * Collections (type) register
         */
        private $collections;

        /**
         * Definition of merged Posts from Core & Plugin
         * 
         * @param array
         */
        private $definition;

        /**
         * The instance of Kernel
         * 
         * Content instance of Core & Plugin
         * @param array
         */
        private $kernel;

        /**
         * Current post 
         * 
         * @param array
         */
        private $post;

        /**
         * Posts register
         * 
         * @param array
         */
        private $posts;

        /**
         * Session data
         * 
         * @param object Instance of Session
         */
        private $session;

        /**
         * Types register
         * 
         * @param array
         */
        private $types;



















        // /**
        //  * List of custom post parameters we want to internationalize
        //  */
        // const LABELS_UI = ['singular_name','add_new','add_new_item','edit_item',
        //     'new_item','view_item','view_items','search_items','not_found',
        //     'not_found_in_trash','parent_item_colon','all_items','archives',
        //     'attributes','insert_into_item','uploaded_to_this_item',
        //     'featured_image','set_featured_image','remove_featured_image',
        //     'use_featured_image','menu_name','filter_items_list',
        //     'items_list_navigation','items_list'];


        // /**
        //  * Responses data
        //  * 
        //  * @param array
        //  */
        // private $responses = array();

        /**
         * Constructor
         */
        public function __construct($kernel)
        {
            // Retrieve instance of Kernel
            $this->kernel = $kernel;

            // Request data
            $this->request = new Request;

            // Session data
            $this->session = new Session($this->kernel->getPlugin()->getConfig('namespace'));

            // Posts definition
            $this->setDefinition($this->kernel->getCore());
            $this->setDefinition($this->kernel->getPlugin());

            // Define Formated Posts
            $this->setPosts();

            // Define Types
            $this->setTypes();

            // Load Posts
            foreach ($this->getPosts() as $post) 
            {
                // Set current Post in loop
                $this->setPost($post);

                // // Define the Label(s)
                $post = $this->setLabel($post);
                $post = $this->setLabels($post);
                
                // Define the Description
                // $post['description']
                $post = $this->setDescription($post);

                // Is public post ?
                // $post['public']
                $post = $this->setPublic($post);

                // Is Hierarchical post ?
                // $post['hierarchical']
                $post = $this->setHierarchical($post);

                // Show UI
                // $post['show_ui']
                $post = $this->setShowUI($post);

                // Show in Menu
                // $post['show_in_menu'] 
                $post = $this->setShowInMenu($post);

                // Show the custom posts in Menus Manager
                // $post['show_in_nav_menus']
                $post = $this->setShowInNavMenus($post);

                // Show in Menu Bar (topbar)
                // $post['show_in_admin_bar']
                $post = $this->setShowInMenuBar($post);

                // Menu Position
                // $post['menu_position']
                $post = $this->setMenuPosition($post);

                // Menu Icon
                // $post['menu_icon']
                $post = $this->setMenuIcon($post);

                // Has Archive
                // $post['has_archive']
                $post = $this->setHasArchive($post);

                // Define if is exportable
                // $post['can_export']
                $post = $this->setCanExport($post);

                // Define query rules
                // $post['query']
                $post = $this->setQuery($post);

                // REST
                // $post['show_in_rest']
                // $post['rest_base']
                // $post['rest_controller_class']
                $post = $this->setREST($post);

                // Define rewrite rules
                // $post['rewrite']
                $post = $this->setRewrite($post);

                // Define Capabilities
                $post = $this->setCapability($post);

                // Define Edit Link (_edit_link)
                $post = $this->setEditLink($post);

                // Define Categories
                new Categories($post);

                // Define Tags
                new Tags($post);

                // Add Metaboxes (and supports)
                // $metaboxes = new Metaboxes($post, $this->kernel->getPlugin()->getConfig('namespace'));
                $metaboxes = new Metaboxes($post);
                $supports = $metaboxes->getSupports();

                // Define Supports
                $post = $this->setSupports($supports, $post);

                // TODO : 'map_meta_cap'
                // (bool) Whether to use the internal default meta capability handling. Default false.

                // TODO : 'register_meta_box_cb'
                // (callable) Provide a callback function that sets up the meta boxes for the edit form. Do remove_meta_box() and add_meta_box() calls in the callback. Default null.

                // TODO : '_builtin'
                // (bool) FOR INTERNAL USE ONLY! True if this post type is a native or "built-in" post_type. Default false.

                // Internationalize the post data
                // $post = $this->i18n($post);

                // Add the custom post to the register
                register_post_type( $post['type'], $post );

                // Create shortcodes
                $this->setShortcodes($post);
                
                if ($this->request->post('type') == $post['type'])
                {
            //         // -- Posts list

            //         add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ]);

                    // Manage Post Column 
                    add_filter( "manage_{$post['type']}_posts_columns", function($columns) use ($post) { return $this->manage_posts_columns($columns, $post); });
                    add_action( "manage_{$post['type']}_posts_custom_column" , function($column, $id) use ($post) { $this->manage_posts_custom_column($column, $id, $post); }, 10, 2 );
                    add_filter( "manage_edit-{$post['type']}_sortable_columns", function($columns) use ($post) { return $this->manage_sortable_columns($columns, $post); });

                    // Menu action on Admin index rows
                    add_filter('post_row_actions', function($actions) use ($post) { return $this->post_row_actions($actions, $post); }, 10, 1);
    

            //         // -- Posts Edit

            //         // Post submission
            //         add_action('pre_post_update', array($this, "pre_post_update"));
    
            //         // Notice (flashbag)
            //         add_action('admin_notices', array(new Notices($this->bs->getNamespace()), "get"));
    
            //         // Clear the post session
            //         add_action('clear_post_session', function() use ($post) { $this->clear_post_session($post); });
            //         add_action('wp_footer', [$this, "clear_post_session"], 10);
            //         add_action('admin_footer', [$this, "clear_post_session"], 10);


                    // -- Misc Options

                    add_filter('screen_options_show_screen', function() use ($post) { return $this->screen_options_show_screen($post); });
                }
            }
        }

        /**
         * ----------------------------------------
         * Posts Definition
         * ----------------------------------------
         */

        /**
         * Definition 
         * 
         * Retrieve definition of both Core & Plugin "Posts"
         */
        private function setDefinition($context)
        {
            if (!is_array($this->definition))
            {
                $this->definition = array();
            }

            foreach ($context->getConfig('posts') as $definition) 
            {
                if (is_array($definition))
                {
                    $definition['context'] = $context;
                    // $definition['context'] = $context->getConfig('context');
                    array_push($this->definition, $definition);
                }
            }

            return $this;
        }
        private function getDefinition()
        {
            return $this->definition;
        }

        /**
         * Posts
         * 
         * Retrieve valid Posts only (from definition)
         */
        private function setPosts()
        {
            // Retrieve Posts definition
            $posts = $this->getDefinition();

            // Add each definition to $this->posts
            foreach ($posts as $post) 
            {
                $this->addPosts($post);
            }

            return $this;
        }
        public function getPosts( string $type = '' )
        {
            if (!empty($type))
            {
                foreach ($this->posts as $post) 
                {
                    if ($post['type'] == $type)
                    {
                        $this->posts = $post;
                        continue;
                    }
                }
            }

            return $this->posts;
        }
        private function addPosts(array $post)
        {
            if (!is_array($this->posts))
            {
                $this->posts = array();
            }

            // Check the Post Type and Post Existance
            if ($this->isValidPostType($post) && !$this->postExists($post['type']))
            {
                array_push($this->posts, $post);
            }

            return $this;
        }

        /**
         * Current Post
         */
        private function setPost(array $post)
        {
            $this->post = $post;

            return $this;
        }
        private function getPost(string $key = '')
        {
            if (!empty($key) && isset($this->post[$key]))
            {
                return $this->post[$key];
            }

            return null;
        }

        /**
         * Types
         * 
         * Retrieve Posts Type (Posts Fields definition)
         */
        private function setTypes()
        {
            if (!is_array($this->types))
            {
                $this->types = array();
            }

            // Retrieve each Posts
            foreach ($this->posts as $post) 
            {
                // Search for Schema parameter
                if (isset($post['schema']) && is_array($post['schema']))
                {
                    // Retrieve each Types
                    foreach ($post['schema'] as $type) 
                    {
                        $this->addType($type, $post);
                    }
                }
            }

            // Rebuild Collection Type
            $this->setCollectionTypes();

            // Add Virtual post
            foreach ($this->types as $posttype => $types) 
            {
                foreach ($types as $index => $type) 
                {
                    if ('collection' == $type['type'])
                    {
                        $vp_type = $posttype.".".$type['key'];
                        $vp_hash = substr(hash("sha256", $vp_type), 0, 10);
                        $vp_name = "vp_".$vp_hash;

                        $this->addPosts([
                            'type' => $vp_name,
                            'name' => $vp_name,
                            'public' => false,
                            // 'public' => true,
                            // 'publicly_queryable' => false,
                            'schema' => [$type]
                        ]);
                    }
                }
            }

            foreach ($this->posts as $key => $post) 
            {
                // Search for Schema parameter
                if (isset($this->posts[$key]['schema']) && is_array($this->posts[$key]['schema']))
                {
                    if (isset($this->types[$post['type']]))
                    {
                        $this->posts[$key]['schema'] = [];
                        // $this->types[$post['type']];
                        foreach ($this->types[$post['type']] as $type) 
                        {
                            array_push($this->posts[$key]['schema'], $type);
                        }
                    }
                }
            }

            return $this;
        }
        private function getTypes()
        {
            return $this->types;
        }
        public function addType(array $type, array $post)
        {
            if (!isset($this->types[$post['type']]) || !is_array($this->types[$post['type']]))
            {
                $this->types[$post['type']] = array();
            }

            if ($this->isValidType($type))
            {
                // Add Type to $this->types register
                $this->types[$post['type']] += [
                    $type['key'] => $this->formatType($type)
                ];
            }

            return $this;
        }
        private function formatType(array $type)
        {
            // Define Types default values
            $type['value']                  = null;
            $type['type']                   = isset($type['type'])                      ? $type['type']                     : "text";
            $type['key']                    = isset($type['key'])                       ? $type['key']                      : null;
            $type['label']                  = isset($type['label'])                     ? $type['label']                    : null;
            $type['default']                = isset($type['default'])                   ? $type['default']                  : null;
            $type['helper']                 = isset($type['helper'])                    ? $type['helper']                   : null;
            $type['rules']['pattern']       = isset($type['rules']['pattern'])          ? $type['rules']['pattern']         : null;
            $type['rules']['size']          = isset($type['rules']['size'])             ? $type['rules']['size']            : null;
            $type['rules']['allowed_types'] = isset($type['rules']['allowed_types'])    ? $type['rules']['allowed_types']   : null;
            $type['attr']['id']             = isset($type['attr']['id'])                ? $type['attr']['id']               : null;
            $type['attr']['placeholder']    = isset($type['attr']['placeholder'])       ? $type['attr']['placeholder']      : null;
            $type['attr']['class']          = isset($type['attr']['class'])             ? $type['attr']['class']            : null;
            $type['attr']['maxlength']      = isset($type['attr']['maxlength'])         ? $type['attr']['maxlength']        : null;
            $type['attr']['max']            = isset($type['attr']['max'])               ? $type['attr']['max']              : null;
            $type['attr']['min']            = isset($type['attr']['min'])               ? $type['attr']['min']              : null;
            $type['attr']['step']           = isset($type['attr']['step'])              ? $type['attr']['step']             : null;
            $type['attr']['width']          = isset($type['attr']['width'])             ? $type['attr']['width']            : null;
            $type['attr']['cols']           = isset($type['attr']['cols'])              ? $type['attr']['cols']             : null;
            $type['attr']['rows']           = isset($type['attr']['rows'])              ? $type['attr']['rows']             : null;
            $type['attr']['required']       = isset($type['attr']['required'])          ? $type['attr']['required']         : false;
            $type['attr']['readonly']       = isset($type['attr']['readonly'])          ? $type['attr']['readonly']         : false;
            $type['attr']['disabled']       = isset($type['attr']['disabled'])          ? $type['attr']['disabled']         : false;
            $type['attr']['multiple']       = isset($type['attr']['multiple'])          ? $type['attr']['multiple']         : false;
            $type['expanded']               = isset($type['expanded'])                  ? $type['expanded']                 : false;
            $type['shortcode']              = isset($type['shortcode'])                 ? $type['shortcode']                : false;
            $type['preview']                = isset($type['preview'])                   ? $type['preview']                  : true;
            $type['choices']                = isset($type['choices'])                   ? $type['choices']                  : [];
            $type['messages']               = isset($type['messages'])                  ? $type['messages']                 : [];
            $type['algo']                   = isset($type['algo'])                      ? $type['algo']                     : [];

            // Type default error messages
            $type['messages']['required']   = isset($type['messages']['required'])      ? $type['messages']['required']     : "This field is required.";
            $type['messages']['email']      = isset($type['messages']['email'])         ? $type['messages']['email']        : "This field is not a valid email.";
            $type['messages']['url']        = isset($type['messages']['url'])           ? $type['messages']['url']          : "This field is not a valid url.";
            $type['messages']['time']       = isset($type['messages']['time'])          ? $type['messages']['time']         : "This field is not a valid time.";
            $type['messages']['date']       = isset($type['messages']['date'])          ? $type['messages']['date']         : "This field is not a valid date.";
            $type['messages']['year']       = isset($type['messages']['year'])          ? $type['messages']['year']         : "This field is not a valid year.";
            $type['messages']['color']      = isset($type['messages']['color'])         ? $type['messages']['color']        : "This field is not a valid color";
            $type['messages']['confirm']    = isset($type['messages']['confirm'])       ? $type['messages']['confirm']      : "Password is not confirmed.";
            $type['messages']['pattern']    = isset($type['messages']['pattern'])       ? $type['messages']['pattern']      : "This field is not valid.";
            $type['messages']['type']       = isset($type['messages']['type'])          ? $type['messages']['type']         : "This field is not valid.";
            $type['messages']['min']        = isset($type['messages']['min'])           ? $type['messages']['min']          : "This value must not be less than $1.";
            $type['messages']['max']        = isset($type['messages']['max'])           ? $type['messages']['max']          : "This value must not be greater than $1.";
            $type['messages']['maxlength']  = isset($type['messages']['maxlength'])     ? $type['messages']['maxlength']    : "This value is too long.";
            $type['messages']['size']       = isset($type['messages']['size'])          ? $type['messages']['size']         : "This file size is not valid.";
            $type['messages']['file_types'] = isset($type['messages']['file_types'])    ? $type['messages']['file_types']   : "This file is not valid.";
            $type['messages']['captcha']    = isset($type['messages']['captcha'])       ? $type['messages']['captcha']      : "This captcha is not valid.";

            // Default algo for password
            if ('password' == $type['type']) 
            {
                // default $algo
                $algo = [
                    'type' => null,
                    'options' => []
                ];

                // retrieve algo settings
                if (!empty($type['algo'])) 
                {
                    if (is_array($type['algo'])) 
                    {
                        if (isset($type['algo']['type'])) {
                            $algo['type'] = $type['algo']['type'];
                            unset($type['algo']['type']);
                        }
                        $algo['options'] = $type['algo'];
                    }
                    elseif (is_string($type['algo'])) 
                    {
                        $algo['type'] = $type['algo'];
                    }
                }

                // Is a valid algo
                if (!in_array($algo['type'], Password::ALGO)) 
                {
                    $algo['type'] = "PASSWORD_DEFAULT";
                }

                $type['algo'] = $algo;
            }

            return $type;
        }

        /**
         * Types Collection
         */
        public function setCollectionTypes()
        {
            // Retrieve collections type
            $this->setCollections();

            // Check each collections
            $this->setSchemaCollection();
        }
        private function setCollections()
        {
            if (!is_array($this->collections))
            {
                $this->collections = array();
            }

            foreach ($this->types as $posttype => $types)
            {
                if (!isset($this->collections[$posttype]))
                {
                    $this->collections[$posttype] = array();
                }

                foreach ($types as $index => $type) 
                {
                    if ('collection' == $type['type'])
                    {
                        array_push($this->collections[$posttype], $type['key']);

                        // Prepare for rebuild recursion
                        $this->types[$posttype][$index]['_X'] = true;

                        // Define Virtual Post name
                        $vp_type = $posttype.".".$this->types[$posttype][$index]['key'];
                        $vp_hash = substr(hash("sha256", $vp_type), 0, 10);
                        $vp_name = "vp_".$vp_hash;
                        $this->types[$posttype][$index]['_VPOST'] = $vp_name;
                    }
                }
            }
        }
        private function setSchemaCollection()
        {
            $_collections = $this->collections;
            $this->collections = array();

            foreach ($_collections as $posttype => $collections)
            {
                foreach ($collections as $collection_name)
                {
                    // retrieve the schema of the collection
                    if (isset($this->types[$posttype][$collection_name]) && isset($this->types[$posttype][$collection_name]['schema']) && is_array($this->types[$posttype][$collection_name]['schema']))
                    {
                        foreach ($this->types[$posttype][$collection_name]['schema'] as $key => $collection_type) 
                        {
                            if (is_string($collection_type))
                            {
                                if (!isset($this->types[$posttype][$collection_type]))
                                {
                                    unset($this->types[$posttype][$collection_name]['schema'][$key]);
                                }

                                else
                                {
                                    if ('collection' != $this->types[$posttype][$collection_type]['type'])
                                    {
                                        $this->types[$posttype][$collection_name]['schema'][$key] = $this->types[$posttype][$collection_type];
                                    }
                                    else
                                    {
                                        if (!isset($this->types[$posttype][$collection_type]['_X']))
                                        {
                                            $this->types[$posttype][$collection_name]['schema'][$key] = $this->types[$posttype][$collection_type];
                                        }
                                    }
                                }
                            }
                        }

                        foreach (['_PARENT', '_VPOST', '_VPOST_ID'] as $_vkey) 
                        {
                            array_push($this->types[$posttype][$collection_name]['schema'], [
                                'key' => $_vkey,
                                'type' => 'hidden'
                                // 'type' => 'text'
                            ]);
                        }
                    }
                }
            }

            foreach ($_collections as $posttype => $collections)
            {
                foreach ($collections as $collection_name)
                {
                    $_RECURSION = false;

                    if (isset($this->types[$posttype][$collection_name]))
                    {
                        if (isset($this->types[$posttype][$collection_name]['schema']) && is_array($this->types[$posttype][$collection_name]['schema']))
                        {
                            foreach ($this->types[$posttype][$collection_name]['schema'] as $key => $collection_type) 
                            {
                                if (is_string($collection_type))
                                {
                                    $_RECURSION = true;
                                }
                            }
                        }
                    }

                    if (!$_RECURSION)
                    {
                        unset($this->types[$posttype][$collection_name]['_X']);
                    }
                    else
                    {
                        if (!isset($this->collections[$posttype]))
                        {
                            $this->collections[$posttype] = array();
                        }
                        array_push($this->collections[$posttype], $collection_name);
                    }
                }
            }

            if (!empty($this->collections))
            {
                $this->setSchemaCollection();
            }
        }


        /**
         * ----------------------------------------
         * Param Validation
         * ----------------------------------------
         */

        /**
         * Check Post Type
         */
        private function isValidPostType(array $post)
        {
            // Default $type
            $posttype = null;

            // Default Is Valid (true)
            $isValid = true;

            // Retrieve the type
            if (isset($post['type']) && is_string($post['type'])) 
            {
                $posttype = $post['type'];
            }

            // Define error if $posttype is empty
            if (empty($posttype)) 
            {
                $isValid = false;
                $errorMessage = "<strong>Invalid Post Type</strong> : The post type can't be empty.";
            }

            // Check chars length
            if (strlen($posttype) > self::POSTTYPE_SIZE) 
            {
                $isValid = false;
                $errorMessage = "<strong>Invalid Post Type</strong> : The post type (".$post['type'].") must have ".self::POSTTYPE_SIZE." chars max.";
            }

            // Check chars type
            if (!preg_match("/^[a-z0-9_]*$/i", $posttype))
            {
                $isValid = false;
                $errorMessage = "<strong>Invalid Post Type</strong> : The post type (".$post['type'].") must content only alpha, numeric and underscore.";
            }

            // Display error
            if (!$isValid && 'development' == $this->kernel->getPlugin()->getConfig('environment')) 
            {
                trigger_error($errorMessage, E_USER_WARNING);
            }

            return $isValid;
        }
        private function postExists(string $type)
        {
            foreach ($this->posts as $post) 
            {
                if (isset($post['type']) && null != $type && $post['type'] == $type)
                {
                    return true;
                }
            }

            return false;
        }

        /**
         * Check Types
         */
        public function isValidType(array $type)
        {
            // Default Is Valid (true)
            $isValid = true;

            // Check Type Key
            if (!isset($type['key']))
            {
                $isValid = false;
                $errorMessage = "<strong>Undefined Type Key</strong> : The type key parameter is required.";
            }

            if (isset($type['key']) && !preg_match("/^[a-z0-9_]*$/i", $type['key']))
            {
                $isValid = false;
                $errorMessage = "<strong>Invalid Type Key</strong> : The type key (".$type['key'].") must content only alpha, numeric and underscore.";
            }

            // Check Type Type
            if (!isset($type['type']))
            {
                $isValid = false;
                $errorMessage = "<strong>Undefined Type Field</strong> : The field type parameter is required. Valid types field are ".implode(", ", Types::ALLOWED).".";
            }

            if (isset($type['type']) && !in_array($type['type'], Types::ALLOWED))
            {
                $isValid = false;
                $errorMessage = "<strong>Invalid Type Field</strong> : The field type \"".$type['type']."\" is not valid. Valid types field are ".implode(", ", Types::ALLOWED).".";
            }

            // Display error
            if (!$isValid && 'development' == $this->bs->getEnvironment()) 
            {
                trigger_error($errorMessage, E_USER_WARNING);
            }

            return $isValid;
        }


        /**
         * ----------------------------------------
         * Checking and Default Post Params
         * ----------------------------------------
         */

        /**
         * Define if is exportable
         * 
         * @param array $post
         * @return array $post
         */
        private function setCanExport(array $post)
        {
            $post['can_export'] = true;

            if (isset($post['exportable']))
            {
                if (is_bool($post['exportable']))
                {
                    $post['can_export'] = $post['exportable'];
                }
                unset($post['exportable']);
            }

            return $post;
        }

        /**
         * Define Capabilities
         * 
         * @param array $capability
         * @param array $post
         * @return array $post
         */
        private function setCapabilities(array $capability, array $post)
        {
            // Default capabilities
            if (!isset($capability['capablilities']))
            {
                $type = $post['capability_type'];

                $post['capablilities'] = [
                    'edit_post' => 'edit_'.$type,
                    'read_post' => 'read_'.$type,
                    'delete_post' => 'delete_'.$type,
                    'edit_posts' => 'edit_'.$type.'s',
                    'edit_others_posts' => 'edit_others_'.$type.'s',
                    'publish_posts' => 'publish_'.$type.'s',
                    'read_private_posts' => 'read_private_'.$type.'s',
                ];
            }

            // TODO: Custom Capabilities

            return $post;
        }

        /**
         * Define Capabilities
         * 
         * @param array $post
         * @return array $post
         */
        private function setCapability(array $post)
        {
            if (!isset($post['capability']) || (!is_bool($post['capability']) && !is_array($post['capability'])))
            {
                $post['capability_type'] = 'post';
                $post['capabilities'] = [];
            }

            if (isset($post['capability']) && is_array($post['capability']))
            {
                $cap = $post['capability'];

                // Define Capability Type
                $post = $this->setCapabilityType($cap, $post);

                // Define Capabilities
                $post = $this->setCapabilities($cap, $post);
            }

            return $post;
        }

        /**
         * Define Capability type
         * 
         * @param array $capability
         * @param array $post
         * @return array $post
         */
        private function setCapabilityType(array $capability, array $post)
        {
            $post['capability_type'] = 'post';

            if (isset($capability['type']) && in_array($capability['type'], self::CAPABILITY_TYPE))
            {
                $post['capability_type'] = $capability['type'];
            }

            else if (isset($capability['type']) && preg_match("/^@/", $capability['type']))
            {
                $type = preg_replace("/^@/", null, $capability['type']);

                switch ($type)
                {
                    case 'type':
                        $post['capability_type'] = $post['type'];
                        break;
                }
            }

            return $post;
        }

        /**
         * Define if delete custom posts with user
         * 
         * @param array $query
         * @param array $post
         * @return array $post
         */
        private function setDeleteWithUser(array $query, array $post)
        {
            $post['delete_with_user'] = null;

            if (isset($query['delete_with_user']) && is_bool($query['delete_with_user']))
            {
                $post['delete_with_user'] = $query['delete_with_user'];
            }

            return $post;
        }

        /**
         * Define the description of the post
         * 
         * @param array $post
         * @return array $post
         */
        private function setDescription(array $post)
        {
            $post['description'] = isset($post['description']) ? $post['description'] : null;

            return $post;
        }

        /**
         * Define _edit_link
         * 
         * @param array $post
         * @return array $post
         */
        private function setEditLink(array $post)
        {
            $post['_edit_link'] = 'post.php?post=%d';

            if (isset($post['ui']['pages']['edit']['link']) && is_string($post['ui']['pages']['edit']['link']))
            {
                $post['_edit_link'] = $post['ui']['pages']['edit']['link'];
            }

            return $post;
        }

        /**
         * Define the endpoint mask
         * 
         * @param array $rewrite
         * @return array $rewrite
         */
        private function setEndPointMask(array $rewrite)
        {
            $rewrite['ep_mask'] = "EP_PERMALINK";

            if (isset($rewrite['endpoint']))
            {
                if (is_string($rewrite['endpoint']) && in_array($rewrite['endpoint'], self::ENDPOINT_MASK))
                {
                    $rewrite['ep_mask'] = $rewrite['endpoint'];
                }
                unset($rewrite['endpoint']);
            }

            return $rewrite;
        }

        /**
         * Whether to exclude posts with this post type from front end search 
         * results. Default is the opposite value of $post['public'].
         * 
         * @param array $query
         * @param array $post
         * @return array $post
         */
        private function setExcludeFromSearch(array $query, array $post)
        {
            $post['exclude_from_search'] = !$post['public'];

            if (isset($query['exclude_from_search']) && is_bool($query['exclude_from_search']))
            {
                $post['exclude_from_search'] = $query['exclude_from_search'];
            }

            return $post;
        }

        /**
         * Whether the feed permastruct should be built for this post type
         * 
         * @param array $rewrite
         * @param array $post
         * @return array $rewrite
         */
        private function setFeeds(array $rewrite, array $post)
        {
            if (!isset($rewrite['feeds']) || !is_bool($rewrite['feeds']))
            {
                $rewrite['feeds'] = ($post['has_archive'] ? true : false);
            }

            return $rewrite;
        }

        /**
         * Whether there should be post type archives, or if a string, 
         * the archive slug to use. Will generate the proper rewrite rules if 
         * $rewrite is enabled.
         * 
         * @param array $post
         * @return array $post
         */
        private function setHasArchive(array $post)
        {
            if (!isset($post['has_archive']) || (!is_bool($post['has_archive']) && !is_string($post['has_archive'])))
            {
                $post['has_archive'] = $post['public'];
            }

            return $post;
        }

        /**
         * Whether the post type is hierarchical (e.g. page). 
         * 
         * @param array $post
         * @return array $post
         */
        private function setHierarchical(array $post)
        {
            if (!isset($post['hierarchical']) || !is_bool($post['hierarchical']))
            {
                $post['hierarchical'] = false;
            }

            return $post;
        }

        /**
         * Define the label of the post
         * 
         * @param array $post
         * @return array $post
         */
        private function setLabel(array $post)
        {
            // Init "label"
            $post['label'] = null;

            // Define the label
            if (isset($post['name'])) 
            {
                $post['label'] = $post['name'];
                unset($post['name']);
            }
            
            return $post;
        }

        /**
         * Define labels of the post
         * 
         * @param array $post
         * @return array $post
         */
        private function setLabels(array $post)
        {
            $post['labels'] = array();

            $post['labels'] = ['name' => $post['label']];

            if (isset($post['ui']['labels'])) 
            {
                $post['labels'] = array_merge(
                    $post['labels'], 
                    $post['ui']['labels']

                    // TODO : i18n
                    // $this->bs->i18n(self::LABELS_UI, $post['ui']['labels'])
                );

                unset($post['ui']['labels']);
            }

            return $post;
        }

        /**
         * The icon of the menu
         * 
         * @param array $post
         * @return array $post
         */
        private function setMenuIcon(array $post)
        {
            $post['menu_icon'] = 'none';

            if (isset($post['ui']['menus']['main']['icon']) && is_string($post['ui']['menus']['main']['icon']))
            {
                if (preg_match("/^@/", $post['ui']['menus']['main']['icon']))
                {
                    if (isset($post['context']) && is_object($post['context']))
                    {
                        $context = $post['context'];
                    }
                    else
                    {
                        $context = $this->kernel->getPlugin();
                    }

                    $file = preg_replace("/^@/", null, $post['ui']['menus']['main']['icon']);
                    $file_path = $context->getConfig('directory').Assets::DIRECTORY_IMAGES.$file;
                    $file_uri = $context->getConfig('uri').Assets::DIRECTORY_IMAGES.$file;
                    
                    if (file_exists($file_path))
                    {
                        $post['menu_icon'] = $file_uri;
                    }
                }
                unset($post['ui']['menus']['main']['icon']);
            }

            return $post;
        }

        /**
         * The position in the menu order the post type should appear
         * 
         * @param array $post
         * @return array $post
         */
        private function setMenuPosition(array $post)
        {
            $post['menu_position'] = null;

            if (isset($post['ui']['menus']['main']['position']))
            {
                if (is_int($post['ui']['menus']['main']['position']))
                {
                    $post['menu_position'] = $post['ui']['menus']['main']['position'];
                }
                unset($post['ui']['menus']['main']['position']);
            }

            return $post;
        }

        /**
         * Define if structure has a pagination
         * 
         * @param array $rewrite
         * @return array $rewrite
         */
        private function setPages(array $rewrite)
        {
            $rewrite['pages'] = false;

            if (isset($rewrite['paged']))
            {
                if (is_bool($rewrite['paged']))
                {
                    $rewrite['pages'] = $rewrite['paged'];
                }
                unset($rewrite['paged']);
            }

            return $rewrite;
        }

        /**
         * Define if the post is a Public post
         * 
         * @param array $post
         * @return array $post
         */
        private function setPublic(array $post)
        {
            if (!isset($post['public']) || !is_bool($post['public']))
            {
                $post['public'] = true;
            }

            return $post;
        }

        /**
         * Whether queries can be performed on the front end for the post type 
         * as part of parse_request().
         * 
         * @param array $query
         * @param array $post
         * @return array $post
         */
        private function setPubliclyQueryable(array $query, array $post)
        {
            $post['publicly_queryable'] = $post['public'];

            if (isset($query['public']) && is_bool($query['public']))
            {
                $post['publicly_queryable'] = $query['public'];
            }

            return $post;
        }

        /**
         * Define Query rules
         * 
         * @param array $post
         * @return array $post
         */
        private function setQuery(array $post)
        {
            // Default query rules
            if (!isset($post['query']) || (!is_bool($post['query']) && !is_array($post['query'])))
            {
                $post['exclude_from_search'] = !$post['public'];
                $post['publicly_queryable'] = $post['public'];
                $post['query_var'] = $post['type'];
                $post['delete_with_user'] = null;
            }

            if (isset($post['query']) && is_array($post['query']))
            {
                $query = $post['query'];

                // Define Query_var
                $post = $this->setQueryVar($query, $post);

                // Is Exclude from search
                $post = $this->setExcludeFromSearch($query, $post);
    
                // Is publicly Queryable
                $post = $this->setPubliclyQueryable($query, $post);

                // Delete with user
                $post = $this->setDeleteWithUser($query, $post);

            }

            return $post;
        }

        /**
         * Define query var
         * 
         * @param array $query
         * @param array $post
         * @return array $post
         */
        private function setQueryVar(array $query, array $post)
        {
            $post['query_var'] = $post['type'];

            if (isset($query['var']) && (is_bool($query['var']) || is_string($query['var'])))
            {
                $post['query_var'] = $query['var'];
            }

            return $post;
        }

        /**
         * Where to show the post type in the admin menu
         * 
         * @param array $post
         * @return array $post
         */
        private function setShowInMenu(array $post)
        {
            $post['show_in_menu'] = $post['show_ui'];

            if (isset($post['ui']['menus']['main']['display']))
            {
                if (is_bool($post['ui']['menus']['main']['display']))
                {
                    $post['show_in_menu'] = $post['ui']['menus']['main']['display'];
                }
                unset($post['ui']['menus']['main']['display']);
            }

            return $post;
        }

        /**
         * Define REST parameters
         * 
         * @param array $post
         * @return array $post
         */
        private function setREST(array $post)
        {
            // TODO: Include Rest Controller
            
            $post['show_in_rest'] = false;
            $post['rest_base'] = $post['type'];
            $post['rest_controller_class'] = false;

            if (isset($post['rest']))
            {
                if (isset($post['rest']['base']) && is_string($post['rest']['base']))
                {
                    $post['rest_base'] = $post['rest']['base'];
                }

                if (isset($post['rest']['controller']) && is_string($post['rest']['controller']))
                {
                    $post['rest_controller_class'] = $post['rest']['controller'];
                    $post['show_in_rest'] = true;
                }
                unset($post['rest']);
            }

            return $post;
        }

        /**
         * Define rewrite rules
         * 
         * @param array $post
         * @return array $post
         */
        private function setRewrite(array $post)
        {
            // Rewrite default value
            if (!isset($post['rewrite']) || (!is_bool($post['rewrite']) && !is_array($post['rewrite'])))
            {
                $post['rewrite'] = true;
            }

            // Is an array
            if (is_array($post['rewrite']))
            {
                $rewrite = $post['rewrite'];

                // Define the Slug
                $rewrite = $this->setSlug($rewrite, $post);

                // Define if prefixed (with_front)
                $rewrite = $this->setWithFront($rewrite);

                // Define if has feed
                $rewrite = $this->setFeeds($rewrite, $post);

                // Define if has pagination
                $rewrite = $this->setPages($rewrite);

                // Define EndPoint Mask
                $rewrite = $this->setEndPointMask($rewrite);

                $post['rewrite'] = $rewrite;
            }

            return $post;
        }

        /**
         * Create the shortcode of the Type
         */
        private function setShortcodes(array $post)
        {
            // Declare default $types as Array
            $types = array();

            // Retrieve types of the post
            if (isset($post['schema']))
            {
                $types = $post['schema'];
            }

            // Retrieve already declared Shortcodes of Core
            $shortcodes = $this->kernel->getCore()->getConfig('shortcodes');

            // Shortcode for _wp_nonce for posttype
            array_push($types,[
                'key' => '_nonce',
                'shortcode' => true
            ]);

            // // Transmit Post Config by a shortcode
            // $name = implode(':', [
            //     $this->bs->getNamespace(),
            //     '_posts',
            // ]);
            // add_shortcode($name, function(){
            //     return json_encode($this->getPosts());
            // });

            // Generate Shortcodes for each $types
            foreach ($types as $type) 
            {
                if (isset($type['shortcode']) && is_bool($type['shortcode']) && $type['shortcode'])
                {
                    // Retrieve the Plugin Namespace
                    $namespace = $this->kernel->getPlugin()->getConfig('namespace');

                    // Add the Type rules to the PHP Session
                    // $this->session->push($post['type'], $type);
                    $this->session->pushAssoc($post['type'], $type['key'], $type);

                    // The trigger
                    $trigger = implode(':', [
                        $namespace,
                        $post['type'],
                        $type['key'],
                    ]);

                    // The function
                    $function = '@shortcode-'.$trigger;
                    
                    // Add new shortcode
                    $shortcodes = array_merge($shortcodes, [$function => $trigger]);
                }
            }

            // Update the shortcode config of Core
            $this->kernel->getCore()->updateConfig('shortcodes', $shortcodes);
        }

        /**
         * Define Makes this post type available for selection in navigation menus
         * 
         * @param array $post
         * @return array $post
         */
        private function setShowInNavMenus(array $post)
        {
            // WP Default value
            // $post['show_in_nav_menus'] = $post['public'];

            // PPM Default Value
            $post['show_in_nav_menus'] = false;

            if (isset($post['ui']['pages']['menus']['display']))
            {
                if (is_bool($post['ui']['pages']['menus']['display'])) 
                {
                    $post['show_in_nav_menus'] = $post['ui']['pages']['menus']['display'];
                }
                unset($post['ui']['pages']['menus']['display']);
            }

            return $post;
        }

        /**
         * Makes this post type available via the admin bar
         * 
         * @param array $post
         * @return array $post
         */
        private function setShowInMenuBar(array $post)
        {
            $post['show_in_admin_bar'] = $post['show_in_menu'];

            if (isset($post['ui']['menus']['top']['display']))
            {
                if (is_bool($post['ui']['menus']['top']['display']))
                {
                    $post['show_in_admin_bar'] = $post['ui']['menus']['top']['display'];
                }
                unset($post['ui']['menus']['top']['display']);
            }

            if (!isset($post['show_in_admin_bar']) || !is_bool($post['show_in_admin_bar']))
            {
                $post['show_in_admin_bar'] = $post['show_in_menu'];
            }

            return $post;
        }

        /**
         * Define Whether to generate and allow a UI for managing this post 
         * type in the admin. 
         * 
         * @param array $post
         * @return array $post
         */
        private function setShowUI(array $post)
        {
            $post['show_ui'] = $post['public'];

            if (isset($post['ui']['show_ui']))
            {
                if (is_bool($post['ui']['show_ui']))
                {
                    $post['show_ui'] = $post['ui']['show_ui'];
                }
                unset($post['ui']['show_ui']);
            }

            return $post;
        }

        /**
         * Define the slug of custom post
         * 
         * @param array $rewrite
         * @param array $post
         * @return array $rewrite
         */
        private function setSlug(array $rewrite, array $post)
        {
            if (!isset($rewrite['slug']) || empty(trim($rewrite['slug'])))
            {
                $rewrite['slug'] = $post['type'];
            }

            switch ($rewrite['slug']) 
            {
                case '@type':
                    $rewrite['slug'] = $post['type'];
                    break;
                
                case '@name':
                    $rewrite['slug'] = Strings::slugify($post['label'], '_');
                    break;
                
                default:
                    $rewrite['slug'] = Strings::slugify($rewrite['slug'], '_');
                    break;
            }

            return $rewrite;
        }

        /**
         * Define Supports
         * 
         * @param array $supports
         * @param array $post
         * @return array $post
         */
        private function setSupports($supports, array $post)
        {
            // Default Supports
            $post['supports'] = self::DEFAULT_SUPPORTS;

            if (is_array($supports)) 
            {
                foreach ($supports as $support) 
                {
                    if (isset($support['key']))
                    {
                        // Default display
                        $display = in_array($support['key'], self::DEFAULT_SUPPORTS) ? true : false;

                        // Define $display
                        if (isset($support['display']) && is_bool($support['display'])) 
                        {
                            $display = $support['display'];
                        }

                        if (!$display && in_array($support['key'], self::DEFAULT_SUPPORTS)) 
                        {
                            $index = array_search($support['key'], $post['supports']);
                            unset($post['supports'][$index]);
                        }
                        else if ($display && !in_array($support['key'], $post['supports'])) 
                        {
                            array_push($post['supports'], $support['key']);
                        }
                    }
                }
            }

            // Add an empty string entry to $post['support] if this array is 
            // empty, to prevent the default WP supports.
            if (empty($post['supports']))
            {
                array_push($post['supports'], '');
                if (is_admin())
                {
                    Misc::injection("<style>#post-body-content {margin-bottom: 0px;}</style>", "head", "admin");
                }
            }

            return $post;
        }

        /**
         * Define if structure has a pagination
         * 
         * @param array $rewrite
         * @return array $rewrite
         */
        private function setWithFront(array $rewrite)
        {
            $rewrite['with_front'] = false;

            if (isset($rewrite['prefixed']))
            {
                if (is_bool($rewrite['prefixed']))
                {
                    $rewrite['with_front'] = $rewrite['prefixed'];
                }
                unset($rewrite['prefixed']);
            }

            return $rewrite;
        }


        /**
         * ----------------------------------------
         * Posts Actions / Hooks
         * ----------------------------------------
         */

        // -- Admin Columns

        /**
         * Define Columns of Admin Post Index
         */
        public function manage_posts_columns($columns, array $post)
        {
            $_columns = array();

            // Define Columns
            if (isset($post['ui']['pages']['index']['columns'])) 
            {
                $_columns = $post['ui']['pages']['index']['columns'];
            }

            foreach ($_columns as $column) 
            {
                if (
                    isset($column['key']) && is_string($column['key']) && 
                    isset($column['display']) && is_bool($column['display'])
                ) 
                {
                    switch ($column['key']) 
                    {
                        case 'checkbox':
                            $column['label'] = __('Select all');
                            $column['key'] = 'cb';
                            break;

                        case 'categories':
                            $column['label'] = __('Categories');
                            $column['key'] = 'taxonomy-c_'.$post['type'];
                            break;

                        case 'tags':
                            $column['label'] = __('Tags');
                            $column['key'] = 'taxonomy-t_'.$post['type'];
                            break;

                        case 'date':
                            $column['label'] = __('Date');
                            break;

                        case 'author':
                            $column['label'] = __('Author');
                            break;

                        case 'comments':
                            $column['label'] = __('Comments');
                            break;

                        case 'title':
                            $column['label'] = __('Title');
                            break;
                    }

                    if (true === $column['display'])
                    {
                        $columns[$column['key']] = $column['label'];
                    }
                    else
                    {
                        unset($columns[$column['key']]);
                    }
                }
            }

            return $columns;
        }

        /**
         * Define Data to display on columns
         */
        public function manage_posts_custom_column(string $column, int $id, array $post)
        {
            $columns = array();

            // Define Columns
            if (isset($post['ui']['pages']['index']['columns'])) 
            {
                $columns = $post['ui']['pages']['index']['columns'];
            }

            foreach ($columns as $col) 
            {
                if (isset($col['key']) && $col['key'] == $column && isset($col['data']))
                {

                    if (is_string($col['data']))
                    {
                        $output = $this->getPostData($col['data'], $id);
                    }

                    elseif (is_array($col['data'])) 
                    {
                        $glue = $col['data'][0];
                        unset($col['data'][0]);

                        $data = [];
                        foreach ($col['data'] as $field) 
                        {
                            $value = $this->getPostData($field, $id);
                            if (!empty($value))
                            {
                                array_push($data, $value);
                            }
                        }
                        $output = implode($glue, $data);
                    }

                    echo $output;
                }
            }
        }
        private function getPostData(string $field, int $id)
        {
            // TODO: get post attachement media

            switch ($field) 
            {
                case 'content':
                    return get_the_content($id);

                case 'date':
                    return get_the_date('', $id);

                case 'id':
                    return get_the_id($id);

                case 'guid':
                    return get_the_guid($id);

                case 'status':
                    return get_post_status($id);

                case 'slug':
                    return get_post_field('post_name', $id);

                case 'title':
                    return get_the_title($id);
                
                default:
                    return get_post_meta($id , $field, true);
            }
        }

        /**
         * Define if Columns are sortable
         */
        public function manage_sortable_columns($columns, array $post)
        {
            $_columns = array();

            if (isset($post['ui']['pages']['index']['columns']) && is_array($post['ui']['pages']['index']['columns']))
            {
                $_columns = $post['ui']['pages']['index']['columns'];
            }

            foreach ($_columns as $column) 
            {
                if (
                    isset($column['key']) && is_string($column['key']) &&
                    isset($column['sortable']) && is_bool($column['sortable'])
                )
                {
                    switch ($column['key']) {
                        case 'categories':
                            $key = 'taxonomy-c_'.$post['type'];
                            break;
                            
                        case 'tags':
                            $key = 'taxonomy-t_'.$post['type'];
                            break;
                        
                        default:
                            $key = $column['key'];
                            break;
                    } 

                    if ($column['sortable'])
                    {
                        $columns[$key] = $key;
                    }
                    else
                    {
                        if (isset($columns[$column['key']]))
                        {
                            unset($columns[$column['key']]);
                        }
                    }
                }
            }
            
            return $columns;
        }

        /**
         * Set actions for items row in Admin table
         */
        public function post_row_actions($actions, $post)
        {
            // Default 
            $edit = true;
            $inline = true;
            $trash = true;
            $view = true;

            // Rertieve Actions
            if (isset($post['ui']['pages']['index']['rows']['actions']))
            {
                if (false === $post['ui']['pages']['index']['rows']['actions'])
                {
                    $edit = false;
                    $inline = false;
                    $trash = false;
                    $view = false;
                }
                elseif (is_array($post['ui']['pages']['index']['rows']['actions']))
                {
                    if (isset($post['ui']['pages']['index']['rows']['actions']['edit']) && is_bool($post['ui']['pages']['index']['rows']['actions']['edit']))
                    {
                        $edit = $post['ui']['pages']['index']['rows']['actions']['edit'];
                    }
                    if (isset($post['ui']['pages']['index']['rows']['actions']['inline']) && is_bool($post['ui']['pages']['index']['rows']['actions']['inline']))
                    {
                        $inline = $post['ui']['pages']['index']['rows']['actions']['inline'];
                    }
                    if (isset($post['ui']['pages']['index']['rows']['actions']['trash']) && is_bool($post['ui']['pages']['index']['rows']['actions']['trash']))
                    {
                        $trash = $post['ui']['pages']['index']['rows']['actions']['trash'];
                    }
                    if (isset($post['ui']['pages']['index']['rows']['actions']['view']) && is_bool($post['ui']['pages']['index']['rows']['actions']['view']))
                    {
                        $view = $post['ui']['pages']['index']['rows']['actions']['view'];
                    }
                }
            }

            if (!$edit)   unset( $actions['edit'] );
            if (!$inline) unset( $actions['inline hide-if-no-js'] );
            if (!$trash)  unset( $actions['trash'] );
            if (!$view)   unset( $actions['view'] );

            return $actions;
        }

        
        // -- Misc Options

        public function screen_options_show_screen( array $post )
        {
            $screen_options_tab = true;

            switch (basename($_SERVER['SCRIPT_FILENAME']))
            {
                case 'edit.php':
                    if (isset($post['ui']['pages']['index']['options']['screen_options']) && is_bool($post['ui']['pages']['index']['options']['screen_options']))
                    {
                        $screen_options_tab = $post['ui']['pages']['index']['options']['screen_options'];
                    }
                    break;

                case 'post.php':
                case 'post-new.php':
                    if (isset($post['ui']['pages']['edit']['options']['screen_options']) && is_bool($post['ui']['pages']['edit']['options']['screen_options']))
                    {
                        $screen_options_tab = $post['ui']['pages']['edit']['options']['screen_options'];
                    }
                    break;
            }

            return $screen_options_tab;
        }
































































        // /**
        //  * Verify the validity of the Name of a custom post
        //  * 
        //  * @param array $post
        //  * @return bool
        //  */
        // private function isValidLabel(array $post)
        // {
        //     // Default $label
        //     $label = null;

        //     // Default Is Valid (true)
        //     $isValid = true;

        //     // Default error message
        //     $errorMessage = null;

        //     // Retrieve the label
        //     if (isset($post['name']) && is_string($post['name'])) {
        //         $label = $post['name'];
        //     }

        //     // Define error if $type is empty
        //     if (empty($label)) {
        //         $isValid = false;
        //         $errorMessage = "<strong>Invalid Post Name</strong> : The post name can't be empty.";
        //     }

        //     // Set error
        //     if (!$isValid) 
        //     {
        //         trigger_error($errorMessage, E_USER_WARNING);
        //     }

        //     return $isValid;
        // }


        /**
         * ----------------------------------------
         * Posts Actions / Hooks
         * ----------------------------------------
         */

        /**
         * 
         */
        public function pre_get_posts($query)
        {
            if (is_admin())
            {
                // // $query->set('posts_per_archive_page', '5');
                // $query->set('posts_per_page', '5');
                // // $query->set('paged', 10);

                // // $limit = 5;
                // // set_query_var('posts_per_archive_page', $limit);


                // echo "<pre style=\"padding-left: 180px;\">";
                // print_r($query->get('posts_per_page'));
                // echo "</pre>";
                // echo "<pre style=\"padding-left: 180px;\">";
                // var_dump($query->get('paged'));
                // echo "</pre>";
            }
        }

        // -- Post Submission

        /**
         * Retrieve Post response and Post validation
         */
        public function pre_post_update($_PID)
        {
            if (wp_is_post_revision($_PID) || wp_is_post_autosave($_PID) || $this->bs->request()->isActionTrash() || $this->bs->request()->isActionUntrash())
            {
                return;
            }

            $response = new Response( $this->getPosts());
            $this->responses = $response->responses();
            
            if ($this->responses->validate())
            {
                add_action('save_post', [$this, 'save_post']);
            }
            else 
            {
                $this->request->redirect( get_edit_post_link($_PID, 'redirect') );
                // header('Location: '.get_edit_post_link($_PID, 'redirect'));
                // exit;
            }
        }

        /**
         * Save / Update Post data
         */
        public function save_post(int $postid)
        {
            $poststypes = [];
            $responses  = [];
            $add        = [];
            $update     = [];
            $delete     = [];

            foreach ($this->getPosts() as $post) 
            {

                // Retrieve all Posts Type
                if (isset($post['type']) && isset($post['public']) && !$post['public'])
                {
                    array_push($poststypes, $post['type']);
                }

                // Post Title replacement
                if ($post['type'] == $this->bs->request()->getPostType())
                {
                    $metaboxes = new Metaboxes($this->bs, $post);
                    $supports = $metaboxes->getSupports();
                    $glue = " ";
                    $replacement = null;
        
                    foreach ($supports as $support) 
                    {
                        if ('title' == $support['key'] && !$support['display'] && isset($support['replace']))
                        {
                            if (isset($support['glue']))
                            {
                                $glue = $support['glue'];
                            }
        
                            $replacements = $support['replace'];
                            $replacements_val = [];
        
                            if (!is_array($replacements))
                            {
                                $replacements = [$replacements];
                            }
        
                            // Check if replacement field (schema) exists
                            foreach ($replacements as $replacement_key) 
                            {
                                foreach ($this->responses->getMetaTypes() as $item) 
                                {
                                    if ($replacement_key == $item['key'] && 'password' != $item['type'])
                                    {
                                        array_push($replacements_val, $item['value']);
                                    }
                                }
                            }
        
                            $replacement = trim(implode($glue, $replacements_val));                    
                        }
                    }
                    
                    if (null !== $replacement)
                    {
                        global $wpdb;
                        $wpdb->update( $wpdb->posts, [
                            'post_title' => $replacement
                        ],[
                            'ID' => $postid
                        ]);
                    }
                }
            }

            // -- retrieve responses

            foreach ($this->responses->getMetaTypes() as $type)
            {
                if ('collection' == $type['type'])
                {
                    if (isset($type['schema']))
                    {
                        $vpost_data = [];
                        
                        foreach ($type['schema'] as $type_key => $schema) 
                        {
                            if (is_array($schema['value']))
                            {
                                foreach ($schema['value'] as $value_key => $value) 
                                {
                                    if (!isset($vpost_data[$value_key]))
                                    {
                                        $vpost_data[$value_key] = array();
                                    }

                                    switch ($schema['key'])
                                    {
                                        case '_PARENT':
                                            unset($type['schema'][$type_key]);
                                            break;

                                        case '_VPOST':
                                            $vpost_data[$value_key]['posttype'] = $value;
                                            unset($type['schema'][$type_key]);
                                            break;

                                        case '_VPOST_ID':
                                            $vpost_data[$value_key]['postid'] = $value;                                        
                                            unset($type['schema'][$type_key]);
                                            break;
                                    }
                                }
                            }
                        }

                        foreach ($type['schema'] as $schema) 
                        {
                            // echo "<hr>";
                            // echo "<pre>";
                            // var_dump( $schema['value'] );
                            // echo "</pre>";
                            if (is_array($schema['value']))
                            {
                                foreach ($schema['value'] as $key => $value) 
                                {
                                    array_push($responses, array_merge($vpost_data[$key], [
                                        'key' => $schema['key'],
                                        'serial' => $key,
                                        'type' => $schema['type'],
                                        'value' => $value,
                                    ]));
                                }
                            }
                        }

                    }
                }

                else
                {
                    array_push($responses, [
                        'key' => $type['key'],
                        'type' => $type['type'],
                        'value' => $type['value'],
                        'posttype' => $this->bs->request()->getPostType(),
                        'postid' => $postid,
                    ]);
                }
            }

            // -- Build Arrays $add, $update, $delete


            // echo "<pre>";
            // print_r( $this->responses->getMetaTypes() );
            // echo "</pre>";

            // echo "<pre>";
            // print_r( $responses );
            // echo "</pre>";

            foreach ($responses as $key => $response) 
            {
                // -- Responses to add
                if (empty($response['postid']))
                {
                    if (!isset($add[$response['serial']]))
                    {
                        $add[$response['serial']] = array();
                    }
                    array_push($add[$response['serial']], $response);
                }

                // -- Responses to update
                else
                {
                    if (!isset($update[$response['postid']]))
                    {
                        $update[$response['postid']] = array();
                    }
                    array_push($update[$response['postid']], $response);
                }
            }

            // echo "<pre>";
            // print_r( $update );
            // echo "</pre>";

            // -- Responses to remove

            // Retrieve old responses
            $query = new \WP_Query([
                'wpse_include_parent' => true,
                'post_parent'         => $postid,
                'post_type'           => $poststypes
            ]);

            // Build the array $delete
            foreach ($query->posts as $post) 
            {
                array_push($delete, $post->ID);
            }

            // Sanitize the array $delete
            foreach ($update as $key => $value) 
            {
                if (in_array($key, $delete))
                {
                    unset($delete[ array_search($key, $delete) ]);
                }
            }

            // -- Proceed to Add / Update / Delete


            // Add
            foreach ($add as $post) 
            {
                $_posttype = isset($post[0]['posttype']) ? $post[0]['posttype'] : null;

                if (null != $_posttype)
                {
                    remove_action('save_post', [$this, 'save_post']);

                    $post_data = [
                        'post_author' => get_current_user_id(),
                        'post_status' => get_post_status($postid),
                        'post_type' => $_posttype,
                        'post_parent' => $postid
                    ];

                    $postID = wp_insert_post($post_data, false);

                    if ($postID)
                    {
                        foreach ($post as $meta)
                        {
                            update_post_meta($postID, $meta['key'], $meta['value']);
                        }
                    }

                }
            }

            // echo "<pre>";
            // print_r( $update );
            // echo "</pre>";

            // Update
            foreach ($update as $postID => $postmeta) 
            {
                foreach ($postmeta as $meta) 
                {
                    // echo "<pre>";
                    // print_r( [$meta['key'], $meta['value']] );
                    // echo "</pre>";
                    update_post_meta($postID, $meta['key'], $meta['value']);
                }
            }

            // Delete
            foreach ($delete as $postID) 
            {
                wp_delete_post($postID, true);
            }

            // exit;
        }


        /**
         * Clear Post Session
         */
        public function clear_post_session( $post = null )
        {
            if (null == $post)
            {
                do_action('clear_post_session');
                return;
            }
    
            $session = new Session($this->bs->getNamespace());
            $session->clear($post['type']);
    
            $notices = new Notices($this->bs->getNamespace());
            $notices->clear();
        }
    }
}