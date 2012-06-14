<?php
/**
 * Plugin Options/Save Custom CSS Credentials Form View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Plugin Options/Save Custom CSS Credentials Form View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Options_Custom_CSS_View extends TablePress_View {

	/**
	 * Set up the view with data and do things that are specific for this view
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action for this view
	 * @param array $data Data for this view
	 */
	public function setup( $action, $data ) {
		$this->action = 'options'; // set this manually here, to get correct page title and nav bar entries
		$this->data = $data;

		// Set page <title>
		$GLOBALS['title'] = sprintf( __( '%s &lsaquo; TablePress', 'tablepress' ), $this->data['view_actions'][ $this->action ]['page_title'] );

		$this->add_header_message( '<strong>' . __( 'Attention: Further action is required to save the changes to your &quot;Custom CSS&quot;!', 'tablepress' ) . '</strong>', 'updated' );

		// admin page helpers, like script/style loading, could be moved to view
		$this->admin_page = TablePress::load_class( 'TablePress_Admin_Page', 'class-admin-page-helper.php', 'classes' );
		$this->admin_page->enqueue_style( 'common' );

		$this->admin_page->add_admin_footer_text();

		$this->add_text_box( 'explanation-text', array( &$this, 'textbox_explanation_text' ), 'normal' );
		$this->add_text_box( 'credentials-form', array( &$this, 'textbox_credentials_form' ), 'normal' );
		$this->add_text_box( 'proceed-no-file-saving', array( &$this, 'textbox_proceed_no_file_saving' ), 'submit' );
	}

	/**
	 * Render the current view (in this view: without form tag)
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>
		<div id="tablepress-page" class="wrap">
		<?php screen_icon( 'tablepress' ); ?>
		<?php
			$this->print_nav_tab_menu();
			// print all header messages
			foreach ( $this->header_messages as $message ) {
				echo $message;
			}

			$this->do_text_boxes( 'header' );
		?>
			<div id="poststuff" class="metabox-holder<?php echo ( isset( $GLOBALS['screen_layout_columns'] ) && ( 2 == $GLOBALS['screen_layout_columns'] ) ) ? ' has-right-sidebar' : ''; ?>">
				<div id="side-info-column" class="inner-sidebar">
				<?php
					// print all boxes in the sidebar
					$this->do_text_boxes( 'side' );
					$this->do_meta_boxes( 'side' );
				?>
				</div>
				<div id="post-body">
					<div id="post-body-content">
					<?php
					$this->do_text_boxes( 'normal' );
					$this->do_meta_boxes( 'normal' );

					$this->do_text_boxes( 'additional' );
					$this->do_meta_boxes( 'additional' );

					// print all submit buttons
					$this->do_text_boxes( 'submit' );
					?>
					</div>
				</div>
				<br class="clear" />
			</div>
		</div>
		<?php
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function textbox_explanation_text( $data, $box ) {
		?>
		<p>
			<?php _e( 'Due to the configuration of your server, TablePress was not able to automatically save your &quot;Custom CSS&quot; to a file.', 'tablepress' ); ?>
			<?php printf( __( 'To try again via the same method that you use for updating plugins or themes, please fill out the &quot;%s&quot; form below.', 'tablepress' ), __( 'Connection Information', 'default' ) ); ?>
		</p>
		<?php
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function textbox_credentials_form( $data, $box ) {
		echo $data['credentials_form'];
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function textbox_proceed_no_file_saving( $data, $box ) {
		?>
		<h2><?php _e( 'Proceed without saving a file', 'tablepress' ) ?></h2>
		<p>
			<?php _e( 'To proceed without trying to save the &quot;Custom CSS&quot; to a file, click the button below.', 'tablepress' ); ?>
			<?php _e( 'Your &quot;Custom CSS&quot; will then be loaded inline.', 'tablepress' ); ?>
		</p><p>
			<a href="<?php echo TablePress::url( array( 'action' => 'options', 'message' => 'success_save_error_custom_css' ) ); ?>" class="button-secondary"><?php _e( 'Proceed without saving &quot;Custom CSS&quot; to a file', 'tablepress' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Return the content for the help tab for this screen
	 *
	 * @since 1.0.0
	 */
	protected function help_tab_content() {
		return 'Help for the Plugin Options/Save Custom CSS Credentials Form screen';
	}

} // class TablePress_Options_Custom_CSS_View