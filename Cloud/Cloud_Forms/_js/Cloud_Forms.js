jQuery( function($){    
    $('#scroll-nav ul li a').on('click', function(e) {
        e.preventDefault();
        target = this.hash;
        $.scrollTo(target, 300);
   });
   
   
	
   function handleFieldError( $input, validation_error ){
		$field = $input.parents('.field' ).first(); 		
		$error = $field.find( '.cloud-error' ).hide().html( '<span class="error-inner">' + validation_error + '</span>') ; 
		$field.addClass('has-error'); 
		$error.show('fast'); 		
	
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

   		$form.find( 'input, textarea, select' ).on( 'click focus', function(){
   			$field = $(this).parents('.field' ).removeClass('has-error' ); 
   			$field.find( '.cloud-error' ).hide('fast'); 
   		}); 
   		if ( $container.hasClass('ajax') ){
	   		$form.submit( function(e){
		   		if ( !$form.hasClass( 'validated' ) ){
			   		e.preventDefault(); 
			   		var data = {
			   			form_id : $form.data('id'), 
			   			form_data : $form.serialize(), 
			   			form_spec : $.parseJSON( $form.find( '#json_spec_'+ $form.data('id') ).html( ) )
			   		}; 	
			   		$.ajax({
				   		// cloud houses global variables like paths, etc
						url : cloud.cloud_ajax,
						data : data, 
						type : 'POST', 
						dataType : 'json', 
						success : function(response){
							$form.find( '.cloud-error' ).html( '' ).hide(); 
							$form.find( '.has-error').removeClass('has-error'); 
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
											var error = false; 
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
											if ( is_tabbed && error ){
												$tabs.filter( '.section-'+section_name + '-tab' ).addClass('has-error');
											}	
										}
																				
									// is a simple, one-section form
									} else {
										for ( field_name in spec ){
											var field = spec[field_name] ; 
											if ( field.validation_error ){		
												// single field			
												if ( $.type( field.validation_error ) === 'string' ){
													var $input = $form.find( '[name="'+field_name+'"]' );	
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
									$.scrollTo( $form, { duration : 500, axis : 'y' } ); 
									
								}
							}
						}
					}); // end $.ajax 
				}
			}); // end $form.submit()
	   	}
   }); // end .cloud-form.each()
     
}); 