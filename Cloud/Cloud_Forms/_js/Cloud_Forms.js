jQuery( function($){	
	
/*
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
   
   
*/
   function handleFieldError( $input, validation_error ){
		$field = $input.parents('.field' ); 		
		$error = $( '<span class="error">'+validation_error +'</span>' ).hide() ; 
		$input.after( $error ); 
		$field.addClass('has-error'); 
		$error.fadeIn('fast'); 		
	
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
					$form.find( '.error' ).remove(); 
					$form.find( '.field.has-error').removeClass('has-error'); 
					if ( response.success ){
						console.log( 'success' ); 
					} else {
						$.scrollTo( $form, { duration : 500, axis : 'y' } ); 
						if ( response.hasOwnProperty( 'updated_form_spec' ) ){
							console.log( response.updated_form_spec ); 
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
											// single field			
											if ( $.type( field.validation_error ) === 'string' ){
												var $input = $form.find( '[name="'+ section_name +'['+field_name+']"]' );	
												var error = field.validation_error ; 
												handleFieldError( $input, error ); 
												
											// cloneable field type
											} else {
												for ( clone_index in field.validation_error ){

													// simple clone
													if ( $.type( field.validation_error[ clone_index ] ) === 'string' ){
														var $input = $form.find( '[name="'+ section_name +'['+field_name+']['+clone_index+']"]' );	
														var error = field.validation_error[ clone_index ] ;		
														if ( error && typeof( error ) !== 'undefined' ) {								
															handleFieldError( $input, error ); 
														}
													// group clone
													} else {

														for ( subfield_slug in field.validation_error[ clone_index ] ){
															var $input = $form.find( '[name="'+ section_name +'['+field_name+']['+clone_index+']['+subfield_slug+']"]' );	
															var error = field.validation_error[ clone_index ][ subfield_slug ] ; 
															if ( error && typeof( error ) !== 'undefined' ) {																				
																handleFieldError( $input, error ); 
															}
														}														
													}
												}												
											}
										}
									}
								}
							// is a simple, one-section form
							} else {
								for ( field_name in spec ){
									var field = spec[field_name] ; 
									if ( field.validation_error ){		
										// single field			
										if ( $.type( field.validation_error ) === 'string' ){
											var $input = $form.find( '[name="['+field_name+']"]' );	
											var error = field.validation_error ; 
											handleFieldError( $input, error ); 
											// cloneable field type
										} else {
											for ( clone_index in field.validation_error ){
												// simple clone
												if ( $.type( field.validation_error[ clone_index ] ) === 'string' ){
													var $input = $form.find( '[name="'+field_name+'['+clone_index+']"]' );	
													var error = field.validation_error[ clone_index ] ;		
													if ( error && typeof( error ) !== 'undefined' ) {								
														handleFieldError( $input, error ); 
													}
												// group clone
												} else {
													for ( subfield_slug in field.validation_error[ clone_index ] ){
														var $input = $form.find( '[name="'+field_name+'['+clone_index+']['+subfield_slug+']"]' );	
														var error = field.validation_error[ clone_index ][ subfield_slug ] ; 
														if ( error && typeof( error ) !== 'undefined' ) {																				
															handleFieldError( $input, error ); 
														}
													}														
												}												
											}												
										}
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