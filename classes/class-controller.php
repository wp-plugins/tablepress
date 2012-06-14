<?php
/**
 * TablePress Base Controller with members and methods for all controllers
 *
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Base Controller class
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */
abstract class TablePress_Controller {

	/**
	 * Instance of the Options Model
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $model_options;

	/**
	 * Instance of the Table Model
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $model_table;

	/**
	 * File name of the admin screens's parent page in the admin menu
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $parent_page = 'tools.php';

	/**
	 * Whether TablePress admin screens are a top-level menu item in the admin menu
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $is_top_level_page = false;

	/**
	 * Initialize all controllers, by loading Plugin and User Options, and performing an update check
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->model_options = TablePress::load_model( 'options' );
		$this->model_table = TablePress::load_model( 'table' );

		// update check, in all controllers (frontend and admin), to make sure we always have up-to-date options
		$this->plugin_update_check(); // should be done very early

		// Admin Page Menu entry, needed for construction of plugin URLs
		$this->parent_page = apply_filters( 'tablepress_admin_menu_parent_page', $this->model_options->get( 'admin_menu_parent_page' ) );
		$this->is_top_level_page = in_array( $this->parent_page, array( 'top', 'middle', 'bottom' ) );
	}

	/**
	 * Check if the plugin was updated and perform necessary actions, like updating the options
	 *
	 * @since 1.0.0
	 */
	protected function plugin_update_check() {
		// Save initial set of plugin options, and time of first activation of the plugin, on first activation
		if ( 0 == $this->model_options->get( 'first_activation' ) ) {
			$this->model_options->update( array(
				'first_activation' => current_time( 'timestamp' )
			) );
		}

		// Update Plugin Options and Table Options, if necessary
		if ( $this->model_options->get( 'plugin_options_db_version' ) < TablePress::db_version ) {
			$this->model_options->merge_plugin_options_defaults();
			$this->model_options->update( array(
				'plugin_options_db_version' => TablePress::db_version,
				'prev_tablepress_version' => $this->model_options->get( 'tablepress_version' ),
				'tablepress_version' => TablePress::version,
				'message_plugin_update' => true
			) );

			$this->model_table->merge_table_options_defaults();
		}

		// Update User Options, if necessary
		// User Options are not saved in DB until first change occurs
		if ( is_user_logged_in() && ( $this->model_options->get( 'user_options_db_version' ) < TablePress::db_version ) ) {
			$this->model_options->merge_user_options_defaults();
			$this->model_options->update( array(
				'user_options_db_version' => TablePress::db_version
			) );
		}
	}

} // class TablePress_Controller