<?php

birch_ns( 'brithoncrmx.subscriptions.view.admin.subscriptions', function( $ns ) {

        global $brithoncrmx;

        $ns->init = function() use ( $ns ) {
            add_action( 'init', array( $ns, 'wp_init' ) );
        };

        $ns->wp_init = function() use ( $ns, $brithoncrmx ) {
            global $birchpress;

        };

    } );
