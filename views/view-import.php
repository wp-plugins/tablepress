<?php
/**
 * Import Table View
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Import Table View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Import_View extends TablePress_View {

	/**
	 * List of WP feature pointers for this view
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $wp_pointers = array( 'tp100_wp_table_reloaded_import' );

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

		$this->admin_page->enqueue_script( 'import', array( 'jquery' ) );

		$this->action_messages = array(
			'error_import' => __( 'Error: The import failed.', 'tablepress' ),
			'error_no_zip_import' => __( 'Error: Import of ZIP files is not available on this server.', 'tablepress' )
		);
		if ( $data['message'] && isset( $this->action_messages[ $data['message'] ] ) ) {
			$class = ( 'error' == substr( $data['message'], 0, 5 ) ) ? 'error' : 'updated';
			$this->add_header_message( "<strong>{$this->action_messages[ $data['message'] ]}</strong>", $class );
		}

		$this->add_text_box( 'head', array( &$this, 'textbox_head' ), 'normal' );
		$this->add_meta_box( 'import-form', __( 'Import Tables', 'tablepress' ), array( &$this, 'postbox_import_form' ), 'normal' );
		$this->add_meta_box( 'import-wp-table-reloaded', __( 'Import from WP-Table Reloaded', 'tablepress' ), array( &$this, 'postbox_wp_table_reloaded_import' ), 'additional' );
	}

	/**
	 * Print the screen head text
	 *
	 * @since 1.0.0
	 */
	public function textbox_head( $data, $box ) {
		?>
		<p>
			<?php _e( 'TablePress can import tables from existing data, like from a CSV file from a spreadsheet application (e.g. Excel), an HTML file resembling a webpage, or its own JSON format.', 'tablepress' ); ?>
			<?php _e( 'You can also import existing tables from the WP-Table Reloaded plugin below.', 'tablepress' ); ?>
		</p>
		<p>
			<?php _e( 'To import a table, select and enter the import source in the following form.', 'tablepress' ); ?> <?php if ( 0 < $data['tables_count'] ) _e( 'You can also choose to import it as a new table or to replace an existing table.', 'tablepress' ); ?>
		</p>
		<?php
	}

	/**
	 * Print the content of the "Import Tables" post meta box
	 *
	 * @since 1.0.0
	 */
	public function postbox_import_form( $data, $box ) {
?>
<table class="tablepress-postbox-table fixed">
<tbody>
	<tr id="row-import-source">
		<th class="column-1" scope="row"><?php _e( 'Import Source', 'tablepress' ); ?>:</th>
		<td class="column-2">
			<input name="import[source]" id="tables-import-source-file-upload" type="radio" value="file-upload"<?php checked( $data['import_source'], 'file-upload', true ); ?> /> <label for="tables-import-source-file-upload"><?php _e( 'File Upload', 'tablepress' ); ?></label>
			<input name="import[source]" id="tables-import-source-url" type="radio" value="url"<?php checked( $data['import_source'], 'url', true ); ?> /> <label for="tables-import-source-url"><?php _e( 'URL', 'tablepress' ); ?></label>
			<input name="import[source]" id="tables-import-source-server" type="radio" value="server"<?php checked( $data['import_source'], 'server', true ); ?> /> <label for="tables-import-source-server"><?php _e( 'File on server', 'tablepress' ); ?></label>
			<input name="import[source]" id="tables-import-source-form-field" type="radio" value="form-field"<?php checked( $data['import_source'], 'form-field', true ); ?> /> <label for="tables-import-source-form-field"><?php _e( 'Manual Input', 'tablepress' ); ?></label>
		</td>
	</tr>
	<tr id="row-import-source-file-upload" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-file-upload"><?php _e( 'Select file', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<input name="import_file_upload" id="tables-import-file-upload" type="file" class="large-text" style="box-sizing: border-box;" />
			<?php
				if ( $data['zip_support_available'] )
					echo '<br /><span class="description">' . __( 'You can import multiple tables by placing them in a ZIP file.', 'tablepress' ) . '</span>';
			?>
		</td>
	</tr>
	<tr id="row-import-source-url" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-url"><?php _e( 'File URL', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<input type="text" name="import[url]" id="tables-import-url" class="large-text" value="<?php echo $data['import_url']; ?>" />
			<?php
				if ( $data['zip_support_available'] )
					echo '<br /><span class="description">' . __( 'You can import multiple tables by placing them in a ZIP file.', 'tablepress' ) . '</span>';
			?>
		</td>
	</tr>
	<tr id="row-import-source-server" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-server"><?php _e( 'Server Path to file', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<input type="text" name="import[server]" id="tables-import-server" class="large-text" value="<?php echo $data['import_server']; ?>" />
			<?php
				if ( $data['zip_support_available'] )
					echo '<br /><span class="description">' . __( 'You can import multiple tables by placing them in a ZIP file.', 'tablepress' ) . '</span>';
			?>
		</td>
	</tr>
	<tr id="row-import-source-form-field" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-form-field"><?php _e( 'Import data', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<textarea name="import[form_field]" id="tables-import-form-field" rows="15" cols="40" class="large-text"><?php echo $data['import_form_field']; ?></textarea>
		</td>
	</tr>
	<tr class="top-border bottom-border">
		<th class="column-1" scope="row"><label for="tables-import-format"><?php _e( 'Import Format', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<select id="tables-import-format" name="import[format]">
			<?php
				foreach ( $data['import_formats'] as $format => $name ) {
					$selected = selected( $format, $data['import_format'], false );
					echo "<option{$selected} value=\"{$format}\">{$name}</option>";
				}
			?>
			</select>
			<?php
				if ( ! $data['html_import_support_available'] )
					echo '<br /><span class="description">' . __( 'Import of HTML files is not available on your server.', 'tablepress' ) . '</span>';
			?>
		</td>
	</tr>
	<tr id="row-import-add_replace" class="top-border">
		<th class="column-1" scope="row"><?php _e( 'Add or Replace?', 'tablepress' ); ?>:</th>
		<td class="column-2">
			<input name="import[add_replace]" id="tables-import-add_replace-add" type="radio" value="add"<?php checked( $data['import_add_replace'], 'add', true ); ?> /> <label for="tables-import-add_replace-add"><?php _e( 'Add as new table', 'tablepress' ); ?></label>
			<input name="import[add_replace]" id="tables-import-add_replace-replace" type="radio" value="replace"<?php checked( $data['import_add_replace'], 'replace', true ); ?><?php disabled( $data['tables_count'] > 0, false, true ); ?> /> <label for="tables-import-add_replace-replace"><?php _e( 'Replace existing table', 'tablepress' ); ?></label>
		</td>
	</tr>
	<tr id="row-import-replace-table" class="bottom-border">
		<th class="column-1" scope="row"><label for="tables-import-replace-table"><?php _e( 'Table to replace', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<select id="tables-import-replace-table" name="import[replace_table]"<?php disabled( $data['tables_count'] > 0, false, true ); ?>>
				<option value=""><?php _e( 'Select:' ); ?></option>
			<?php
				foreach ( $data['tables'] as $table ) {
					if ( '' == trim( $table['name'] ) )
						$table['name'] = __( '(no name)', 'tablepress' );
					$text = esc_html( sprintf( __( 'ID %1$s: %2$s', 'tablepress' ), $table['id'], $table['name'] ) );
					$selected = selected( $table['id'], $data['import_replace_table'], false );
					echo "<option{$selected} value=\"{$table['id']}\">{$text}</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr class="top-border">
		<th class="column-1" scope="row"></th>
		<td class="column-2"><input type="submit" value="<?php echo esc_attr__( 'Import', 'tablepress' ); ?>" class="button button-primary button-large" name="submit" /></td>
	</tr>
</tbody>
</table>
<?php
	}

	/**
	 * Print the content of the "Import from WP-Table Reloaded" post meta box
	 *
	 * @since 1.0.0
	 */
	public function postbox_wp_table_reloaded_import( $data, $box ) {
		?>
<p>
	<?php _e( 'To import all tables from a WP-Table Reloaded installation, choose the relevant import source below.', 'tablepress' ); ?>
<br />
	<?php _e( 'If WP-Table Reloaded is installed on this site, the "WordPress database" option is recommended.', 'tablepress' ); ?>
	<?php _e( 'If you want to import tables from another site, create a "WP-Table Reloaded Dump File" there and upload it below, after choosing "WP-Table Reloaded Dump File".', 'tablepress' ); ?>
<br />
	<?php printf( __( 'Before doing this, it is highly recommended to read the <a href="%s" title"Migration Guide from WP-Table Reloaded to TablePress">migration guide</a> on the TablePress website.', 'tablepress' ), 'http://tablepress.org/migration-from-wp-table-reloaded/' ); ?>
</p>
<table class="tablepress-postbox-table fixed">
<tbody>
	<tr id="row-import-wp-table-reloaded-source">
		<th class="column-1" scope="row"><?php _e( 'Import Source', 'tablepress' ); ?>:</th>
		<td class="column-2">
			<input name="import[wp_table_reloaded][source]" id="import-wp-table-reloaded-source-db" type="radio" value="db"<?php checked( $data['import_wp_table_reloaded_source'], 'db', true ); disabled( $data['wp_table_reloaded_installed'], false, true ); ?> /> <label for="import-wp-table-reloaded-source-db"><?php _e( 'WordPress database', 'tablepress' ); ?></label>
			<input name="import[wp_table_reloaded][source]" id="import-wp-table-reloaded-source-dump-file" type="radio" value="dump-file"<?php checked( $data['import_wp_table_reloaded_source'], 'dump-file', true ); ?> /> <label for="import-wp-table-reloaded-source-dump-file"><?php _e( 'WP-Table Reloaded Dump File', 'tablepress' ); ?></label>
		</td>
	</tr>
	<tr id="row-import-wp-table-reloaded-source-dump-file" class="bottom-border">
		<th class="column-1 top-align" scope="row"><label for="tables-import-wp-table-reloaded-dump-file"><?php _e( 'Select file', 'tablepress' ); ?>:</label></th>
		<td class="column-2">
			<input name="import_wp_table_reloaded_file_upload" id="tables-import-wp-table-reloaded-dump-file" type="file" class="large-text" style="box-sizing: border-box;" />
		</td>
	</tr>
	<tr id="row-import-wp-table-reloaded-source-db" class="bottom-border">
		<th class="column-1 top-align" scope="row" style="padding:2px;"></th>
		<td class="column-2" style="padding:2px;"></td>
	</tr>
	<tr class="top-border">
		<th class="column-1" scope="row"><?php _e( 'Import tables', 'tablepress' ); ?>:</th>
		<td class="column-2"><label for="import-wp-table-reloaded-tables"> <input type="checkbox" id="import-wp-table-reloaded-tables" name="import[wp_table_reloaded][tables]" value="true" checked="checked" /> <?php _e( 'Import all tables and their settings from WP-Table Reloaded.', 'tablepress' ); ?> <?php _e( '<span class="description">(recommended)</span>', 'tablepress' ); ?></label></td>
	</tr>
	<tr class="bottom-border">
		<th class="column-1" scope="row"><?php _e( 'Import styling', 'tablepress' ); ?>:</th>
		<td class="column-2"><label for="import-wp-table-reloaded-css"> <input type="checkbox" id="import-wp-table-reloaded-css" name="import[wp_table_reloaded][css]" value="true" checked="checked" /> <?php _e( 'Try to automatically convert the "Custom CSS" code from the "Plugin Options" screen of WP-Table Reloaded.', 'tablepress' ); ?></label></td>
	</tr>
	<tr class="top-border">
		<th class="column-1" scope="row"></th>
		<td class="column-2"><input type="submit" value="<?php echo esc_attr__( 'Import from WP-Table Reloaded', 'tablepress' ); ?>" class="button button-large" id="submit_wp_table_reloaded_import" name="submit_wp_table_reloaded_import" /></td>
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
		return 'Help for the Import Table screen';
	}

	/**
	 * Set the content for the WP feature pointer about the WP-Table Reloaded import feature
	 *
	 * @since 1.0.0
	 */
	public function wp_pointer_tp100_wp_table_reloaded_import() {
		$content  = '<h3>' . __( 'TablePress Feature: Import from WP-Table Reloaded', 'tablepress' ) . '</h3>';
		$content .= '<p>' .	 __( 'You can import your existing tables and "Custom CSS" from WP-Table Reloaded into TablePress.', 'tablepress' ) . '</p>';

		$this->admin_page->print_wp_pointer_js( 'tp100_wp_table_reloaded_import', '#tablepress_import-import-wp-table-reloaded', array(
			'content'  => $content,
			'position' => array( 'edge' => 'bottom', 'align' => 'left', 'offset' => '16 -16' ),
		) );
	}

} // class TablePress_Import_View