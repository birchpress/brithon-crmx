<?php


birch_ns( 'brithoncrmx.sso.model', function( $ns ) {

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
                add_action( 'wp_ajax_nopriv_brithoncrmx_login', array( $ns, 'user_login' ) );
                add_action( 'wp_ajax_nopriv_brithoncrmx_register', array( $ns, 'user_register' ) );
                add_action( 'wp_ajax_nopriv_brithoncrmx_errorhandler', array( $ns, 'remote_error_handler' ) );
                add_action( 'wp_ajax_brithoncrmx_errorhandler', array( $ns, 'remote_error_handler' ) );
            }
        };

        $ns->get_product_name = function() use ( $ns, $brithoncrmx ) {
            $host = $_SERVER['HTTP_HOST'];
            $components = explode('.', $host);
            $product = explode('-', $components[0])[0];

            return $product;
        };

        $ns->get_hkey = function() use ( $ns, $brithoncrmx ) {
            $hkey = '_sEcR37_-t0KEn';
            return $hkey;
        };

        $ns->perform_server_validation = function( $token, $timestamp ) use ( $ns ) {
            $expiration_seconds = 60;
            $hkey = $ns->get_hkey();

            $timestamp = intval( $timestamp );

            if ( $timestamp + $expiration_seconds < time() ) {
                return false;
            }

            if ( $token === hash_hmac( 'sha256', "$product_name-$timestamp", $hkey ) ) {
                return true;
            }

            return false;
        };

        $ns->decrypt = function( $string, $key ) use ( $ns ) {
            $td = mcrypt_module_open( 'rijndael-256', '', 'cfb' );
            $iv = mcrypt_create_iv( mcrypt_enc_get_iv_size( $td ), MCRYPT_DEV_RANDOM );
            $key_size = mcrypt_enc_get_key_size( $td );
            $key = substr( md5( $key ), 0, $key_size );

            mcrypt_generic_init( $td, $key, $iv );

            $cipher = base64_decode( $string );
            $result = mdecrypt_generic( $td, $cipher );

            mcrypt_generic_deinit( $td );
            mcrypt_module_close( $td );

            return $result;
        };

        $ns->user_login = function() use ( $ns ) {
            if ( !isset( $_POST['token'] ) ) {
                $ns->return_error_msg( __( 'Empty validation token!', 'brithoncrmx' ) );
            }

            $token = $_POST['token'];
            $creds_str = $_POST['creds'];

            if ( !$ns->perform_server_validation( $token ) ) {
                $ns->return_error_msg( __( 'Invalid token', 'brithoncrmx' ) );
            }

            $creds = json_decode( $ns->decrypt( $creds_str, $token ) ); 

            if ( !isset( $creds['user_login'] ) ) {
                $ns->return_error_msg( __( 'Empty username!', 'brithoncrmx' ) );
            }
            if ( !isset( $creds['user_password'] ) ) {
                $ns->return_error_msg( __( 'Empty password!', 'brithoncrmx' ) );
            }
            if ( !isset( $creds['remember'] ) ) {
                $creds['remebmer'] = false;
            }

            $user = wp_signon( $creds, false );
            if ( is_wp_error( $user ) ) {
                $ns->return_error_msg( $user->get_error_message() );
            }

            die( json_encode( $user ) );
        };


        $ns->user_register = function() use ( $ns, $brithoncrmx ) {

            $token = $_POST['token'];
            $creds_str = $_POST['creds'];

            if ( !$ns->perform_server_validation( $token ) ) {
                $ns->return_error_msg( __( 'Invalid token', 'brithoncrmx' ) );
            }

            $creds = json_decode( $ns->decrypt( $creds_str, $token ) );

            if ( ! isset($creds['user_login']) ) {
                $ns->return_error_msg( __( 'Empty username!', 'brithoncrmx' ) );
            }
            if ( ! isset($creds['user_pass']) ) {
                $ns->return_error_msg( __( 'Empty password!', 'brithoncrmx' ) );
            }
            if ( ! isset($creds['user_email']) ) {
                $ns->return_error_msg( __( 'Empty email address!', 'brithoncrmx' ) );
            }
            if ( ! isset($creds['first_name']) ) {
                $ns->return_error_msg( __( 'First name required!', 'brithoncrmx' ) );
            }
            if ( ! isset($creds['last_name']) ) {
                $ns->return_error_msg( __( 'Last name required!', 'brithoncrmx' ) );
            }
            if ( ! isset($creds['organization']) ) {
                $ns->return_error_msg( __( 'Organization required!', 'brithoncrmx' ) );
            }

            $user_id = wp_insert_user( $creds );

            if ( ! is_wp_error( $user_id ) ) {
                add_user_meta( $user_id, 'organization', $creds['organization'] );

                $creds = array_merge($creds, array('remebmer' => true));

                $usr = wp_signon( $creds, false );

                die( json_encode( $usr ) );

            } else {
                $ns->return_err_msg( $user_id->get_error_message( $user_id->get_error_code() ) );
            }
        };

        $ns->request = function( $url, $method, $data ) use ( $ns ) {
            $context = array(
                'http' => array(
                    'method' => $method,
                    'header' => '',
                    'content' => $data
                )
            );
            $context = stream_context_create( $context );
            return file_get_contents( $url, false, $context );
        };

        $ns->return_error_msg = function( $msg ) use ( $ns ) {
            die( json_encode( array(
                        'message' => $msg
                    ) ) );
        };

        $ns->remote_error_handler = function( $msg ) use ( $ns ) {
            die( json_encode( array(
                        'message' => $_POST['message']
                    ) ) );
        };
    } );
