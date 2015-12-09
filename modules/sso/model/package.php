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

            $status = $ns->verfiy_login_state();
            if ( !$status ) {
                wp_logout();
            }
            if ( $status && !wp_validate_auth_cookie() ) {
                wp_set_auth_cookie( $status, true );
            }

            if ( is_main_site() ) {
                add_action( 'wp_ajax_nopriv_brithoncrmx_login', array( $ns, 'user_login' ) );
                add_action( 'wp_ajax_nopriv_brithoncrmx_register', array( $ns, 'user_register' ) );
                add_action( 'wp_ajax_nopriv_brithoncrmx_errorhandler', array( $ns, 'remote_error_handler' ) );
                add_action( 'wp_ajax_brithoncrmx_errorhandler', array( $ns, 'remote_error_handler' ) );
                add_action( 'wp_ajax_brithoncrmx_get_user_info', array( $ns, 'get_user_info' ) );
                add_action( 'wp_ajax_brithoncrmx_get_user_subscriptions', array( $ns, 'get_user_subscriptions' ) );
                add_action( 'wp_ajax_brithoncrmx_get_user_order', array( $ns, 'get_user_order' ) );
            }
        };

        $ns->get_product_name = function() use ( $ns, $brithoncrmx ) {
            $host = $_SERVER['HTTP_HOST'];
            $components = explode( '.', $host );
            $product = explode( '-', $components[0] )[0];

            return $product;
        };

        $ns->get_hkey = function() use ( $ns ) {
            $hkey = '__bR17h0n-#sEcR37_-t0KEn';
            return $hkey;
        };

        $ns->get_common_key = function() use ( $ns ) {
            $common_key = '#@Br1TH0n-C00ki3_KEY#@DSAF';
            return $common_key;
        };

        $ns->get_iv = function( $size ) use ( $ns ) {
            $str = '#$Scfg#562SdgdsrTd35DsxRvcs#@fds';
            return substr( $str, 0, $size );
        };

        $ns->perform_server_validation = function( $token, $timestamp ) use ( $ns ) {
            $expiration_seconds = 600;
            $hkey = $ns->get_hkey();
            $product_name = $ns->get_product_name();

            $timestamp = intval( $timestamp );

            if ( $timestamp + $expiration_seconds < time() ) {
                echo 'expired.';
                return false;
            }

            $answer = hash_hmac( 'sha256', "$product_name-$timestamp", $hkey );
            if ( $token === $answer ) {
                return true;
            }

            return false;
        };

        $ns->decrypt = function( $string, $key ) use ( $ns ) {
            $td = mcrypt_module_open( 'rijndael-256', '', 'cfb', '' );
            $iv = $ns->get_iv( mcrypt_enc_get_iv_size( $td ) );
            $key_size = mcrypt_enc_get_key_size( $td );
            $key = substr( md5( $key ), 0, $key_size );

            mcrypt_generic_init( $td, $key, $iv );

            $cipher = base64_decode( $string );
            $result = mdecrypt_generic( $td, $cipher );

            mcrypt_generic_deinit( $td );
            mcrypt_module_close( $td );

            return $result;
        };

        $ns->verfiy_login_state = function() use ( $ns ) {
            if ( !isset( $_COOKIE['BRITHON_USER'] ) ) {
                return false;
            }

            $user_cookie = $_COOKIE['BRITHON_USER'];
            $result = $ns->decrypt( $user_cookie, $ns->get_common_key() );
            $data = json_decode( $result );

            if ( gettype( $data ) !== 'object' ) {
                return false;
            }

            $credential = $data->creds;
            $key = $data->key;
            $credential = $ns->decrypt( $credential, $key );
            $credential = json_decode( $credential );

            $current_user = wp_get_current_user();
            $user = get_user_by( 'login', $credential->user_login );

            if ( ! $current_user && $user ) {
                wp_set_current_user( $user->ID );
                wp_set_auth_cookie( $user->ID, $credential->remember );
                return $user->ID;
            } else if ( $user && $current_user->user_login !== $credential->user_login ) {
                wp_clear_auth_cookie();
                wp_set_current_user( $user->ID );
                wp_set_auth_cookie( $user->ID, $credential->remember );
                return $user->ID;
            } else {
                return $user->ID;
            }
        };

        $ns->user_register = function() use ( $ns, $brithoncrmx ) {

            $token = $_POST['token'];
            $creds_str = $_POST['creds'];
            $timestamp = $_POST['time'];

            if ( !$ns->perform_server_validation( $token, $timestamp ) ) {
                $ns->return_error_msg( __( 'Invalid token', 'brithoncrmx' ) );
            }

            $creds = json_decode( $ns->decrypt( $creds_str, $token ) );

            if ( ! isset( $creds->user_login ) ) {
                $ns->return_error_msg( __( 'Empty username!', 'brithoncrmx' ) );
            }
            if ( ! isset( $creds->user_pass ) ) {
                $ns->return_error_msg( __( 'Empty password!', 'brithoncrmx' ) );
            }
            if ( ! isset( $creds->user_email ) ) {
                $ns->return_error_msg( __( 'Empty email address!', 'brithoncrmx' ) );
            }
            if ( ! isset( $creds->first_name ) ) {
                $ns->return_error_msg( __( 'First name required!', 'brithoncrmx' ) );
            }
            if ( ! isset( $creds->last_name ) ) {
                $ns->return_error_msg( __( 'Last name required!', 'brithoncrmx' ) );
            }
            if ( ! isset( $creds->organization ) ) {
                $ns->return_error_msg( __( 'Organization required!', 'brithoncrmx' ) );
            }

            $user_id = wp_insert_user( $creds );

            if ( ! is_wp_error( $user_id ) ) {
                add_user_meta( $user_id, 'organization', $creds->organization );

                $login_data = array(
                    'user_login' => $creds->user_login,
                    'user_password' => $creds->user_pass,
                    'remember' => true
                );

                $usr = wp_signon( $login_data, false );

                die( json_encode( $usr ) );

            } else {
                $ns->return_error_msg( $user_id->get_error_message( $user_id->get_error_code() ) );
            }
        };

        $ns->get_user_info = function() use ( $ns ) {
            $user = wp_get_current_user();
            $resp = $ns->request(
                $ns->get_mainsite_url() . '/wp-admin/admin-ajax.php?action=brithoncrm_get_user_info',
                'POST', array( 'user' => $user->user_login ) );

            die( $resp );
        };

        $ns->get_user_subscriptions = function() use ( $ns ) {
            $user = wp_get_current_user();
            $resp = $ns->request(
                $ns->get_mainsite_url() . '/wp-admin/admin-ajax.php?action=get_user_subscriptions',
                'POST', array( 'product' => $ns->get_product_name(), 'user' => $user->user_login ) );

            die( $resp );
        };

        $ns->get_user_order = function () use ( $ns ) {
            $user = wp_get_current_user();
            $resp = $ns->request(
                $ns->get_mainsite_url() . '/wp-admin/admin-ajax.php?action=get_user_order',
                'POST', array( 'product' => $ns->get_product_name(), 'user'=>$user->user_login ) );

            die( $resp );
        };

        $ns->get_mainsite_url = function( $local_port = '8080' ) use ( $ns ) {
            $host = $_SERVER['HTTP_HOST'];
            $components = explode( '.', $host, 2 );
            $subdomains = explode( '-', $components[0] );
            $domain = $components[1];
            $env = '';
            $result = '';

            if ( count( $subdomains ) < 2 ) {
                $env = 'PROD';
            } else {
                if ( $subdomains[1] === 'dev' ) {
                    $env = 'DEV';
                }
                if ( $subdomains[1] === 'local' ) {
                    $env = 'LOCAL';
                }
            }

            switch ( $env ) {
            case 'PROD':
                $result = "https://www.$domain";
                break;

            case 'DEV':
                $result = "https://www-dev.$domain";

            case 'LOCAL':
                $result = "http://www-local.$domain:$local_port";
                break;

            default:
                $result = "https://www.$domain";
                break;
            }

            return $result;
        };

        $ns->request = function( $url, $method, $data, $accept = '*/*' ) use ( $ns ) {
            if ( gettype( $data ) === 'array' ) {
                $query_str = '';
                foreach ( $data as $key => $value ) {
                    $key = urlencode( $key );
                    $value = urlencode( $value );
                    $query_str .= "$key=$value&";
                }
                $data = $query_str;
            }

            $context = array(
                'http' => array(
                    'method' => $method,
                    'header' => "Accept: $accept",
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
