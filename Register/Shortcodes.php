<?php

namespace Register;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Components\Form\Types;
use \Components\Utils\Strings;

if (!class_exists('Register\Shortcodes'))
{
	class Shortcodes extends \Register\Actions
	{
        /**
         * The Function file Header
         */
        const HEADERS = [];

        /**
         * Execute a shortcode
         * 
         * This is the Shortcode Callback function
         */
        public function exec($attrs, $content = "", $tag)
        {
            // decompose the trigger name
            list($namespace, $posttype, $key) = explode(":", $tag);

            // Make sure $attrs is an array
            if (!is_array($attrs))
            {
                $attrs = array();
            }

            // WP Nonce
            if ('_nonce' === $key)
            {
                wp_nonce_field($posttype, $posttype.'[nonce]');
                echo '<input type="hidden" name="post_type" value="'.$posttype.'">';
            }

            // Custom post fields
            else
            {
                // Retrieve the Type Setiings
                $type = $this->getSession()->readShortcode( [$posttype, $key] );

                // Check is a valid type
                if (isset($type['key']) && $type['key'] === $key)
                {
                    // Rebuild $attrs 
                    foreach ($attrs as $key => $value) 
                    {
                        switch ($key) 
                        {
                            case 'default':
                            case 'label':
                            case 'helper':
                            case 'preview':
                            case 'expanded':
                                $type[$key] = $value;
                                break;
        
                            case 'id':
                            case 'required':
                            case 'readonly':
                            case 'disabled':
                            case 'class':
                            case 'placeholder':
                            case 'maxlength':
                            case 'step':
                            case 'max':
                            case 'min':
                            case 'width':
                            case 'cols':
                            case 'rows':
                            case 'multiple':
                                $type['attr'][$key] = $value;
                                break;
        
                            case 'pattern':
                            case 'regex':
                                $type['rules'][$key] = $value;
                                break;
                            
                            default:
                                $value = parse_url($value);
        
                                if (isset($value['query']))
                                {
                                    parse_str($value['query'], $output);
        
                                    if (isset($type['key']))
                                    {
                                        $type[$key] = array_merge($type[$key], $output);
                                    }
                                    else 
                                    {
                                        $type[$key] = $output;
                                    }
                                }
        
                                elseif (isset($value['path']))
                                {
                                    $type[$key] = $value['path'];
                                }
                                break;
                        }

                    }

                    if (in_array($type['type'], Types::ALLOWED))
                    {
                        $type['_posttype'] = $posttype;
                        $type['_namespace'] = $namespace;
            
                        $classname = Strings::ucfirst($type['type']);
                        $classname = Types::BASE.$classname;
            
                        $type = new $classname($type);
                        echo $type->render();
                    }
                }
            }
        }
        
    }
}