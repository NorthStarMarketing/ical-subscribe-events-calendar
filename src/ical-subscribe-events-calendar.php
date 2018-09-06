<?php
/**
 * An extention to enable iCal Subscribe for The Events Calendar.
 *
 * @package     iCal Subscribe
 * @author      North Star Marketing <tech@northstarmarketing.com>
 * @copyright   2018 North Star Marketing
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: iCal Subscribe for The Events Calendar
 * Description: Adds a link to the calendar page so users can subscribe to the event feed.
 * Version:     1.0.0
 * Author:      North Star Marketing
 * Author URI:  https://www.northstarmarketing.com
 * Text Domain: ical-subscribe-events-calendar
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

add_action( 'init', 'is_events_calendar_activated' );

/**
 * Checks if the events calendar plugin is activated.
 */
function isec_is_events_calendar_activated() {

	if ( ! class_exists( 'Tribe__Events__Main' ) ) {

		if ( current_user_can( 'activate_plugins' ) ) {

			add_action( 'admin_init', 'isec_plugin_deactivate' );

			// Show a notice to the user that is able to activate this.
			add_action( 'admin_notices', 'isec_plugin_admin_notice' );
		}

		add_action( 'admin_init', 'isec_plugin_deactivate' );

	} else {

		add_action( 'plugins_loaded', 'isec_plugin_init' );
		add_action( 'tribe_events_after_footer', 'isec_add_ical_subscribe_link' );
		add_action( 'pre_get_posts', 'isec_year_ics_export' );

	}

}

/**
 * Deactivates the plugin.
 */
function isec_plugin_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Shows an error message if events calendar plugin is not activated.
 */
function isec_plugin_admin_notice() {
	echo '<div class="error"><p>The Events Calendar must be installed and activated before you can use <strong>iCal Subscribe for The Events Calendar</strong>.</p></div>';
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}

/**
 * Adds the iCal Subscribe link to the bottom of the calendar page.
 */
function isec_add_ical_subscribe_link() {
	$url  = parse_url( tribe_get_ical_link( 'upcoming' ) );
	$feed = str_replace( $url['scheme'], 'webcal', tribe_get_ical_link( 'upcoming' ) );
	echo '<a href="' . $feed . '" class="tribe-events-button" style="margin-top: 21px;">+ Subscribe</a>';
}

/**
 * Sets up the query for the iCal Subscribe link.
 *
 * @param object $query The current wp_query to modify.
 */
function isec_year_ics_export( $query ) {
	if ( ! isset( $_GET['ical'] ) ) {
		return;
	}
	if ( ! isset( $query->tribe_is_event_query ) || ! $query->tribe_is_event_query ) {
		return;
	}
	$query->set( 'post_type', 'tribe_events' );
	$query->set( 'posts_per_page', 100000 );
	$query->set( 'eventDisplay', 'list' );
}
