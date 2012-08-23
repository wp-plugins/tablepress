/**
 * JavaScript code for the "Import" screen
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

jQuery(document).ready( function($) {

	/**
	 * Show select box for table to replace only if needed
	 *
	 * @since 1.0.0
	 */
	$( '#row-import-add_replace' ).on( 'change', 'input', function() {
		$( '#tables-import-replace-table' ).prop( 'disabled', 'replace' != $(this).val() );
	} )
	.find( 'input:checked' ).change();

	/**
	 * Show only the import source field that was selected with the radio button
	 *
	 * @since 1.0.0
	 */
	$( '#row-import-source' ).on( 'change', 'input', function() {
		$( '#row-import-source-file-upload, #row-import-source-url, #row-import-source-server, #row-import-source-form-field' ).hide();
		$( '#row-import-source-' + $(this).val() ).show();
	} )
	.find( 'input:checked' ).change();

	/**
	 * Select correct value in import format dropdown on file select
	 *
	 * @since 1.0.0
	 */
	$( '#tables-import-file-upload' ).on( 'change', set_import_format );
	$( '#tables-import-url, #tables-import-server' ).on( 'blur', set_import_format );
	function set_import_format() {
		var path = $(this).val(),
			filename_start,
			extension_start,
			filename = path,
			extension = '';
		// determine filename from full path
		filename_start = path.lastIndexOf( '\\' );
		if ( -1 != filename_start ) { // Windows-based path
			filename = path.substr( filename_start + 1 );
		} else {
			filename_start = path.lastIndexOf( '/' );
			if ( -1 != filename_start ) { // Windows-based path
				filename = path.substr( filename_start + 1 );
			}
		}
		// determine extension from filename
		extension_start = path.lastIndexOf( '.' );
		if ( -1 != extension_start )
			extension = path.substr( extension_start + 1 ).toLowerCase();

		$( '#tables-import-format' ).val( extension );
	}


	/**
	 * Check, whether inputs are valid
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).find( 'form' ).on( 'submit', function( /* event */ ) {
		var import_source = $( '#row-import-source' ).find( 'input:checked' ).val(),
			selected_import_source_field = $( '#tables-import-' + import_source ).get(0),
			valid_form = true;

		/* the value of the selected import source field must be set/changed from the default */
		if ( selected_import_source_field.defaultValue == selected_import_source_field.value ) {
			$( selected_import_source_field )
				.addClass( 'invalid' )
				.one( 'change', function() { $(this).removeClass( 'invalid' ); } )
				.focus().select();
			valid_form = false;
		}

		/* if replace is selected, a table must be selected */
		if ( 'replace' == $( '#row-import-add_replace' ).find( 'input:checked' ).val() ) {
			if ( '' == $( '#tables-import-replace-table' ).val() ) {
				$( '#row-import-add_replace' )
					.one( 'change', 'input', function() { $( '#tables-import-replace-table' ).removeClass( 'invalid' ); } );
				$( '#tables-import-replace-table' )
					.addClass( 'invalid' )
					.one( 'change', function() { $(this).removeClass( 'invalid' ); } )
					.focus().select();
				valid_form = false;
			}
		}

		if ( ! valid_form )
			return false;
		// at this point, the form is valid and will be submitted
	} );

} );