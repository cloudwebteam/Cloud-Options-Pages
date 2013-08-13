jQuery( function($){    
    $('#scroll-nav ul li a').on('click', function(e) {
        e.preventDefault();
        target = this.hash;
        $.scrollTo(target, 300);
   });
   
   
	
   function handleFieldError( $input, validation_error ){
   		console.log( validation_error ); 
		var $field = $input.parents('.field' ).first(); 	
		if ( $field.find( ':focus' ).size() > 0 ){
			return ;
		}	
		for( index in validation_error ){
			var error_type = validation_error[index];
			console.log( error_type ); 
			$field.find( '.error-message[data-validation="'+error_type+'"]' ).show();
		} 
		$field.find( '.cloud-error').slideDown('fast'); 
		return;
   }
   $('.cloud-form').each( function(){
   		var $container = $(this);
   		var $form = $(this).find( 'form' ); 

   		var is_tabbed = false; 
   		if ( $container.hasClass('tabs-layout') || $container.hasClass( 'tabs_animated-layout' ) ){
   			is_tabbed = true; 
			var $tabs = $form.find( '.tabs li' ); 
			var $tabsContent = $form.find( '.tabs-content > div' )
			var $contentContainer = $form.find( '.tabs-content' ); 
			$tabsContent.hide(); 
			
			$tabs.find( 'a' ).click( function( e ){
				e.preventDefault(); 
				var $target = $tabsContent.filter( $(this).attr('href') ).first(); 
				if ( $target.size() > 0 ){
					if ( $container.hasClass( 'tabs_animated-layout' ) ){
						$contentContainer.css({ height: $contentContainer.height(), overflow: 'hidden' }); 
						var destinationHeight = $target.css('opacity', 0 ).show().outerHeight(); 
						$target.hide().css('opacity', 1 ); 
						
						$tabsContent.hide(); 
						$target.fadeIn( 500 ); 
						$contentContainer.animate({
							height: destinationHeight
						}, 500, function(){
							$contentContainer.css({ height: 'auto', overflow: 'visible' });
						}); 
						
					} else {
						$tabsContent.hide();
						$target.show(); 
					}
					$tabs.removeClass('active' ); 
					$(this).parent( 'li' ).addClass('active' ); 
					localStorage.setItem('lastTab', $(this).attr('href') );	   					
				}

			}); 

			//go to the latest tab, if it exists:
			var lastTab = typeof localStorage !== 'undefined' ? localStorage.getItem('lastTab') : false ;
			if (lastTab && $tabs.find('a[href="'+lastTab+'"]').size() > 0 ) {
   				$tabs.find('a[href="'+lastTab+'"]').parent('li').addClass('active'); 
   				$tabsContent.filter( lastTab ).show();
   			} else {
				$tabsContent.first().show(); 
   				$tabs.first().addClass('active' );    					   			
   			}   				
   			
   		}
   	
   		$container.find( 'input, textarea, select' ).on( 'focus', function(){
		   	var $field = $(this).parents( '.clone, .group, .field').first();
		   	$field.find( '.error-message' ).fadeOut('fast');
   		}); 
   		if ( $container.hasClass('ajax') ){
   		
		   	function validate_field( $input, callback ){
		   		var $field = $input.parents( '.clone, .group, .field').first();
		   		$field.removeClass('has-error' ); 
		   		$field.find( '.cloud-error').hide(); 
		   		$field.find( '.error-message').hide(); 
	   			var is_check_type = $input.is('[type="checkbox"], [type="radio"]') ; 		   		
			   	if ( $field.hasClass('required') ){
					if ( is_check_type ){
						if ( ! $input.is(':checked') ){
							$field.find( '.error-message[data-validation="required"]').show(); 
							$field.addClass('has-error'); 
							$field.find( '.cloud-error' ).slideDown(); 	 
							if ( typeof callback === 'function' ){
								callback( 'required' ); 
							}						
							return false;		   					
						}
					} else {
						if ( ! $input.val() || $input.val() === $input.data( 'placeholder' ) ){
							$field.find( '.error-message[data-validation="required"]').show(); 
							$field.addClass('has-error'); 
							$field.find( '.cloud-error' ).slideDown(); 
							if ( typeof callback === 'function' ){
								callback( 'required' ); 
							}							   		
							return false; 
						} 
					}
				} 
				var validation = $field.data('validate') ; 
				if ( validation ){
					var data = {
						action : 'input_validate',
						validation : validation, 
						value : $input.val()
					}					   						
			   		$.ajax({
				   		// cloud houses global variables like paths, etc
						url : cloud.cloud_ajax,
						data : data, 
						type : 'POST', 
						dataType : 'json', 
						success : function(response){   
							if ( response != 0 ){
								for ( i in response ){
									var validation_type = response[i]; 
									$field.find( '.error-message[data-validation="'+validation_type+'"]').show(); 
								}
								$field.addClass('has-error'); 
								$field.find( '.cloud-error' ).slideDown( 'fast' ); 
								
								if ( typeof callback === 'function' ){
									callback( validation ); 
								}										
							} else {
								if ( typeof callback === 'function' ){
									callback( true ); 
								}					
							}				
						}		
					}); 
				} else {
					if ( typeof callback === 'function' ){
						callback( true ); 
						return true; 
					}				
				}				  
			}		
   			var $fields = $form.find( '.field' ); 
   			setTimeout( function(){
	   			$fields.each( function(){
		   			var $input = $(this).find( '.input textarea, .input input, .input select' );  
		   			var is_check_type = $input.is('[type="checkbox"], [type="radio"]') ; 
		   			var action = $input.is('[type="checkbox"], [type="radio"]') ? 'change' : 'blur';
	
			   			$input.not( '.special-field' ).on( action, function(){
				   			// if the submit button was hit, then let the form do all the validation, otherwise validate it.
				   			if ( $form.find( '.submit input:focus').size() === 0 ){ 
			   					validate_field( $input ); 
			   				}
			   			}); 
	   			});
   			}, 100 ); 
   			var responses_recieved = []; 
   			
	   		$form.submit( function(e){
	   			$form.removeClass('has-error' ); 
	   			$form.find( '.has-error' ).removeClass('has-error' ); 
			   	$form.find( '.cloud-error' ).hide(); 
			   	$form.find( '.error-message' ).hide();	   		
		   		if ( $form.hasClass( 'validated' ) ){
		   			var success_function = $form.find( 'input[type="submit"]' ).data( 'on_success' ); 
		   			if ( success_function ){
			   			function_parts = success_function.split( '.' ); 
						if (typeof( window[ function_parts[0] ] ) !== 'undefined' ) {
							var global = window[ function_parts[0] ] ; 
							if ( typeof( global ) === 'function' ){
								global( $form );
							} else {
								if ( global.hasOwnProperty( function_parts[1] ) ){
									global[ function_parts[1] ]( $form );							
								}
							}
						} else {
							throw("Error.  Function " + success_function + " does not exist.");
						}
			   			e.preventDefault();  
			   			return false; 
			   		} else {
			   			return true; 
			   		}
			   	}
		   		responses_recieved = []; 
		   		e.stopPropagation(); 
			   	e.preventDefault();		   		

		   		var $fields = $form.find( '.field' ); 
		   		var inputs_to_validate = []; 
	   			$fields.each( function(){		   			
		   			var $input = $(this).find( '.input textarea, .input input, .input select' );
		   			if ( $input.size() > 0 ){
			   			inputs_to_validate.push( $input ); 
			   		}				   		
	   			});		   
	   			for( i = 0 ; i < inputs_to_validate.length ; i++ ){
	  		   		validate_field( inputs_to_validate[i], handle_validation_response ) ; 
	   			} 
	   			
		   		function handle_validation_response( response ){		   		
		   			responses_recieved.push( response ); 
		   			if (responses_recieved.length == inputs_to_validate.length ){
		   				attempt_submit(); 
		   			}
		   		}
		   		function attempt_submit(){
			   		var passed_validation = true ; 
	   				for( i in responses_recieved ){
	   					if ( responses_recieved[i] !== true ){
	   						passed_validation = false; 
	   					}
	   				}	
	   				if ( passed_validation ){
	   					$form.addClass('validated' ); 
	   					$form.submit(); 
	   					return true; 
	   				} else {
	   					
			   			var $form_sections = $form.find( '.section' ); 
		   						   			
		   				if ( $form_sections.size() > 0 ){
		   					$form_sections.each( function(){
		   						if ( $(this).find( '.has-error' ) ){
		   							$(this).addClass('has-error' ); 
		   							$form.addClass( 'has-error' ); 
		   						}
		   					}); 
		   				} else {
		   					if ( $form.find( '.has-error' ).size() ){
		   						$form.addClass( 'has-error' ); 
		   					}
		   				}
		   				$.scrollTo( $form.find( '.has-error' ) , 300 ) ; 
		   			}
		   		}
		   	}); 
	   	}
   }); // end .cloud-form.each()
     
}); 