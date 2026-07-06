( function () {
	document.addEventListener( 'input', function ( event ) {
		if ( ! event.target.matches( '.wpgo-range-control input[type="range"]' ) ) {
			return;
		}

		var output = event.target.parentNode.querySelector( 'output' );

		if ( output ) {
			output.value = event.target.value + '%';
		}
	} );
}() );
