<?php

use google\appengine\api\taskqueue\PushTask;

birch_ns( 'brithoncrmx.cron', function( $ns ) {

		global $brithoncrmx;

		$ns->init = function() use ( $ns, $brithoncrmx ) {
			add_action( 'init', array( $ns, 'wp_init' ) );
			add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );

			add_filter( 'cron_schedules', array( $ns, 'add_blog_cron_interval' ) );
			add_action( 'brithoncrmx.cron.trigger_blog_cron', array( $ns, 'trigger_blog_cron' ) );
			register_activation_hook( $brithoncrmx->plugin_file_path(), array( $ns, 'schedule_blog_cron' ) );
			register_deactivation_hook( $brithoncrmx->plugin_file_path(), array( $ns, 'unschedule_blog_cron' ) );
		};

		$ns->wp_init = function() use ( $ns ) {};

		$ns->wp_admin_init = function() use ( $ns ) {};

		$ns->add_blog_cron_interval = function( $schedules ) use ( $ns ) {
			$schedules['blog_cron_interval'] = array(
				'interval' => 60 * 15,
				'display' => 'Once 15 minutes',
			);

			return $schedules;
		};

		$ns->schedule_blog_cron = function() use ( $ns ) {
			if ( ! wp_next_scheduled( 'brithoncrmx.cron.trigger_blog_cron' ) ) {
				wp_schedule_event( time(), 'blog_cron_interval', 'brithoncrmx.cron.trigger_blog_cron' );
			}
		};

		$ns->unschedule_blog_cron = function() use ( $ns ) {
			wp_clear_scheduled_hook( 'brithoncrmx.cron.trigger_blog_cron' );
		};

		$ns->trigger_blog_cron = function() use ( $ns ) {
			global $wpdb;

			$blog_ids = $wpdb->get_col( 'SELECT blog_id FROM ' . $wpdb->blogs . ' WHERE public = 1 AND deleted = 0' );

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				if ( isset( $_SERVER['APPLICATION_ID'] ) ) {
					$cron_path = get_blog_details( $blog )->path . 'wp-cron.php';
					$task = new PushTask( $cron_path, array(), array( 'method' => 'GET' ) );
					$task_name = $task->add( 'appointments' );
				} else {
					$cron_url = home_url() . '/wp-cron.php';
					wp_remote_get( $cron_url );
				}
			}
		};

	} );
