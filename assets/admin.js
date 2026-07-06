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

	var PREVIEW_WIDTH      = 260;
	var REFERENCE_WIDTHS   = {
		desktop: 1200,
		tablet: 768,
		mobile: 375
	};
	var PREVIEW_GROUPS     = [ 'desktop', 'tablet', 'mobile' ];

	function hexToRgb( hex ) {
		var value = ( hex || '' ).replace( '#', '' );

		if ( 3 === value.length ) {
			value = value.charAt( 0 ) + value.charAt( 0 ) + value.charAt( 1 ) + value.charAt( 1 ) + value.charAt( 2 ) + value.charAt( 2 );
		}

		if ( 6 !== value.length ) {
			value = 'ff0000';
		}

		return {
			r: parseInt( value.substring( 0, 2 ), 16 ),
			g: parseInt( value.substring( 2, 4 ), 16 ),
			b: parseInt( value.substring( 4, 6 ), 16 )
		};
	}

	function fieldValue( id, fallback ) {
		var field = document.getElementById( id );
		var parsed = field ? parseFloat( field.value ) : NaN;

		return isNaN( parsed ) ? fallback : parsed;
	}

	function updatePreview( group ) {
		var grid = document.querySelector( '[data-wpgo-preview="' + group + '"] .wpgo-preview__grid' );

		if ( ! grid ) {
			return;
		}

		var columns       = Math.max( 1, fieldValue( 'wpgo-' + group + '-columns', 1 ) );
		var gap           = Math.max( 0, fieldValue( 'wpgo-' + group + '-gap', 0 ) );
		var spacing       = Math.max( 0, fieldValue( 'wpgo-' + group + '-spacing', 0 ) );
		var referenceWidth = 'desktop' === group
			? fieldValue( 'wpgo-desktop-container_width', REFERENCE_WIDTHS.desktop )
			: REFERENCE_WIDTHS[ group ];
		var scale         = PREVIEW_WIDTH / Math.max( 1, referenceWidth );
		var colorField    = document.getElementById( 'wpgo-overlay-color' );
		var opacity       = fieldValue( 'wpgo-overlay-opacity', 30 ) / 100;
		var rgb           = hexToRgb( colorField ? colorField.value : '#ff0000' );

		grid.style.setProperty( '--wpgo-preview-columns', columns );
		grid.style.setProperty( '--wpgo-preview-gap', ( gap * scale ) + 'px' );
		grid.style.setProperty( '--wpgo-preview-spacing', ( spacing * scale ) + 'px' );
		grid.style.setProperty( '--wpgo-preview-fill', 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + opacity + ')' );
	}

	function updateAllPreviews() {
		PREVIEW_GROUPS.forEach( updatePreview );
	}

	document.addEventListener( 'input', function ( event ) {
		if ( ! event.target.matches( '.wpgo-settings input' ) ) {
			return;
		}

		updateAllPreviews();
	} );

	document.addEventListener( 'DOMContentLoaded', updateAllPreviews );
}() );
