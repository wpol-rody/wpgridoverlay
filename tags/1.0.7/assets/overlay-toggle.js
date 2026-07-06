( function () {
	var config = window.wpgoOverlayToggle;
	var item = document.getElementById( 'wp-admin-bar-wpgo-toggle-overlay' );
	var link = item ? item.querySelector( 'a' ) : null;
	var overlay = document.querySelector( '.wpgo-overlay' );
	var isBusy = false;

	if ( ! config || ! link || ! overlay || ! window.fetch || ! window.URLSearchParams ) {
		return;
	}

	function setToggleState( enabled, labels ) {
		overlay.classList.toggle( 'wpgo-overlay--hidden', ! enabled );

		if ( labels && labels.title ) {
			link.textContent = labels.title;
		}

		if ( labels && labels.metaTitle ) {
			link.setAttribute( 'title', labels.metaTitle );
		}
	}

	function setBusyState( busy ) {
		isBusy = busy;
		item.classList.toggle( 'wpgo-toggle-overlay--busy', busy );
	}

	link.addEventListener( 'click', function ( event ) {
		var body;

		event.preventDefault();

		if ( isBusy ) {
			return;
		}

		setBusyState( true );

		body = new window.URLSearchParams();
		body.append( 'action', 'wpgo_toggle_overlay' );
		body.append( 'nonce', config.nonce );

		window.fetch( config.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
			},
			body: body.toString(),
		} )
			.then( function ( response ) {
				return response.json().then( function ( data ) {
					if ( ! response.ok || ! data.success ) {
						throw new Error( 'Could not toggle the grid overlay.' );
					}

					return data.data;
				} );
			} )
			.then( function ( data ) {
				setToggleState( Boolean( data.enabled ), data.labels );
				setBusyState( false );
			} )
			.catch( function () {
				setBusyState( false );
			} );
	} );
}() );
