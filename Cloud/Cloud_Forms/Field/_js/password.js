CloudField.on( 'init', function( $, $context ){
	var selector = '.field.type-password'; 
	var $fields = jQuery( selector, $context ).add( $context.filter( selector ) ); 
	
	$fields.each( function(){
		var $field = jQuery(this); 
	
		var $password = $field.find( 'input.password-field' ); 
		var $confirm = $field.find( 'input.password-confirm-field' ); 		
		$confirm.unbind('blur').on( 'blur', function(){
   			if ( $field.find( ':focus' ).size() > 0 ){
				return ;
			} 		
			$field.find('.input .error-message-special').slideUp(); 
			var confirm_value = $confirm.val(); 
			var password_value = $password.val(); 
			var $focused = jQuery(':focus');

			if ( confirm_value || password_value ){
				if ( ! confirm_value ){
					$field.find('.error-message-special[data-validation="confirm_empty"]').slideDown(); 
					$field.addClass('has-error');
				} else {
					if ( confirm_value !== password_value ){
						$field.find('.error-message-special[data-validation="confirm_error"]').slideDown(); 
						$field.addClass('has-error');
					}
				}
			}
		}); 
	}); 
});