<?php

birch_ns( 'brithoncrmx.subscriptions', function( $ns ) {

        global $brithoncrmx;

        $ns->init = function() use ( $ns ) {

        };

        $ns->wp_init = function() use ( $ns, $brithoncrmx ) {
            global $birchpress;

        };
} );
