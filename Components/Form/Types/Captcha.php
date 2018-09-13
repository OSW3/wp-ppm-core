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

if (!class_exists('Components\Form\Types\Captcha'))
{
    class Captcha extends Types 
    {
        /**
         * Define attributes of the tag
         */
        const ATTRIBUTES = [];

        /**
         * Define Captcha Key
         */
        private $captchaKey;

        /**
         * Define Captcha Secret
         */
        private $captchaSecret;

        /**
         * Define Captcha type
         */
        private $captchaType;

        /**
         * Override tag pattern
         */
        public function tag()
        {
            switch ($this->getCaptchaType())
            {
                case 'recaptcha':
                    return $this->google_reCaptcha();
            }
        }

        /**
         * Field Builder
         */
        public function builder()
        {
            $this->setCaptchaType();
            $this->setCaptchaKey();
            $this->setCaptchaSecret();
        }


        /**
         * ----------------------------------------
         * Options and Attribute Getters / Setters
         * ----------------------------------------
         */

        /**
         * ReCaptche Type
         */
        private function setCaptchaType()
        {
            $this->captchaType = $this->getRule('type');

            return $this;
        }
        private function getCaptchaType()
        {
            return $this->captchaType;
        }

        /**
         * ReCaptche Key
         */
        private function setCaptchaKey()
        {
            $this->captchaKey = $this->getRule('key');

            return $this;
        }
        private function getCaptchaKey()
        {
            return $this->captchaKey;
        }

        /**
         * ReCaptche Secret
         */
        private function setCaptchaSecret()
        {
            $this->captchaSecret = $this->getRule('secret');

            return $this;
        }
        private function getCaptchaSecret()
        {
            return $this->captchaSecret;
        }


        /**
         * ----------------------------------------
         * Google ReCaptcha
         * ----------------------------------------
         */

        /**
         * tag for Google reCaptcha
         */
        public function google_reCaptcha()
        {
            $api = 'https://www.google.com/recaptcha/api.js';

            // Google Script Injection
            if (is_admin())
            {
                add_action('admin_head', function() use ($api) { echo '<script src="'.$api.'"></script>'; });
                do_action('admin_head');
            }
            else
            {
                wp_enqueue_script('g-recaptcha', $api);
            }
            
            // Set tag
            return '<div class="g-recaptcha" data-sitekey="'.$this->getCaptchaKey().'"></div>';
        }
    }
}
