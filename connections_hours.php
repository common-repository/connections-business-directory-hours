<?php
/**
 * An Extension for the Connections plugin which adds a metabox for
 * adding the business hours of operation and a widget to display
 * them.
 *
 * @package   Connections Business Directory Extension - Open Hours
 * @category  Extension
 * @author    Steven A. Zahm
 * @license   GPL-2.0+
 * @link      https://connections-pro.com
 * @copyright 2021 Steven A. Zahm
 *
 * @wordpress-plugin
 * Plugin Name:       Connections Business Directory Extension - Open Hours
 * Plugin URI:        https://connections-pro.com/add-on/hours/
 * Description:       An extension for the Connections plugin which allows you to add the business hours of operation to an entry and a widget to display them.
 * Version:           1.2.1
 * Author:            Steven A. Zahm
 * Author URI:        https://connections-pro.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       connections_hours
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists('Connections_Business_Hours') ) {

	class Connections_Business_Hours {

		const VERSION = '1.2.1';

		/**
		 * Stores the instance of this class.
		 *
		 * @since 1.1
		 *
		 * @var Connections_Business_Hours
		 */
		private static $instance;

		/**
		 * @var string The absolute path this this file.
		 *
		 * @since 1.1
		 */
		private $file = '';

		/**
		 * @var string The URL to the plugin's folder.
		 *
		 * @since 1.1
		 */
		private $url = '';

		/**
		 * @var string The absolute path to this plugin's folder.
		 *
		 * @since 1.1
		 */
		private $path = '';

		/**
		 * @var string The basename of the plugin.
		 *
		 * @since 1.1
		 */
		private $basename = '';

		/**
		 * Connections_Business_Hours constructor.
		 */
		public function __construct() { /* Do nothing here */ }

		/**
		 * @since 1.1
		 *
		 * @return Connections_Business_Hours
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Connections_Business_Hours ) ) {

				self::$instance = $self = new self;

				$self->file     = __FILE__;
				$self->url      = plugin_dir_url( $self->file );
				$self->path     = plugin_dir_path( $self->file );
				$self->basename = plugin_basename( $self->file );

				/**
				 * This should run on the `plugins_loaded` action hook. Since the extension loads on the
				 * `plugins_loaded` action hook, load immediately.
				 */
				cnText_Domain::register(
					'connections_hours',
					$self->basename,
					'load'
				);

				self::loadDependencies();
				self::hooks();
			}

			return self::$instance;
		}

		/**
		 * @since 1.1
		 *
		 * @return string
		 */
		public function getBaseURL() {

			return $this->url;
		}

		private static function loadDependencies() {

			require_once( 'includes/class.widgets.php' );
		}

		/**
		 * @since 1.1
		 */
		private static function hooks() {

			// Register CSS and JavaScript.
			add_action( 'init', array( __CLASS__ , 'registerScripts' ) );

			if ( is_admin() ) {

				// Enqueue the admin CSS and JS
				add_action( 'cn_admin_enqueue_edit_styles', array( __CLASS__, 'adminStyles' ) );

				// Since we're using a custom field, we need to add our own sanitization method.
				add_filter( 'cn_meta_sanitize_field-business_hours', array( __CLASS__, 'sanitize') );
			}

			// Register the metabox and fields.
			add_action( 'cn_metabox', array( __CLASS__, 'registerMetabox') );

			// Business Hours uses a custom field type, so let's add the action to add it.
			add_action( 'cn_meta_field-business_hours', array( __CLASS__, 'field' ), 10, 2 );

			// Add the business hours option to the admin settings page.
			add_filter( 'cn_content_blocks', array( __CLASS__, 'settingsOption') );

			// Enqueue the public CSS
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts' ), 11 );

			// Add the action that'll be run when calling $entry->getContentBlock( 'business_hours' ) from within a template.
			add_action( 'cn_output_meta_field-business_hours', array( __CLASS__, 'block' ), 10, 4 );

			// Register the widget.
			add_action( 'widgets_init', array( 'cnbhHoursWidget', 'register' ) );
		}

		/**
		 * Callback for the `init` action.
		 *
		 * Register the CSS and JS files.
		 *
		 * @since 1.0
		 */
		public static function registerScripts() {

			$url = Connections_Business_Hours()->getBaseURL();
			$url = cnURL::makeProtocolRelative( $url );

			// If SCRIPT_DEBUG is set and TRUE load the non-minified JS files, otherwise, load the minified files.
			$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

			$requiredCSS = class_exists( 'Connections_Form' ) ? array( 'cn-public', 'cn-form-public' ) : array( 'cn-public' );

			// Register CSS.
			wp_register_style( 'cnbh-admin' , "{$url}assets/css/cnbh-admin$min.css", array( 'cn-admin', 'cn-admin-jquery-ui' ) , self::VERSION );
			wp_register_style( 'cnbh-public', "{$url}assets/css/cnbh-public$min.css", $requiredCSS, self::VERSION );

			// Register JavaScript.
			wp_register_script( 'jquery-timepicker' , "{$url}assets/js/jquery-ui-timepicker-addon$min.js", array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ) , '1.4.3' );
			wp_register_script( 'cnbh-ui-js' , "{$url}assets/js/cnbh-common$min.js", array( 'jquery-timepicker' ) , self::VERSION, true );

			wp_localize_script( 'cnbh-ui-js', 'cnbhDateTimePickerOptions', Connections_Business_Hours::dateTimePickerOptions() );
		}

		/**
		 * Callback for the `cn_admin_enqueue_edit_styles` action.
		 *
		 * Enqueues the CSS on the Connections admin pages only.
		 *
		 * @since 1.0
		 *
		 * @param string $pageHook The current admin page hook.
		 */
		public static function adminStyles( $pageHook ) {

			wp_enqueue_style( 'cnbh-admin' );
		}

		/**
		 * Callback for the `wp_enqueue_scripts` action.
		 *
		 * Enqueues the CSS.
		 *
		 * NOTE: This will only be enqueued if Form is installed and active
		 * because a CSS file registered by Form is listed as a dependency
		 * when registering 'cnbh-public'.
		 *
		 * @since  1.0
		 */
		public static function enqueueScripts() {

			wp_enqueue_style( 'cnbh-public' );
		}

		/**
		 * Returns the date picker options.
		 *
		 * @since 1.0
		 *
		 * @return array
		 */
		public static function dateTimePickerOptions() {

			$options = array(
				'currentText'   => __( 'Now', 'connections_hours' ),
				'closeText'     => __( 'Done', 'connections_hours' ),
				'amNames'       => array( __( 'AM', 'connections_hours' ), __( 'A', 'connections_hours' ) ),
				'pmNames'       => array( __( 'PM', 'connections_hours' ), __( 'P', 'connections_hours' ) ),
				'timeFormat'    => cnFormatting::dateFormatPHPTojQueryUI( self::timeFormat() ),
				'timeSuffix'    => '',
				'timeOnlyTitle' => __( 'Choose Time', 'connections_hours' ),
				'timeText'      => __( 'Time', 'connections_hours' ),
				'hourText'      => __( 'Hour', 'connections_hours' ),
				'minuteText'    => __( 'Minute', 'connections_hours' ),
				'secondText'    => __( 'Second', 'connections_hours' ),
				'millisecText'  => __( 'Millisecond', 'connections_hours' ),
				'microsecText'  => __( 'Microsecond', 'connections_hours' ),
				'timezoneText'  => __( 'Time Zone', 'connections_hours' ),
				'isRTL'         => is_rtl(),
				'parse'         => 'loose',
				);

			return apply_filters( 'cnbh_timepicker_options', $options );
		}

		/**
		 * Returns the time format.
		 *
		 * @since 1.0
		 *
		 * @return string
		 */
		public static function timeFormat() {

			return apply_filters( 'cnbh_time_format', get_option('time_format') );
		}

		/**
		 * Format a time supplied as string to a format from a format.
		 *
		 * @since 1.0
		 *
		 * @param string $value
		 * @param null   $to
		 * @param null   $from
		 *
		 * @return string
		 */
		public static function formatTime( $value, $to = NULL, $from = NULL ) {

			$to   = is_null( $to ) ? self::timeFormat() : $to;
			$from = is_null( $from ) ? self::timeFormat() : $from;

			if ( strlen( $value ) > 0 ) {

				return cnDate::createFromFormat( $from, $value )->format( $to );

			} else {

				return $value;
			}
		}

		/**
		 * Return the weekdays with teh start day as defined in the WP General Settings.
		 *
		 * @since 1.0
		 *
		 * @return array
		 */
		public static function getWeekdays() {
			global $wp_locale;

			// Output the weekdays sorted by the start of the week
			// set in the WP General Settings. The array keys need to be
			// retained which is why array_shift and array push are not
			// being used.
			$weekStart = apply_filters( 'cnbh_start_of_week', get_option('start_of_week') );
			$weekday   = $wp_locale->weekday;

			for ( $i = 0; $i < $weekStart; $i++ ) {

				$day = array_slice( $weekday, 0, 1, true );
				unset( $weekday[ $i ] );

				$weekday = $weekday + $day;
			}

			return $weekday;
		}

		/**
		 * Callback for the `cn_content_blocks` filter.
		 *
		 * Add the Business Open Hours as an option to display in the Content Block settings.
		 *
		 * @since 1.0
		 *
		 * @param array $blocks
		 *
		 * @return array
		 */
		public static function settingsOption( $blocks ) {

			$blocks['business_hours'] = __( 'Business Hours', 'connections_hours' );

			return $blocks;
		}

		/**
		 * Callback for the `cn_metabox` action.
		 *
		 * Register the business open hours metabox.
		 *
		 * @since 1.0
		 */
		public static function registerMetabox() {

			$atts = array(
				'id'       => 'business-hours',
				'title'    => __( 'Business Hours', 'connections_hours' ),
				'context'  => 'normal',
				'priority' => 'core',
				'fields'   => array(
					array(
						'id'    => 'business_hours',
						'type'  => 'business_hours',
						),
					),
				);

			cnMetaboxAPI::add( $atts );
		}

		/**
		 * Callback for the `cn_meta_field-business_hours` action.
		 *
		 * Display the business open hours fields within the registered metabox.
		 *
		 * @since 1.0
		 *
		 * @param array $field
		 * @param array $value
		 */
		public static function field( $field, $value ) {

			?>

			<table id="start_of_week">

				<thead>
					<tr>
						<th><?php _e( 'Weekday', 'connections_hours' ); ?></th>
						<td><?php _e( 'Open', 'connections_hours' ); ?></td>
						<td><?php _e( 'Close', 'connections_hours' ); ?></td>
						<td><?php _e( 'Add / Remove Period', 'connections_hours' ); ?></td>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th><?php _e( 'Weekday', 'connections_hours' ); ?></th>
						<td><?php _e( 'Open', 'connections_hours' ); ?></td>
						<td><?php _e( 'Close', 'connections_hours' ); ?></td>
						<td><?php _e( 'Add / Remove Period', 'connections_hours' ); ?></td>
					</tr>
				</tfoot>

				<tbody>

					<tr id="cnbh-period" style="display: none;">
						<td>&nbsp;</td>
						<td>
							<?php

							cnHTML::field(
									array(
										'type'     => 'text',
										'class'    => '',
										'id'       => $field['id'] . '[day][period][open]',
										'required' => false,
										'label'    => '',
										'before'   => '',
										'after'    => '',
										'return'   => false,
									)
								);

							?>
						</td>
						<td>
							<?php

							cnHTML::field(
									array(
										'type'     => 'text',
										'class'    => '',
										'id'       => $field['id'] . '[day][period][close]',
										'required' => false,
										'label'    => '',
										'before'   => '',
										'after'    => '',
										'return'   => false,
									)
								);

							?>
						</td>
						<td><span class="button cnbh-remove-period">&ndash;</span><span class="button cnbh-add-period">+</span></td>
					</tr>

				<?php

					if ( ( ! is_array( $value ) ) ) $value = array();

					foreach ( self::getWeekdays() as $key => $day ) {

						// If there are no periods saved for the day,
						// add an empty period to prevent index not found errors.
						if ( ! isset( $value[ $key ] ) ) {

							$value[ $key ] = array(
								0 => array(
									'open'  => '',
									'close' => ''
									),
								);
						}

						foreach ( $value[ $key ] as $period => $data ) {

							$open = cnHTML::field(
										array(
											'type'     => 'text',
											'class'    => array( 'timepicker', 'clearable' ),
											'id'       => $field['id'] . '[' . $key . '][' . $period . '][open]',
											'required' => FALSE,
											'label'    => '',
											'before'   => '',
											'after'    => '',
											'return'   => TRUE,
										),
										self::formatTime( $data['open'], NULL, 'H:i' )
									);

							$close = cnHTML::field(
										array(
											'type'     => 'text',
											'class'    => array( 'timepicker', 'clearable' ),
											'id'       => $field['id'] . '[' . $key . '][' . $period . '][close]',
											'required' => FALSE,
											'label'    => '',
											'before'   => '',
											'after'    => '',
											'return'   => TRUE,
										),
										self::formatTime( $data['close'], NULL, 'H:i' )
									);

							if ( $period == 0 ) {

								// Display the "+" button only. This button should only be shown on the first period of the day.
								$buttons = sprintf( '<span class="button cnbh-remove-period" data-day="%1$d" data-period="%2$d" style="display: none;">–</span><span class="button cnbh-add-period" data-day="%1$d" data-period="%2$d">+</span>',
									$key,
									$period
									);

							} else {

								// Display both buttons. Both buttons should be shown for every period after the first.
								$buttons = sprintf( '<span class="button cnbh-remove-period" data-day="%1$d" data-period="%2$d">–</span><span class="button cnbh-add-period" data-day="%1$d" data-period="%2$d">+</span>',
									$key,
									$period
									);
							}

							printf( '<tr %1$s %2$s %3$s><th>%4$s</th><td>%5$s</td><td>%6$s</td><td>%7$s</td></tr>',
								'class="cnbh-day-' . absint( $key ) . '"',
								$period == 0 ? 'id="cnbh-day-' . absint( $key ) . '"' : '',
								$period == 0 ? 'data-count="' . absint( count( $value[ $key ] ) - 1 ) . '"' : '',
								$period == 0 ? esc_attr( $day ) : '&nbsp;',
								$open,
								$close,
								$buttons
								);
						}

					}

				?>

				</tbody>
			</table>

			<?php

			printf( '<p>%s</p>', __( 'To create a closed day or closed period within a day, leave both the open and close hours blank.', 'connections_hours' ) );

			// Enqueue the JS required for the metabox.
			wp_enqueue_script( 'cnbh-ui-js' );
		}

		/**
		 * Sanitize the times as a text input using the cnSanitize class.
		 *
		 * @since 1.0
		 *
		 * @param array $value The opening/closing hours.
		 *
		 * @return array
		 */
		public static function sanitize( $value ) {

			$hasOpenHours = FALSE;

			if ( empty( $value ) ) return $value;

			foreach ( $value as $key => $day ) {

				foreach ( $day as $period => $time ) {

					if ( 0 < strlen( $time['open'] ) || 0 < strlen( $time['open'] ) ) {

						// Save all time values in 24hr format.
						$time['open']  = self::formatTime( $time['open'], 'H:i' );
						$time['close'] = self::formatTime( $time['close'], 'H:i' );

						$value[ $key ][ $period ]['open']  = cnSanitize::string( 'text', $time['open'] );
						$value[ $key ][ $period ]['close'] = cnSanitize::string( 'text', $time['close'] );

						$hasOpenHours = TRUE;
					}

				}
			}

			if ( ! $hasOpenHours ) {

				return NULL;
			}

			return $value;
		}

		/**
		 * Callback for the `cn_output_meta_field-business_hours` action.
		 *
		 * @since 1.0
		 *
		 * @see cnEntry_HTML::getContentBlock()
		 *
		 * Render the business open hours.
		 *
		 * @param string       $id    The field id.
		 * @param array        $value The business hours data.
		 * @param cnEntry_HTML $entry
		 * @param array        $atts  The shortcode atts array passed from the calling action.
		 *
		 *@internal
		 */
		public static function block( $id, $value, $entry, $atts ) {
			global $wp_locale;

			$defaults = array(
				'header'                => TRUE,
				'footer'                => FALSE,
				'day_name'              => 'full', // Valid options are 'full', 'abbrev' or 'initial'.
				'show_closed_day'       => TRUE,
				'show_closed_period'    => FALSE,
				'show_if_no_hours'      => FALSE,
				'show_open_status'      => TRUE,
				'highlight_open_period' => TRUE,
				'open_close_separator'  => '&ndash;',
				);

			$atts = wp_parse_args( $atts, $defaults );

			if ( ! self::hasOpenHours( $value ) ) return;

			echo '<div class="cnbh-block">';

			// Whether or not to display the open status message.
			if ( $atts['show_open_status'] && self::openStatus( $value ) ) {

				printf( '<p class="cnbh-status cnbh-status-open">%s</p>' , __( 'We are currently open.', 'connections_hours' ) );

			} elseif ( $atts['show_open_status'] ) {

				printf( '<p class="cnbh-status cnbh-status-closed">%s</p>' , __( 'Sorry, we are currently closed.', 'connections_hours' ) );
			}

			?>

			<table class="cnbh">

				<?php if ( $atts['header'] ) : ?>

				<thead>
					<tr>
						<th>&nbsp;</th>
						<th><?php _e( 'Open', 'connections_hours' ); ?></th>
						<th class="cnbh-separator">&nbsp;</th>
						<th><?php _e( 'Close', 'connections_hours' ); ?></th>
					</tr>
				</thead>

				<?php endif; ?>

				<?php if ( $atts['footer'] ) : ?>

				<tfoot>
					<tr>
						<th>&nbsp;</th>
						<th><?php _e( 'Open', 'connections_hours' ); ?></th>
						<th class="cnbh-separator">&nbsp;</th>
						<th><?php _e( 'Close', 'connections_hours' ); ?></th>
					</tr>
				</tfoot>

				<?php endif; ?>

				<tbody>
					<?php

					foreach ( self::getWeekdays() as $key => $day ) {

						// Display the day as either its initial or abbreviation.
						switch ( $atts['day_name'] ) {

							case 'initial' :

								$day = $wp_locale->get_weekday_initial( $day );
								break;

							case 'abbrev' :

								$day = $wp_locale->get_weekday_abbrev( $day );
								break;
						}

						// Show the "Closed" message if there are no open and close hours recorded for the day.
						if ( $atts['show_closed_day'] && ! self::openToday( $value[ $key ] ) ) {

							printf( '<tr %1$s %2$s %3$s><th>%4$s</th><td class="cnbh-closed" colspan="3">%5$s</td></tr>',
								'class="cnbh-day-' . absint( $key ) . '"',
								'id="cnbh-day-' . absint( $key ) . '"',
								'data-count="' . absint( count( $value[ $key ] ) - 1 ) . '"',
								esc_attr( $day ),
								__( 'Closed Today', 'connections_hours' )
								);

							// Exit this loop.
							continue;
						}

						// If there are open and close hours recorded for the day, loop thru the open periods.
						foreach ( $value[ $key ] as $period => $time ) {

							// Show the "Closed" message if there are no open and close hours recorded for the period.
							if ( self::openPeriod( $time ) ) {

							printf( '<tr %1$s %2$s %3$s><th>%4$s</th><td class="cnbh-open">%5$s</td><td class="cnbh-separator">%6$s</td><td class="cnbh-close">%7$s</td></tr>',
								'class="cnbh-day-' . absint( $key ) . ( $atts['highlight_open_period'] && date( 'w', current_time( 'timestamp' ) ) == $key && self::isOpen( $time['open'], $time['close'] ) ? ' cnbh-open-period' : '' ) . '"',
								$period == 0 ? 'id="cnbh-day-' . absint( $key ) . '"' : '',
								$period == 0 ? 'data-count="' . absint( count( $value[ $key ] ) - 1 ) . '"' : '',
								$period == 0 ? esc_attr( $day ) : '&nbsp;',
								self::formatTime( $time['open'], NULL, 'H:i' ),
								esc_attr( $atts['open_close_separator'] ),
								self::formatTime( $time['close'], NULL, 'H:i' )
								);

							} elseif ( $atts['show_closed_period'] && $period > 0 ) {

								printf( '<tr %1$s %2$s %3$s><th>%4$s</th><td class="cnbh-closed" colspan="3">%5$s</td></tr>',
									'class="cnbh-day-' . absint( $key ) . '"',
									'id="cnbh-day-' . absint( $key ) . '"',
									'data-count="' . absint( count( $value[ $key ] ) - 1 ) . '"',
									$period == 0 ? esc_attr( $day ) : '&nbsp;',
									__( 'Closed Period', 'connections_hours' )
									);

							}

						}

					}

					?>

				</tbody>
			</table>

			<?php

			echo '</div>';
		}

		/**
		 * Whether or not the business is currently open or not.
		 *
		 * @param array $value
		 *
		 * @return bool
		 */
		public static function openStatus( $value ) {

			foreach ( self::getWeekdays() as $key => $day ) {

				foreach ( $value[ $key ] as $period => $time ) {

					if ( date( 'w', current_time( 'timestamp' ) ) == $key &&
					     self::openPeriod( $time ) &&
					     self::isOpen( $time['open'], $time['close'] )
					) {

						return TRUE;
					}

				}

			}

			return FALSE;
		}

		/**
		 * Whether or not there are any open hours during the week.
		 *
		 * @since 1.0
		 *
		 * @param array $days
		 *
		 * @return boolean
		 */
		public static function hasOpenHours( $days ) {

			foreach ( $days as $key => $day ) {

				if ( self::openToday( $day ) ) return TRUE;
			}

			return FALSE;
		}

		/**
		 * Whether or not the day has any open periods.
		 *
		 * @since 1.0
		 *
		 * @param array $day
		 *
		 * @return bool
		 */
		private static function openToday( $day ) {

			foreach ( $day as $period => $data ) {

				if ( self::openPeriod( $data ) ) return TRUE;
			}

			return FALSE;
		}

		/**
		 * Whether or not the period is open.
		 *
		 * @since 1.0
		 *
		 * @param array $period
		 *
		 * @return bool
		 */
		private static function openPeriod( $period ) {

			if ( empty( $period ) ) return FALSE;

			if ( ! empty( $period['open'] ) && ! empty( $period['close'] ) ) return TRUE;

			return FALSE;
		}

		/**
		 * Whether or not the business is open.
		 *
		 * @link http://stackoverflow.com/a/17145145
		 *
		 * @since 1.0
		 *
		 * @param string $t1 Time open.
		 * @param string $t2 Time close.
		 * @param null   $tn Time now.
		 *
		 * @return bool
		 */
		private static function isOpen( $t1, $t2, $tn = NULL ) {

			$tn = is_null( $tn ) ? date( 'H:i', current_time( 'timestamp' ) ) : self::formatTime( $tn, 'H:i' );

			$t1 = +str_replace( ':', '', $t1 );
			$t2 = +str_replace( ':', '', $t2 );
			$tn = +str_replace( ':', '', $tn );

			if ( $t2 >= $t1 ) {

				return $t1 <= $tn && $tn < $t2;

			} else {

				return ! ( $t2 <= $tn && $tn < $t1 );
			}

		}

	}

	/**
	 * Start up the extension.
	 *
	 * @since 1.0
	 *
	 * @return Connections_Business_Hours|false
	 */
	function Connections_Business_Hours() {

		if ( class_exists( 'connectionsLoad' ) ) {

			return Connections_Business_Hours::instance();

		} else {

			add_action(
				'admin_notices',
				function() {

					echo '<div id="message" class="error"><p><strong>ERROR:</strong> Connections must be installed and active in order use Connections Business Hours.</p></div>';
				}
			);

			return false;
		}
	}

	/**
	 * We'll load the extension on `plugins_loaded` so we know Connections will be loaded and ready first.
	 * Set priority 11, so we know Form is loaded first.
	 */
	add_action( 'plugins_loaded', 'Connections_Business_Hours', 11 );

}
