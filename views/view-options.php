<?php
/**
 * Plugin Options View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Plugin Options View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Options_View extends TablePress_View {

	/**
	 * Set up the view with data and do things that are specific for this view
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action for this view
	 * @param array $data Data for this view
	 */
	public function setup( $action, $data ) {
		parent::setup( $action, $data );

		$this->admin_page->enqueue_style( 'codemirror' );
		$this->admin_page->enqueue_script( 'codemirror' );
		$this->admin_page->enqueue_script( 'codemirror-css', array( 'tablepress-codemirror' ) );
		add_action( "admin_footer-{$GLOBALS['hook_suffix']}", array( &$this, 'print_codemirror_js' ) );

		$action_messages = array(
			'success_save' => __( 'Options saved successfully.', 'tablepress' ),
			'success_save_error_custom_css' => __( 'Options saved successfully, but &quot;Custom CSS&quot; was not saved to file.', 'tablepress' ),
			'error_save' => __( 'Error: Options could not be saved.', 'tablepress' )
		);
		if ( $data['message'] && isset( $action_messages[ $data['message'] ] ) ) {
			$class = ( 'error' == substr( $data['message'], 0, 5 ) ) ? 'error' : 'updated';
			$this->add_header_message( "<strong>{$action_messages[ $data['message'] ]}</strong>", $class );
		}

		$this->add_text_box( 'head', array( &$this, 'textbox_head' ), 'normal' );
		$this->add_meta_box( 'frontend-options', __( 'Frontend Options', 'tablepress' ), array( &$this, 'postbox_frontend_options' ), 'normal' );
		$this->add_meta_box( 'backend-options', __( 'Backend Options', 'tablepress' ), array( &$this, 'postbox_backend_options' ), 'normal' );
		$this->add_meta_box( 'user-options', __( 'User Options', 'tablepress' ), array( &$this, 'postbox_user_options' ), 'normal' );
		$this->data['submit_button_caption'] = __( 'Save Options', 'tablepress' );
		$this->add_text_box( 'submit', array( &$this, 'textbox_submit_button' ), 'submit' );
	}

	/**
	 * Print the JavaScript code to invoke CodeMirror on the "Custom CSS" textarea (in the admin footer)
	 *
	 * @since 1.0.0
	 */
	public function print_codemirror_js() {
		echo <<<JS
<script type="text/javascript">
CodeMirror.fromTextArea( document.getElementById( 'option-custom-css' ), {
	mode: 'css',
	indentUnit: 2,
	tabSize: 2,
	indentWithTabs: true
} );
</script>
JS;
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function textbox_head( $data, $box ) {
		?>
		<p><?php _e( 'TablePress has several options which affect the plugin behavior in different areas.', 'tablepress' ); ?><br /><?php _e( 'Frontend Options influence the output and used features of tables in pages, posts or text-widgets.', 'tablepress' ); ?> <?php printf( __( 'The Backend Options control the plugin\'s admin area, e.g. the &quot;%s&quot; screen.', 'tablepress' ), __( 'Edit Table', 'tablepress' ) ); ?> <?php _e( 'Administrators have access to further Admin Options.', 'tablepress' ); ?></p>
		<?php
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function postbox_frontend_options( $data, $box ) {
?>
<table class="tablepress-postbox-table fixed">
<tbody>
	<tr>
		<th class="column-1" scope="row"><label for="option-use-custom-css-file"><?php _e( 'Custom CSS file', 'tablepress' ); ?>:</label></th>
		<td class="column-2"><input type="checkbox" id="option-use-custom-css-file" name="options[use_custom_css_file]" value="true"<?php checked( $data['frontend_options']['use_custom_css_file'] ); ?> />
		<?php
			echo content_url( 'tablepress-custom.css' );
			echo ' ';
			echo ( $data['frontend_options']['custom_css_file_exists'] ) ? '(File exists.)' : '(File seems not to exist.)';
		?>
		</td>
	</tr>
	<tr>
		<th class="column-1 top-align" scope="row"><label for="option-custom-css"><?php _e( 'Custom CSS', 'tablepress' ); ?>:</label></th>
		<td class="column-2"><textarea name="options[custom_css]" id="option-custom-css" class="large-text" rows="8"><?php echo esc_textarea( $data['frontend_options']['custom_css'] ); ?></textarea></td>
	</tr>
</tbody>
</table>
<?php
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 */
	public function postbox_backend_options( $data, $box ) {
?>
<table class="tablepress-postbox-table fixed">
<tbody>
	<tr>
		<th class="column-1" scope="row">Label</th>
		<td class="column-2">Field</td>
	</tr>
</tbody>
</table>
<?php
	}

	/**
	 * Render a form for user options
	 *
	 * @since 1.0.0
	 */
	public function postbox_user_options( $data, $box ) {
		?>
<table class="tablepress-postbox-table fixed">
<tbody>
		<?php
		// get list of current admin menu entries
		$entries = array();
		foreach ( $GLOBALS['menu'] as $entry ) {
			if ( false !== strpos( $entry[2], '.php' ) )
				$entries[ $entry[2] ] = $entry[0];
		}

		// remove <span> elements with notification bubbles (e.g. update or comment count)
		if ( isset( $entries['plugins.php'] ) )
			$entries['plugins.php'] = preg_replace( '/ <span.*span>/', '', $entries['plugins.php'] );
		if ( isset( $entries['edit-comments.php'] ) )
			$entries['edit-comments.php'] = preg_replace( '/ <span.*span>/', '', $entries['edit-comments.php'] );

		// add separator and generic positions
		$entries['-'] = __( '---', 'tablepress' );
		$entries['top'] = __( 'Top-Level (top)', 'tablepress' );
		$entries['middle'] = __( 'Top-Level (middle)', 'tablepress' );
		$entries['bottom'] = __( 'Top-Level (bottom)', 'tablepress' );

		$select_box = '<select id="option-admin-menu-parent-page" name="options[admin_menu_parent_page]">' . "\n";
		foreach ( $entries as $page => $entry ) {
			$select_box .= '<option' . selected( $page, $data['user_options']['parent_page'], false ) . disabled( $page, '-', false ) .' value="' . $page . '">' . $entry . "</option>\n";
		}
		$select_box .= "</select>\n";
		?>
	<tr class="bottom-border">
		<th class="column-1" scope="row"><label for="option-admin-menu-parent-page"><?php _e( 'Admin menu entry', 'tablepress' ); ?>:</label></th>
		<td class="column-2"><?php printf( __( 'TablePress shall be shown in this section of the admin menu: %s', 'tablepress' ), $select_box ); ?></td>
	</tr>
		<?php
		$select_box = '<select id="option-plugin-language" name="options[plugin_language]">' . "\n";
		$select_box .= '<option' . selected( $data['user_options']['plugin_language'], 'auto', false ) . ' value="auto">' . sprintf( __( 'WordPress Default (currently %s)', 'tablepress' ), get_locale() ) . "</option>\n";
		$select_box .= '<option value="-" disabled="disabled">---</option>' . "\n";
		foreach ( $data['user_options']['plugin_languages'] as $lang_abbr => $language ) {
			$select_box .= '<option' . selected( $data['user_options']['plugin_language'], $lang_abbr, false ) . ' value="' . $lang_abbr . '">' . "{$language['name']} ({$lang_abbr})</option>\n";
		}
		$select_box .= "</select>\n";
		?>
	<tr class="top-border">
		<th class="column-1" scope="row"><label for="option-plugin-language"><?php _e( 'Plugin Language', 'tablepress' ); ?>:</label></th>
		<td class="column-2"><?php printf( __( 'TablePress shall be shown in this language: %s', 'tablepress' ), $select_box ); ?></td>
	</tr>
</tbody>
</table>
<?php
	}

	/**
	 * Return the content for the help tab for this screen
	 *
	 * @since 1.0.0
	 */
	protected function help_tab_content() {
		return 'Help for the Plugin Options screen';
	}

} // class TablePress_Options_View