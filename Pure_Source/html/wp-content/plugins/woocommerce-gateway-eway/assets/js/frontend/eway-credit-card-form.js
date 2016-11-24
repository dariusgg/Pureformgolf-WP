jQuery( function( $ ) {

	$( 'input.wc-credit-card-form-card-number' ).payment( 'formatCardNumber' );
	$( 'input.wc-credit-card-form-card-expiry' ).payment( 'formatCardExpiry' );
	$( 'input.wc-credit-card-form-card-cvc' ).payment( 'formatCardCVC' );

	$( '#eway_credit_card_form' ).submit( function( event ) {

		// Clear validation classes
		$( '#EWAY_TEMPCARDNUMBER' ).parent().removeClass( 'validate-required' ).removeClass( 'woocommerce-invalid' );
		$( '#EWAY_EXPIRY' ).parent().removeClass( 'validate-required' ).removeClass( 'woocommerce-invalid' );
		$( '#EWAY_CARDCVN' ).parent().removeClass( 'validate-required' ).removeClass( 'woocommerce-invalid' );

		// Validation
		if ( ! $.payment.validateCardNumber( $( '#EWAY_TEMPCARDNUMBER' ).val() ) ) {
			$( '#EWAY_TEMPCARDNUMBER' ).parent().addClass( 'validate-required' ).addClass( 'woocommerce-invalid' );
			return false;
		}

		// Card Type
		if ( eway_settings.card_types !== "" ) {
			var card_type = $.payment.cardType( $( '#EWAY_TEMPCARDNUMBER' ).val() );
			var found_card_type = false;
			for ( var i = 0; i < eway_settings.card_types.length; i++ ) {
				if ( card_type == eway_settings.card_types[i] ) {
					found_card_type = true;
				}
			}
			if ( ! found_card_type ) {
				$( '#EWAY_TEMPCARDNUMBER' ).parent().addClass( 'validate-required' ).addClass( 'woocommerce-invalid' );
				return false;
			}
		}

		var expiry = $( '#EWAY_EXPIRY' ).payment( 'cardExpiryVal' );
		if ( ! $.payment.validateCardExpiry( expiry.month, expiry.year ) ) {
			$( '#EWAY_EXPIRY' ).parent().addClass( 'validate-required' ).addClass( 'woocommerce-invalid' );
			return false;
		}
		if ( ! $.payment.validateCardCVC( $( '#EWAY_CARDCVN' ).val() ) ) {
			$( '#EWAY_CARDCVN' ).parent().addClass( 'validate-required' ).addClass( 'woocommerce-invalid' );
			return false;
		}
		$( '#EWAY_CARDEXPIRYMONTH' ).val( expiry.month );
		$( '#EWAY_CARDEXPIRYYEAR' ).val( expiry.year );
		$( '#EWAY_CARDNUMBER' ).val( $( '#EWAY_TEMPCARDNUMBER' ).val().replace(/ /g,'') );

		// Disable the submit button after clicking and validation passes
		$( this ).find( 'input[type="submit"]' ).attr( 'disabled', 'disabled' );
		$( this ).css( { opacity: 0.5 } );
	});
});