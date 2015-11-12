<?php

birch_ns( 'brithoncrmx.cron', function( $ns ) {

		global $brithoncrmx;

		$ns->init = function() use ( $ns ) {
			add_action( 'init', array( $ns, 'wp_init' ) );
			add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );
		};

		$ns->wp_init = function() use ( $ns ) {
			add_filter( 'cron_schedules', array( $ns, 'add_blog_cron_interval' ) );
			add_action( 'wp', array( $ns, 'schedule_blog_cron' ) );
		};

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

		$ns->trigger_blog_cron = function() use ( $ns ) {
			global $wpdb;

			$blog_ids = $wpdb->get_col( 'SELECT blog_id FROM ' . $wpdb->blogs . ' WHERE public = 1 AND deleted = 0' );

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$cron_url = home_url().'/wp-cron.php';

				wp_remote_get( $cron_url );
			}
		};

		$ns->wp_admin_init = function() use ( $ns ) {};

	} );
