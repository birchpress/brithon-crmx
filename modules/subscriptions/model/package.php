<?php


birch_ns( 'brithoncrmx.subscriptions.model', function( $ns ) {

        global $brithoncrmx;

        $ns->init = function() use ( $ns ) {
            register_activation_hook( __FILE__, array( $ns, 'plugin_init' ) );
            add_action( 'init', array( $ns, 'wp_init' ) );
        };

        $ns->plugin_init = function() use ( $ns ) {
            global $birchpress;

        };

        $ns->wp_init = function() use ( $ns, $brithoncrmx ) {
            global $birchpress;

            if ( is_main_site() ) {

            }
        };

    } );
