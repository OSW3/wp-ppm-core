<?php

namespace Kernel;

// Make sure we don't expose any info if called directly
if (!defined('WPINC'))
{
    echo "Hi there!<br>Do you want to plug me ?<br>";
	echo "If you looking for more about me, you can read at http://osw3.net/wordpress/plugins/please-plug-me/";
	exit;
}

use \Kernel\Kernel;
use \Register\Options;

if (!class_exists('Kernel\State'))
{
	class State extends Kernel
	{
		/**
		 * Plugin activation
		 */
		public function activate()
		{
			// Add plugin options 
			// TODO: (if not already added)
			Options::add([ $this->getNamespace() => $this->getOptions() ]);

			// TODO: Create Specific Database (if not already created)

			// TODO: Initialize Text domain

			set_transient( $this->getNamespace(), 1);

            // print_r("\n");
			// echo "activate"; exit;
		}

		/**
		 * Plugin deactivation
		 */
		public function deactivate()
		{
			// Delete plugin options 
			// TODO: (if not preserved)
			Options::delete( $this->getNamespace() );

			// TODO: Delete Specific Database (if not preserved)

			// TODO: Delete Text domain (if not preserved)

			// echo "Stop"; exit;
		}
	}
}
