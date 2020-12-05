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
				GreenletOptions.showTemporaryMessage( '.import-default' )
				return;
			}

			e.target.nextElementSibling.classList.add( 'is-active' );
			var nonce = e.target.nextElementSibling.nextElementSibling.value;

			var body = { action: 'booster_import_options', value: data, nonce: nonce }
			GreenletOptions.post( { url: glOptionsData.ajaxUrl, method: 'POST', body } ).then( res => {
				if ( 1 === res ) {
					GreenletOptions.showTemporaryMessage( '.import-success' )
				} else if ( 2 === res ) {
					GreenletOptions.showTemporaryMessage( '.import-warning' )
				} else {
					GreenletOptions.showTemporaryMessage( '.import-error' )
				}
			} ).catch( err => {
				GreenletOptions.showTemporaryMessage( '.import-error' )
			} ).finally( () => e.target.nextElementSibling.classList.remove( 'is-active' ) )
		} else if ( e.target && e.target.id === 'copy-export' ) {
			var textEl = document.getElementById( 'export-content' )
			textEl.select()
			var successful = false
			try {
				successful = document.execCommand( 'copy' )
			} catch (err) {}
			if ( successful ) GreenletOptions.showTemporaryMessage( '.copy-success', 2000 )
		}
	}
);
