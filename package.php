<?php

birch_ns( 'brithoncrmx', function( $ns ) {

		$_ns_data = new stdClass();

		$ns->set_product_version = function( $product_version ) use ( $ns, $_ns_data ) {
			$_ns_data->product_version = $product_version;
		};

		$ns->get_product_version = function() use ( $ns, $_ns_data ) {
			return $_ns_data->product_version;
		};

		$ns->set_product_name = function( $product_name ) use ( $ns, $_ns_data ) {
			$_ns_data->product_name = $product_name;
		};

		$ns->get_product_name = function() use ( $ns, $_ns_data ) {
			return $_ns_data->product_name;
		};

		$ns->set_product_code = function( $product_code ) use ( $ns, $_ns_data ) {
			$_ns_data->product_code = $product_code;
		};

		$ns->get_product_code = function() use ( $ns, $_ns_data ) {
			return $_ns_data->product_code;
		};

		$ns->set_plugin_file_path = function ( $plugin_file_path )
		use( $ns, $_ns_data ) {

			$_ns_data->plugin_file_path = $plugin_file_path;
		};

		$ns->plugin_url = function() use ( $ns, $_ns_data ) {
			return plugins_url() . '/' . basename( $_ns_data->plugin_file_path, '.php' );
		};

		$ns->plugin_file_path = function() use ( $ns, $_ns_data ) {
			return $_ns_data->plugin_file_path;
		};

		$ns->plugin_dir_path = function () use ( $ns, $_ns_data ) {
			return plugin_dir_path( $_ns_data->plugin_file_path );
		};

		$ns->load_modules = function() use ( $ns, $_ns_data ) {
			global $birchpress;

			$modules_dir = $ns->plugin_dir_path() . 'modules';
			$_ns_data->module_names = $birchpress->load_modules( $modules_dir );
		};

		$ns->upgrade_module = function( $module_a ) {};

		$ns->upgrade = function() use ( $ns, $_ns_data ) {
			foreach ( $_ns_data->module_names as $module_name ) {
				$ns->upgrade_module( array(
						'module' => $module_name
					) );
			}
		};

		$ns->init_packages = function() use ( $ns ) {
			global $birchpress;

			$birchpress->init_package( $ns );
		};

		$ns->run = function() use( $ns ) {
			global $birchpress;

			$ns->load_modules();
			$ns->init_packages();
			$ns->upgrade();
		};

	} );
