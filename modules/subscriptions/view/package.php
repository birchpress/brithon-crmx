<?php

birch_ns( 'brithoncrmx.subscriptions.view.admin.subscriptions', function( $ns ) {

        global $brithoncrm;

        $ns->init = function() use ( $ns ) {
            add_action( 'init', array( $ns, 'wp_init' ) );
        };

        $ns->wp_init = function() use ( $ns, $brithoncrm ) {
            global $birchpress;

            $params = array(

            );

            if ( is_main_site() ) {
                $birchpress->view->register_3rd_scripts();
                $birchpress->view->register_core_scripts();

                wp_register_script( 'brithoncrmx_subscriptions_apps_admin_subscriptions',
                    $brithoncrm->plugin_url() . '/modules/subscriptions/assets/js/apps/admin/subscriptions/index.bundle.js',
                    array( 'birchpress', 'react-with-addons', 'immutable' ) );
                wp_localize_script( 'brithoncrmx_subscriptions_apps_admin_subscriptions', 'brithoncrmx_subscriptions_apps_admin_subscriptions', $params );

                wp_enqueue_script( 'brithoncrmx_subscriptions_apps_admin_subscriptions' );
            }
        };

    } );
