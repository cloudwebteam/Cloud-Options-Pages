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
							// has multiple sections
							for( i in spec ){
								if ( spec[i].hasOwnProperty( 'fields' ) ){
									var has_sections = true; 
								} 
								break; 
							}
							if ( has_sections ){
								for ( section_name in spec ){
									var section = spec[ section_name ] ; 
									for ( field_name in section.fields ){
										var field = section.fields[field_name] ; 
										if ( field.validation_error ){
											$input = $form.find( '[name="'+ section_name +'['+field_name+']"]' );
											$field = $input.parents('.field' ); 
											$error = $( '<span class="error">'+field.validation_error +'</span>' ).hide() ; 
											$input.after( $error ); 
											$field.addClass('has-error'); 
											$error.fadeIn('fast'); 
										}
									}
								}
							// is a simple, one-section form
							} else {
								for ( field_name in spec ){
									var field = spec[field_name] ; 
									if ( field.validation_error ){
										$input = $form.find( '[name="'+field_name+'"]' );
										$field = $input.parents('.field' ); 
										$error = $( '<span class="error">'+field.validation_error +'</span>' ).hide() ; 
										$input.after( $error ); 
										$field.addClass('has-error'); 
										$error.fadeIn('fast'); 
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