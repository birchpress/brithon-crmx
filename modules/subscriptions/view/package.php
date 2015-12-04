<?php

birch_ns( 'brithoncrmx.subscriptions.view.admin.subscriptions', function( $ns ) {

        global $brithoncrmx;

        $ns->init = function() use ( $ns ) {
            add_action( 'init', array( $ns, 'wp_init' ) );
            add_action( 'admin_menu', array( $ns, 'create_admin_menus' ) );
        };

        $ns->wp_init = function() use ( $ns, $brithoncrmx ) {
            global $birchpress;

            $params = array(
                'ajax_url' => admin_url( 'admin-ajax.php' )
            );

            if ( is_main_site() ) {
                $birchpress->view->register_3rd_scripts();
                $birchpress->view->register_core_scripts();
                wp_register_script( 'brithoncrmx_subscriptions_apps_admin_subscriptions',
                    $brithoncrmx->plugin_url() . '/modules/subscriptions/assets/js/apps/admin/subscriptions/index.bundle.js',
                    array( 'birchpress', 'react-with-addons', 'immutable' ) );
                wp_localize_script( 'brithoncrmx_subscriptions_apps_admin_subscriptions',
                    'brithoncrmx_subscriptions_apps_admin_subscriptions', $params );

                wp_enqueue_script( 'brithoncrmx_subscriptions_apps_admin_subscriptions' );
            }
        };

        $ns->create_admin_menus = function() use ( $ns ) {
            add_menu_page( __( 'Account Information', 'brithoncrmx' ),
                __( 'Account', 'brithoncrmx' ), 'read',
                'brithoncrmx/subscriptions', array( $ns, 'render_setting_page' ), '', 80 );
        };

        $ns->render_setting_page = function() use ( $ns ) {
?>
            <h3><?php _e( 'Account Information', 'brithoncrmx' ) ?></h3>
            <section id="birchpress-account-info">

            </section>
<?php
        };

    } );
