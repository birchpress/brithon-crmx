<?php

/*
  Plugin Name: <%= productName %>
  Plugin URI: http://www.brithon.com
  Description: CRM for apps.brithon.com
  Version: <%= productVersion %>
  Author: Brithon Inc.
  Author URI: http://www.brithon.com
  License: GPLv2
 */

if ( defined( 'ABSPATH' ) && ! function_exists( 'brithoncrmx_main' ) ) {

    function brithoncrmx_main() {


        brithoncrmx_load( array(
                'plugin_file_path' => __FILE__,
                'product_version' => '<%= productVersion %>',
                'product_name' => '<%= productName %>',
                'product_code' => '<%= productCode %>',
                'global_name' => 'brithoncrmx'
            ) );
    }

    brithoncrmx_main();
}