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
// use \Kernel\Request;

if (!class_exists('Components\Form\Types\Collection'))
{
    class Collection extends Types 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = ['id', 'name', 'class'];

        /**
         * The pattern of Serial Expression
         */
        const SERIAL = '{{number}}';

        /**
         * List of collection items (on load)
         */
        private $items = [];

        /**
         * Override tag pattern
         */
        public function tag()
        {
            $tag = '<div 
                class="ppm-collection" 
                data-ppm-collection="'.$this->getId().'" 
                data-role="collection" 
                data-init="'.$this->getInitLoop().'"
                >';

            // Collection container
            $tag.= $this->container();

            // Button Add item
            $tag.= $this->button();

            // Item prototype
            $tag.= $this->prototype();

            $tag.= '</div>';
            
            return $tag;
        }

        /**
         * Override defaults parameters
         */
        public function builder()
        {
            $this->setType('collection');
            $this->setSchema();
            $this->setInitLoop();
            $this->setItems();
        }

        /**
         * Items
         */
        private function setItems()
        {
            // Default items array
            $items = array();

            // -- Generate Items list from Responses on session

            // $session = $this->session->responses($this->getConfig('post_type'));

            // if (isset($session[$this->getConfig('key')]))
            // {
            //     $types = $session[$this->getConfig('key')];

            //     foreach ($types as $type => $values) 
            //     {
            //         if (is_array($values))
            //         {
            //             foreach ($values as $key => $value) 
            //             {
            //                 if (!isset($items[$key]))
            //                 {
            //                     $items[$key] = array();
            //                 }
                            
            //                 $items[$key] = array_merge($items[$key], [$type => $value]);
            //             }
            //         }
            //     }
            // }


            // -- Generate Items list from database
            
            if (empty($items))
            {
                $query = new \WP_Query([
                    'wpse_include_parent' => true,
                    'post_parent'         => get_the_ID(),
                    'post_type'           => $this->getDefinition('_VPOST'),
                    'posts_per_page'      => -1
                ]);

                if (isset($query->posts))
                {
                    foreach ($query->posts as $post) 
                    {
                        $metas = get_post_meta($post->ID);

                        $item               = array();
                        $item['_PARENT']    = get_the_ID();
                        $item['_VPOST']     = $this->getDefinition('_VPOST');
                        $item['_VPOST_ID']  = $post->ID;

                        foreach ($metas as $key => $value) 
                        {
                            $item[$key] = $value[0];
                        }

                        array_push($items, $item);
                    }
                }

                if (empty($items))
                {
                    array_push($items, $this->addItem());
                }
            }


            // -- Generate Items List from initial number of loop (config.php)

            if (empty($items) && 'post-new.php' == basename($_SERVER['SCRIPT_FILENAME']))
            {
                for ($i=0; $i< $this->getLoop(); $i++)
                {
                    array_push($items, $this->addItem());
                    // $item               = array();
                    // $item['_PARENT']    = get_the_ID();
                    // // $item['_PARENT']    = null;
                    // $item['_VPOST']     = $this->getConfig('_VPOST');
                    // $item['_VPOST_ID']  = null;

                    // array_push($items, $item);
                }
            }


            $this->items = $items;

            return $this;
        }
        private function getItems()
        {
            return $this->items;
        }

        // Add an empty itme
        private function addItem(Type $var = null)
        {
            $item               = array();
            $item['_PARENT']    = get_the_ID();
            // $item['_PARENT']    = null;
            $item['_VPOST']     = $this->getDefinition('_VPOST');
            $item['_VPOST_ID']  = null;

            return $item;
        }

        /**
         * Template of Collection container
         */
        private function container()
        {
            // Default items list
            $items = '';
            foreach ($this->getItems() as $key => $item) {
                $items.= $this->item( $item, $key );
            }

            $tag = '<div class="ppm-collection-container" data-role="container">';
            $tag.= '<div class="ppm-collection-alert hidden" data-role="alert">Empty Collection message</div>';
            $tag.= $items;
            $tag.= '</div>';
            
            return $tag;
        }

        /**
         * Template of Add Button
         */
        private function button()
        {
            $tag = '<div class="ppm-collection-control">';
            $tag.= '<button type="button" class="button button-secondary button-large" data-role="control" data-control="add">Add</button>';
            $tag.= '</div>';

            return $tag;
        }

        /**
         * Collection prototype
         */
        private function prototype()
        {
            $tag = '<script type="text/html" data-role="prototype">';
            $tag.= $this->item();
            $tag.= '</script>';

            return $tag;
        }

        /**
         * Template of Item 
         */
        public function item( array $item = [], $serial = null)
        {
            $tag = '<div id="ppm-collection-item-'.self::SERIAL.'" class="ppm-collection-item" data-role="item">';

            // Item header
            $tag.= $this->item_header();

            // Item Body
            $tag.= '<table class="form-table ppm-collection">';
            $tag.= '<tbody>';
            $tag.= $this->item_body($item, $serial);
            $tag.= '</tbody>';
            $tag.= '</table>';

            $tag.= '</div>';
            
            if (null !== $serial)
            {
                $tag = preg_replace("/".self::SERIAL."/", $serial, $tag);
            }

            return $tag;
        }

        /**
         * Template of Item Header
         */
        private function item_header()
        {
            $tag = '<div class="ppm-collection-item-header">';
            $tag.= '<button type="button" class="button button-link button-small dashicons-before dashicons-dismiss hidden" data-role="control" data-control="remove"></button>';
            $tag.= '<h4>'.$this->getLabel().'</h4>';
            $tag.= '</div>';
            
            return $tag;
        }

        /**
         * Template of Item Body
         */
        public function item_body(array $item = [], $id = '')
        {
            $tag = '';

            foreach ($this->getSchema() as $type) 
            {
                $type['_posttype'] = $this->getPosttype();
                $type['_namespace'] = $this->getNamespace();
                $type['_collection'] = $this->getId();

                $classname = Strings::ucfirst($type['type']);
                $classname = Types::BASE.$classname;

                $type = new $classname($type, 'collection');
                $key = $type->getDefinition('key');
                
                // Add the {{number}} variable to the Name
                $type->setName($this->getName().'['.$key.']['.self::SERIAL.']');

                switch ($key)
                {
                    case '_VPOST':
                        $type->setValue($this->getDefinition('_VPOST'));
                        break;
                    
                    case '_PARENT':
                        $type->setValue(get_the_ID());
                        break;

                    case '_VPOST_ID':
                    default:
                        if (empty($item))
                        {
                            $type->setValue(''); 
                        }
                        else if ( isset($item[$key]) )
                        {
                            $type->setValue( $item[$key] );
                        }
                }


                if ('hidden' == $type->getDefinition('type'))
                {
                    $tag.= $type->tagRender();
                }
                else
                {
                    $tag.= $type->render();
                }

                if ('wysiwyg' == $type->getDefinition('type'))
                {
                    // $type->setId($this->getName().'['.$key.'][{{number}}]');
    
                    // echo "<pre>";
                    // var_dump( $type->getName('name') );
                    // echo "</pre>";

                    // $tag = str_replace(
                    //     "wp-".$type['post_type']."†".$type['key']."†-{{number}}-", 
                    //     "wp-".$type['post_type']."†".$type['key']."†-__number__-",
                    //     $tag
                    // );
                }

            }
            
            return $tag;
        }

        /**
         * Temple of the collection container
         */
        public function render()
        {
            $output = '';

            $attrColspan = ' colspan="2"';
            
            if (null != $this->tagHelper())
            {
                $output.= '<tr>';
                $output.= '<td'.$attrColspan.' class="ppm-collection-row ppm-collection-row-header">';
                $output.= $this->tagHelper();
                $output.= '</td>';
                $output.= '</tr>';
            }
            
            $output.= '<tr>';
            $output.= '<td'.$attrColspan.' class="ppm-collection-row">';
            $output.= $this->tagRender();
            $output.= '</td>';
            $output.= '</tr>';

            return $output;
        }
    }
}