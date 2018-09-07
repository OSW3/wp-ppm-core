<?php

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

// use \Components\Strings;
// use \Components\FileSystem as FS;

use \Components\Utils\Misc;

if (!class_exists('Register\Taxonomy'))
{
	abstract class Taxonomy
	{
        /**
         * Labels Index
         */
        const LABELS = ['name', 'singular_name', 'search_items', 'popular_items', 'all_items', 'parent_item', 'parent_item_colon',  'edit_item', 'view_item', 'update_item', 'add_new_item',  'new_item_name', 'separate_items_with_commas',  'add_or_remove_items', 'choose_from_most_used', 'not_found',  'no_terms', 'items_list_navigation', 'items_list', 'most_used',  'back_to_items'];

        /**
         * Taxonomy definition
         * 
         * this is the definition for Tags or Categories
         */
        private $definition;

        /**
         * The post Admin Columns UI
         * 
         * @param bool
         */
        private $postColumns;

        /**
         * The post Is Public
         * 
         * @param bool
         */
        private $postIsPublic;

        /**
         * The post Type
         * 
         * @param string
         */
        private $postType;

        /**
         * Taxonomy Type
         */
        private $type;

        /**
         * Constructor 
         * 
         * @param array $post
         */
        public function __construct(array $post)
        {
            // Define the type of taxonomy (categories or tags)
            $this->setType();

            // Define the Post Settings
            $this->setPostType($post);
            $this->setPostAdminColumns($post);
            $this->setPostIsPublic($post);

            // Define definition of Tags or Categories
            $this->setDefinition($post);

            // Add Taxonomies to register
            $this->flush();
        }

        private function flush()
        {
            if (!empty($this->definition))
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

                // Associated Object
                $taxonomy = $this->setAssociatedObjects($taxonomy);
    
                // Add taxonomy to the WP register
                register_taxonomy($taxonomy['key'], $taxonomy['objects'], $taxonomy);
            }
        }

        /**
         * 
         */
        private function setAssociatedObjects(array $taxonomy)
        {
            $taxonomy['objects'] = array(
                $this->getPostType()
            );

            // Retrieve Taxonomy associated objects
            $objects = $this->getDefinition('objects');

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
         * Definition
         */
        private function setDefinition(array $post)
        {
            // Default definition
            $definition = array();

            // retrieve taxonomy type
            $taxonomyType = $this->getType();

            if (isset($post[$taxonomyType]))
            {
                $definition = $post[$taxonomyType];
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

            return null;
            // return $this->definition;
        }

        /**
         * Description
         */
        private function setDescription(array $taxonomy)
        {
            $taxonomy['description'] = $this->getDefinition('description');
            
            return $taxonomy;
        }

        /**
         * Hierarchical
         */
        private function setHierarchical(array $taxonomy)
        {
            $taxonomy['hierarchical'] = static::SETTINGS['hierarchical'];

            return $taxonomy;
        }

        /**
         * Set Key
         * 
         * @param array $taxonomy
         * @return array $taxonomy
         */
        private function setKey(array $taxonomy)
        {
            $key = static::SETTINGS['prefix'];
            $key.= $this->getPostType();

            if (strlen($key) > 32) 
            {
                $key = substr($key, 0, 32);
            }
            
            $taxonomy['key'] = $key;

            return $taxonomy;
        }

        /**
         * Labels
         */
        private function setLabels(array $taxonomy)
        {
            // Define labels
            $labels = $this->getDefinition('labels');

            if (!is_array($labels)) 
            {
                $labels = array();
            }
            
            // Add "name" to labels
            $labels = array_merge($labels, [
                'name' => $this->getName()
            ]);
            
            // TODO: i18n of labels

            $taxonomy['labels'] = $labels;

            return $taxonomy;
        }

        /**
         * taxonomy Name
         */
        public function getName()
        {
            $name = $this->getDefinition('name');

            if (empty($name)) 
            {
                $name = ucfirst(strtolower($this->getType()));
            }

            return $name;
        }

        /**
         * Post Admin Columns
         */
        private function setPostAdminColumns(array $post)
        {
            $columns = array();

            if (isset( $post['ui']['pages']['index']['columns'] ))
            {
                $columns = $post['ui']['pages']['index']['columns'];
            }

            $this->postColumns = $columns;

            return $this;
        }
        public function getPostAdminColumns()
        {
            return $this->postColumns;
        }

        /**
         * Post Type
         */
        private function setPostType(array $post)
        {
            $type = null;

            if (isset($post['type']))
            {
                $type = $post['type'];
            }

            $this->postType = $type;

            return $this;
        }
        private function getPostType()
        {
            return $this->postType;
        }

        /**
         * Post Public
         */
        private function setPostIsPublic(array $post)
        {
            $public = null;

            if (isset($post['public']))
            {
                $public = $post['public'];
            }

            $this->postIsPublic = $public;

            return $this;
        }
        private function getPostIsPublic()
        {
            return $this->postIsPublic;
        }

        /**
         * Is Public
         */
        private function setPublic(array $taxonomy)
        {
            $taxonomy['public'] = $this->getDefinition('public');

            if (null === $taxonomy['public'])
            {
                $taxonomy['public'] = $this->getPostIsPublic('public');
            }
            
            return $taxonomy;
        }

        /**
         * Query
         */
        private function setPubliclyQueryable(array $taxonomy)
        {
            $taxonomy['publicly_queryable'] = $this->getDefinition('publicly_queryable');

            if (null === $taxonomy['publicly_queryable'])
            {
                $taxonomy['publicly_queryable'] = $taxonomy['public'];
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

            $columns = $this->getPostAdminColumns();

            foreach ($columns as $column) 
            {
                if (isset($column['key']) && $column['key'] == $this->getType() &&  $column['display'])
                {
                    $taxonomy['show_admin_column'] = true;
                }
            }

            return $taxonomy;
        }

        /**
         * 
         */
        private function setShowUI(array $taxonomy)
        {
            $ui = $this->getDefinition('show_ui');

            $taxonomy['show_ui'] = $taxonomy['public'];

            if (isset($ui) && is_bool($ui))
            {
                $taxonomy['show_ui'] = $ui;
            }

            return $taxonomy;
        }

        /**
         * Type
         */
        private function setType()
        {
            $type = Misc::get_called_class_name(get_called_class());

            $this->type = $type;

            return $this;
        }
        private function getType()
        {
            return $this->type;
        }
    }
}



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