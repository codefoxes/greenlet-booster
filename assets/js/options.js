/**
 * Scripts needed for options.
 *
 * @package greenlet\assets\js
 */

document.addEventListener(
	'click',
	function( e ) {
		if ( e.target && e.target.id === 'import-btn' ) {
			e.preventDefault();

			if ( ! window.confirm( 'Click OK to Import. Any saved theme settings will be lost!' ) ) {
				return;
			}

			var data = document.getElementById( 'import-content' ).value;

			if ( data === '' ) {
				showTemporaryMessage( 'import-default' )
				return;
			}

			e.target.nextElementSibling.classList.add( 'is-active' );
			var nonce = e.target.nextElementSibling.nextElementSibling.value;
			var args  = { action: 'booster_import_options', value: data, nonce: nonce };

			var xhr = new XMLHttpRequest();
			xhr.open( 'POST', ajaxurl, true );
			xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
			xhr.send( jsonToFormData( args ) );

			xhr.onload = function () {
				if ( xhr.readyState === 4 && xhr.status === 200 ) {
					if (xhr.responseText === '1') {
						showTemporaryMessage( 'import-success' )
					} else if (xhr.responseText === '2') {
						showTemporaryMessage( 'import-warning' )
					} else {
						showTemporaryMessage( 'import-error' )
					}
				}
				e.target.nextElementSibling.classList.remove( 'is-active' );
			}
		} else if ( e.target && e.target.id === 'save-btn' ) {
			e.preventDefault();
			e.target.nextElementSibling.classList.add( 'is-active' );
			var editorStyle = document.getElementById( 'editor_styles' )

			var bnonce = e.target.nextElementSibling.nextElementSibling.value;
			var bargs  = { action: 'booster_save_backend', settings: JSON.stringify( { editor_styles: editorStyle.checked } ), nonce: bnonce };
			var req    = xhRequest( { url: ajaxurl, body: jsonToFormData( bargs ), method: 'POST', headers: { 'Content-type': 'application/x-www-form-urlencoded' } } )
			req.then(
				function( res ) {
					if ( res === '0' ) {
						showTemporaryMessage( 'setting-error' )
					} else {
						showTemporaryMessage( 'setting-success' )
						document.querySelector( '.export-option textarea' ).value = res
					}
					e.target.nextElementSibling.classList.remove( 'is-active' )
				}
			);
		}
	}
);
