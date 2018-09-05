<?php

// TODO : Sortable Taxonomy column
 
// 'show_in_menu'
// (bool) Whether to show the taxonomy in the admin menu. If true, the taxonomy is shown as a submenu of the object type menu. If false, no menu is shown. $show_ui must be true. If not set, default is inherited from $show_ui (default true).
// 'show_in_nav_menus'
// (bool) Makes this taxonomy available for selection in navigation menus. If not set, the default is inherited from $public (default true).

// 'show_in_rest'
// (bool) Whether to include the taxonomy in the REST API.
// 'rest_base'
// (string) To change the base url of REST API route. Default is $taxonomy.
// 'rest_controller_class'
// (string) REST API Controller class name. Default is 'WP_REST_Terms_Controller'.

// 'show_tagcloud'
// (bool) Whether to list the taxonomy in the Tag Cloud Widget controls. If not set, the default is inherited from $show_ui (default true).
// 'show_in_quick_edit'
// (bool) Whether to show the taxonomy in the quick/bulk edit panel. It not set, the default is inherited from $show_ui (default true).
// 'meta_box_cb'
// (bool|callable) Provide a callback function for the meta box display. If not set, post_categories_meta_box() is used for hierarchical taxonomies, and post_tags_meta_box() is used for non-hierarchical. If false, no meta box is shown.
// 'capabilities'
// (array) Array of capabilities for this taxonomy.
// 'manage_terms'
// (string) Default 'manage_categories'.
// 'edit_terms'
// (string) Default 'manage_categories'.
// 'delete_terms'
// (string) Default 'manage_categories'.
// 'assign_terms'
// (string) Default 'edit_posts'.
// 'rewrite'
// (bool|array) Triggers the handling of rewrites for this taxonomy. Default true, using $taxonomy as slug. To prevent rewrite, set to false. To specify rewrite rules, an array can be passed with any of these keys:
// 'slug'
// (string) Customize the permastruct slug. Default $taxonomy key.
// 'with_front'
// (bool) Should the permastruct be prepended with WP_Rewrite::$front. Default true.
// 'hierarchical'
// (bool) Either hierarchical rewrite tag or not. Default false.
// 'ep_mask'
// (int) Assign an endpoint mask. Default EP_NONE.
// 'query_var'
// (string) Sets the query var key for this taxonomy. Default $taxonomy key. If false, a taxonomy cannot be loaded at ?{query_var}={term_slug}. If a string, the query ?{query_var}={term_slug} will be valid.
// 'update_count_callback'
// (callable) Works much like a hook, in that it will be called when the count is updated. Default _update_post_term_count() for taxonomies attached to post types, which confirms that the objects are published before counting them. Default _update_generic_term_count() for taxonomies attached to other object types, such as users.
// '_builtin'
// (bool) This taxonomy is a "built-in" taxonomy. INTERNAL USE ONLY! Default false.



// function custom_taxonomy() {

// 	$labels = array(
// 		'name'                       => _x( 'Taxonomies', 'Taxonomy General Name', 'text_domain' ),
// 		'singular_name'              => _x( 'Taxonomy', 'Taxonomy Singular Name', 'text_domain' ),
// 		'menu_name'                  => __( 'Taxonomy', 'text_domain' ),
// 		'all_items'                  => __( 'All Items', 'text_domain' ),
// 		'parent_item'                => __( 'Parent Item', 'text_domain' ),
// 		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
// 		'new_item_name'              => __( 'New Item Name', 'text_domain' ),
// 		'add_new_item'               => __( 'Add New Item', 'text_domain' ),
// 		'edit_item'                  => __( 'Edit Item', 'text_domain' ),
// 		'update_item'                => __( 'Update Item', 'text_domain' ),
// 		'view_item'                  => __( 'View Item', 'text_domain' ),
// 		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
// 		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
// 		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
// 		'popular_items'              => __( 'Popular Items', 'text_domain' ),
// 		'search_items'               => __( 'Search Items', 'text_domain' ),
// 		'not_found'                  => __( 'Not Found', 'text_domain' ),
// 		'no_terms'                   => __( 'No items', 'text_domain' ),
// 		'items_list'                 => __( 'Items list', 'text_domain' ),
// 		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
// 	);
// 	$args = array(
// 		'labels'                     => $labels,
// 		'hierarchical'               => false,
// 		'public'                     => true,
// 		'show_ui'                    => true,
// 		'show_admin_column'          => true,
// 		'show_in_nav_menus'          => true,
// 		'show_tagcloud'              => true,
// 	);
// 	register_taxonomy( 'taxonomy', array( 'post' ), $args );

// }
// add_action( 'init', 'custom_taxonomy', 0 );

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Components\Strings;
use \Components\FileSystem as FS;

if (!class_exists('Register\Taxonomy'))
{
	class Taxonomy
	{
        /**
         * Definition for Categorie
         */
        const CATEGORIES = [
            'hierarchical' => true,
            'prefix' => 'c_'
        ];

        /**
         * Definition for Tags
         */
        const TAGS = [
            'hierarchical' => false,
            'prefix' => 't_'
        ];

        /**
         * Labels
         */
        const LABELS = ['name', 'singular_name', 'search_items', 
            'popular_items', 'all_items', 'parent_item', 'parent_item_colon', 
            'edit_item', 'view_item', 'update_item', 'add_new_item', 
            'new_item_name', 'separate_items_with_commas', 
            'add_or_remove_items', 'choose_from_most_used', 'not_found', 
            'no_terms', 'items_list_navigation', 'items_list', 'most_used', 
            'back_to_items'];

        
        /**
         * The instance of the bootstrap class
         * 
         * @param object instance
         */
        protected $bs;

        /**
         * The post data
         * 
         * @param array
         */
        private $post;

        /**
         * Taxonomy config defined in the config.php
         */
        private $taxonomy;

        /**
         * Taxonomy Type
         */
        private $type;

        /**
         * @param string $type of taxonomy
         * @param array $post
         */
        public function __construct($bs, string $type, array $post)
        {
            // Retrieve the bootstrap class instance
            $this->bs = $bs;

            // Define the type of taxonomy (categories or tags)
            $this->setType($type);

            // Retrieve the array Post data
            $this->setPost($post);

            // Retrieve the array of $post['categories'] or $post['tags']
            $this->setTaxonomy();

            // Define the Taxonomy
            $this->WP_Taxonomy();
        }

        /**
         * Format and register a Taxonomy
         */
        private function WP_Taxonomy()
        {
            if ($this->taxonomy) 
            {
                $taxonomy = array();
    
                // Taxonomy Key
                $taxonomy = $this->setKey($taxonomy);
    
                // Taxonomy Labels
                $taxonomy = $this->setLabels($taxonomy);
    
                // Taxonomy Description
                $taxonomy = $this->setDescription($taxonomy);
    
                // Taxonomy Public
                $taxonomy = $this->setPublic($taxonomy);
                
                // Is publicly Queryable
                $taxonomy = $this->setPubliclyQueryable($taxonomy);
                
                // Is Hierarchical
                $taxonomy = $this->setHierarchical($taxonomy);
    
                // Show UI
                $taxonomy = $this->setShowUI($taxonomy);
    
                // Show in admin column
                $taxonomy = $this->setShowInAdminColumn($taxonomy);

                // $taxonomy => $this->setXxxxx($taxonomy);
                
                // Associated Object
                $taxonomy = $this->setAssociatedObjects($taxonomy);
    
                // Add taxonomy to the WP register
                register_taxonomy($taxonomy['key'], $taxonomy['objects'], $taxonomy);
            }
        }


        /**
         * Set Taxonomy Type
         * 
         * @param string $type of the taxonomy
         * @return object $this
         */
        private function setType(string $type)
        {
            $this->type = $type;

            return $this;
        }

        /**
         * Get Taxonomy Type
         * 
         * @return string
         */
        private function getType()
        {
            return $this->type;
        }

        /**
         * Retrieve Post data
         * 
         * @param array $post
         * @return object $this
         */
        private function setPost(array $post)
        {
            $this->post = $post;

            return $this;
        }

        /**
         * Get Post data
         * 
         * @param string $key of $post data
         * @return mixed
         */
        private function getPost(string $key = '')
        {
            if (isset($this->post[$key])) 
            {
                return $this->post[$key];
            }

            return $this->post;
        }

        /**
         * Set Taxonomy Config
         * 
         * @return object $this
         */
        private function setTaxonomy()
        {
            $this->taxonomy = null;

            // retrieve taxonomy type
            $type = $this->getType();

            if (isset($this->post[$type]))
            {
                $this->taxonomy = $this->post[$type];
            }

            return $this;
        }

        /**
         * Get Taxonomy Config
         */
        private function getTaxonomy(string $key = '')
        {
            if (is_array($this->taxonomy) && isset($this->taxonomy[$key])) 
            {
                return $this->taxonomy[$key];
            }

            return null;
        }

        /**
         * Get Key Prefix
         */
        private function getPrefix()
        {
            switch ($this->getType()) 
            {
                case 'categories':
                    return self::CATEGORIES['prefix'];

                case 'tags':
                    return self::TAGS['prefix'];
            }
        }

        /**
         * Set Key
         * 
         * @param array $taxonomy
         * @return array $taxonomy
         */
        private function setKey(array $taxonomy)
        {
            $taxonomy['key'] = $this->getPrefix();
            $taxonomy['key'].= $this->getPost('type');

            if (strlen($taxonomy['key']) > 32) 
            {
                $taxonomy['key'] = substr($taxonomy['key'], 0, 32);
            }
            
            return $taxonomy;
        }

        /**
         * Set Labels
         */
        private function setLabels(array $taxonomy)
        {
            $ui = $this->getTaxonomy('ui');

            // Define Taxonomy labels
            $taxonomy['labels'] = array();

            // Define the name
            $name = $this->getTaxonomy('name');

            if (empty($name)) 
            {
                $name = ucfirst(strtolower($this->getType()));
            }

            $taxonomy['labels'] = array_merge( $taxonomy['labels'], ['name' => $name] );

            // define other labels
            $labels = $ui['labels'];

            if (!is_array($labels)) 
            {
                $labels = array();
            }

            $taxonomy['labels'] = array_merge( $taxonomy['labels'], $labels );

            // internationalization
            $taxonomy['labels'] = $this->bs->i18n(self::LABELS, $taxonomy['labels']);

            return $taxonomy;
        }

        /**
         * 
         */
        private function setDescription(array $taxonomy)
        {
            $taxonomy['description'] = $this->getTaxonomy('description');
            
            return $taxonomy;
        }

        /**
         * Is Public
         */
        private function setPublic(array $taxonomy)
        {
            $taxonomy['public'] = $this->getTaxonomy('public');

            if (null === $taxonomy['public'])
            {
                $taxonomy['public'] = $this->getPost('public');
            }
            
            return $taxonomy;
        }

        /**
         * 
         */
        private function setPubliclyQueryable(array $taxonomy)
        {
            $taxonomy['publicly_queryable'] = $this->getTaxonomy('publicly_queryable');

            if (null === $taxonomy['publicly_queryable'])
            {
                $taxonomy['publicly_queryable'] = $taxonomy['public'];
            }
            
            return $taxonomy;
        }

        /**
         * 
         */
        private function setHierarchical(array $taxonomy)
        {
            switch ($this->getType()) 
            {
                case 'categories':
                    $taxonomy['hierarchical'] = self::CATEGORIES['hierarchical'];
                    break;

                case 'tags':
                    $taxonomy['hierarchical'] = self::TAGS['hierarchical'];
                    break;
            }

            return $taxonomy;
        }

        /**
         * 
         */
        private function setAssociatedObjects(array $taxonomy)
        {
            $taxonomy['objects'] = array(
                $this->getPost('type')
            );

            // Retrieve Taxonomy associated objects
            $objects = $this->getTaxonomy('objects');

            if (null != $objects)
            {
                if (!is_array($objects)) 
                {
                    $objects = [$objects];
                }

                $taxonomy['objects'] = array_merge($taxonomy['objects'], $objects);
            }

            
            return $taxonomy;
        }

        /**
         * 
         */
        private function setShowUI(array $taxonomy)
        {
            $ui = $this->getTaxonomy('ui');
            $taxonomy['show_ui'] = $taxonomy['public'];

            if (isset($ui['show_ui']) && is_bool($ui['show_ui']))
            {
                $taxonomy['show_ui'] = $ui['show_ui'];
            }

            return $taxonomy;
        }

        /**
         * 
         */
        private function setShowInAdminColumn(array $taxonomy)
        {
            // Define default value
            $taxonomy['show_admin_column'] = false;

            // Retrieve the Post UI settings
            $postUI = $this->getPost('ui');
            
            if (isset($postUI['pages']['index']['columns'])) 
            {
                $columns = $postUI['pages']['index']['columns'];

                foreach ($columns as $column) 
                {
                    if (isset($column['key']) && $column['key'] == $this->getType() &&  $column['display'])
                    {
                        $taxonomy['show_admin_column'] = true;
                    }
                }
            }
            return $taxonomy;
        }
    }
}