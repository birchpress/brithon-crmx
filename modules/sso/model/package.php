<?php


birch_ns( 'brithoncrmx.sso.model', function( $ns ) {

        global $brithoncrm;

        $ns->init = function() use ( $ns ) {
            register_activation_hook( __FILE__, array( $ns, 'plugin_init' ) );
            add_action( 'init', array( $ns, 'wp_init' ) );
        };

        $ns->plugin_init = function() use ( $ns ) {
            global $birchpress;

        };

        $ns->wp_init = function() use ( $ns, $brithoncrm ) {
            global $birchpress;

            if ( is_main_site() ) {
                add_action( 'wp_ajax_nopriv_brithoncrmx_login', array( $ns, 'user_login' ) );
                add_action( 'wp_ajax_nopriv_brithoncrmx_register', array( $ns, 'user_register' ) );
                add_action( 'wp_ajax_nopriv_brithoncrmx_errorhandler', array( $ns, 'remote_error_handler' ) );
                add_action( 'wp_ajax_brithoncrmx_errorhandler', array( $ns, 'remote_error_handler' ) );
            }
        };

        $ns->perform_server_validation = function( $token ) use ( $ns ) {
            $main_site = 'http://www.brithon.com/wp-admin/admin-ajax.php'
            $result = $ns->request( $main_site, 'POST', array( 'token' => $token ) );
            $result = json_decode( $result );
            if ( !$result ) {
                return false;
            }
            if ( !$result['status'] ) {
                return false;
            }
            return true;
        };

        $ns->user_login = function() use ( $ns ) {
            if ( !isset( $_POST['username'] ) ) {
                $ns->return_error_msg( __( 'Empty username!', 'brithoncrmx' ) );
            }
            if ( !isset( $_POST['password'] ) ) {
                $ns->return_error_msg( __( 'Empty password!', 'brithoncrmx' ) );
            }
            if ( !isset( $_POST['remember'] ) ) {
                $_POST['remebmer'] = false;
            }
            if ( !isset( $_POST['token'] ) ) {
                $ns->return_error_msg( __( 'Empty validation token!', 'brithoncrmx' ) );
            }

            $username = $_POST['username'];
            $password = $_POST['password'];
            $remember = $_POST['remebmer'];

            $token = $_POST['token'];

            if ( !$ns->perform_server_validation( $token ) ) {
                $ns->return_error_msg( __( 'Invalid token', 'brithoncrmx' ) );
            }

            $creds = array(
                'user_login' => $username,
                'user_password' => $password,
                'remebmer' => $remember,
            );
            $user = wp_signon( $creds, false );
            if ( is_wp_error( $user ) ) {
                $ns->return_error_msg( $user->get_error_message() );
            }

            die( json_encode( $user ) );
        };


        $ns->user_register = function() use ( $ns, $brithoncrm ) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $org = $_POST['org'];
            $token = $_POST['token'];

            if ( ! $token ) {
                $ns->return_error_msg( __( 'Empty validation token!', 'brithoncrmx' ) );
            }
            if ( ! $username ) {
                $ns->return_error_msg( __( 'Empty username!', 'brithoncrmx' ) );
            }
            if ( ! $password ) {
                $ns->return_error_msg( __( 'Empty password!', 'brithoncrmx' ) );
            }
            if ( ! $email ) {
                $ns->return_error_msg( __( 'Empty email address!', 'brithoncrmx' ) );
            }
            if ( ! $first_name ) {
                $ns->return_error_msg( __( 'First name required!', 'brithoncrmx' ) );
            }
            if ( ! $last_name ) {
                $ns->return_error_msg( __( 'Last name required!', 'brithoncrmx' ) );
            }
            if ( ! $org ) {
                $ns->return_error_msg( __( 'Organization required!', 'brithoncrmx' ) );
            }

            if ( !$ns->perform_server_validation( $token ) ) {
                $ns->return_error_msg( __( 'Invalid token', 'brithoncrmx' ) );
            }

            $userdata = array(
                'user_login' => $username,
                'user_pass' => $password,
                'user_email' => $email,
                'display_name' => "$first_name $last_name",
                'nickname' => "$first_name $last_name",
                'first_name' => $first_name,
                'last_name' => $last_name,
            );

            $user_id = wp_insert_user( $userdata );

            if ( ! is_wp_error( $user_id ) ) {
                add_user_meta( $user_id, 'organization', $org );
                $creds = array();
                $creds['user_login'] = $username;
                $creds['user_password'] = $password;
                $creds['remember'] = true;
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
        }

        $ns->remote_error_handler = function( $msg ) use ( $ns ) {
            die( json_encode( array(
                        'message' => $_POST['message']
                    ) ) );
        }
    } );
