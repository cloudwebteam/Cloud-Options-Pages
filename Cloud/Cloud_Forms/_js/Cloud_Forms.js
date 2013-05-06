jQuery( function($){	
	
	$('a[data-toggle="tab"]').on('shown', function (e) {
		//save the latest tab; use cookies if you like 'em better:
		localStorage.setItem('lastTab', $(this).attr('href') );
	});
	
	//go to the latest tab, if it exists:
	var lastTab = localStorage.getItem('lastTab');
	if (lastTab && $('#page-tabs a[href="'+lastTab+'"]').size() > 0 ) {
		$('#page-tabs a[href="'+lastTab+'"]').tab('show');
	} else { 
		$('#page-tabs a:first').tab('show');	
	}
	
    $('#scroll-nav').scrollspy();
        
    $('#scroll-nav ul li a').on('click', function(e) {
        e.preventDefault();
        target = this.hash;
        $.scrollTo(target, 300);
   });
   
   
   function handleFieldError( $input, validation_error ){
		$field = $input.parents('.field' ); 		
		
		if ( $.type( validation_error ) === 'string' ){
			$error = $( '<span class="error">'+validation_error +'</span>' ).hide() ; 
			$input.after( $error ); 
			$field.addClass('has-error'); 
			$error.fadeIn('fast'); 		
		} else {
			for ( index in validation_error ){
			}		
		}
		return;
   }
   
   $('.cloud-form.ajax form').submit( function(e){
   		var $form = $(this); 
   		if ( !$form.hasClass( 'validated' ) ){
	   		e.preventDefault(); 
	   		var data = {
	   			form_id : $form.data('id'), 
	   			form_data : $form.serialize(), 
	   			form_spec : $.parseJSON( $form.find( '#json_spec_'+ $form.data('id') ).html( ) )
	   		}; 	
	   		
	   		$.ajax({
		   		// cloud houses global variables like paths, etc
				url : cloud.ajax_url,
				data : data, 
				type : 'POST', 
				dataType : 'json', 
				success : function(response){
					console.log( response ); 
					$form.find( '.error' ).remove(); 
					$form.find( '.field.has-error').removeClass('has-error'); 
					if ( response.success ){
						console.log( 'success' ); 
					} else {
						if ( response.hasOwnProperty( 'updated_form_spec' ) ){
							var spec = response.updated_form_spec ; 
							for( i in spec ){
								if ( spec[i].hasOwnProperty( 'fields' ) ){
									var has_sections = true; 
								} 
								break; 
							}
							// has multiple sections							
							if ( has_sections ){
								for ( section_name in spec ){
									var section = spec[ section_name ] ; 
									for ( field_name in section.fields ){
										var field = section.fields[field_name] ; 
										if ( field.validation_error ){											
											if ( $.type( field.validation_error ) === 'string' ){
												$input = $form.find( '[name="'+ section_name +'['+field_name+']"]' );																							
											} else {
												console.log( 'array!' ); 
												for ( index in field.validation_error ){
													$input = $form.find( '[name="'+ section_name +'['+field_name+']['+index+']"]' );	
													handleFieldError( $input, field.validation_error[ index ] );																						
												}												
											}
											handleFieldError( $input, field.validation_error ); 
											
										}
									}
								}
							// is a simple, one-section form
							} else {
								for ( field_name in spec ){
									var field = spec[field_name] ; 
									if ( field.validation_error ){
										$input = $form.find( '[name="'+field_name+'"]' );
																			
										handleFieldError( $input, field.validation_error ); 
									}
								}							
							}
						}
					}
				}
			});
	   	}
   		
   }); 
     
}); 