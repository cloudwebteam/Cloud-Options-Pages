jQuery( function($){
	$('.field.type-password').each( function(){
		var $field = $(this); 
	
		var $password = $field.find( 'input.password-field' ); 
		var $confirm = $field.find( 'input.password-confirm-field' ); 		
		$confirm.blur( function(){
			var confirm_value = $confirm.val(); 
			var password_value = $password.val(); 
			if ( confirm_value || password_value ){
				if ( confirm_value !== password_value ){
					$confirm.siblings('.cloud-error').slideDown('fast'); 
					$field.addClass('has-error');
				} else {
					$field.removeClass('has-error');					
				}
			}
		}); 
	}); 
});