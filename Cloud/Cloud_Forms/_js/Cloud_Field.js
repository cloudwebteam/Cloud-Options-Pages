jQuery( function($){

// handle copy_to_use code link
   
   // popup useful code snippets
   $('.copy_to_use input').click( function(e){
   		e.preventDefault();
   		var $input = $(this); 
   		var $container = $input.parent(); 
   		$input.select();
   		$container.addClass('active'); 
   		
   });    
   $('.copy_to_use input').blur( function(e){
   		var $input = $(this); 
   		var $container = $input.parent(); 
   		$container.removeClass('active');
   	});   
   

	$('.cloneable').each( function(){
		var $cloneable = $(this); 
		var $to_clone = $cloneable.find( '.to-clone' ).clone( true, true ).removeClass('to-clone'); 	
		$cloneable.find( '.to-clone' ).remove(); 		
		
		var $clones = $cloneable.find( '.clone' ); 
		var $add_buttons = $cloneable.find( '.add' );
		var $remove_buttons = $cloneable.find( '.remove' ); 
		
		var min_size = typeof $cloneable.data('min') !== 'undefined' ? $cloneable.data('min') : 1; 
		var max_size = typeof $cloneable.data('max') !== 'undefined' ? $cloneable.data('max') : 1; 
		
		function reset_value_keys(){
			var $clones = $cloneable.find( '.clone' );
			var counter = 0;
			$clones.each( function(){
				var $inputs = $(this).find('input, textarea').not('[type="button"], .copy'); 
				//increment the inputs' name attributes so that it is saved as a unique value
				$inputs.each( function(){
					var prev_name = $(this).attr('name');
					if ( prev_name !== undefined ){
						$(this).attr('name', prev_name.replace(/\[\d+\]/g, '['+counter+']' ) );
					}
				});
				// change the "code" link
				if ( $(this).find('input.copy').size() > 0 ){
					var prev_copy_to_use = $(this).find('input.copy').attr('value') ;			
					$(this).find('input.copy').val( prev_copy_to_use.replace(/ \d+/g, ' ' + counter ) );
				}
				counter++;
				
				// change the clone number
				$(this).find('.number').text( counter ); 
			});
		}		
		function add_clone( $button ){
			if ( ! $button.hasClass('disabled') ){				
				var counter = $clones.size();
				var $parent_clone = $button.parents('.clone'); 
				// copy an existing clone 
				var $new_clone = $to_clone.clone(true, true).hide();
				//get rid of the values
				$new_clone.find('input, textarea').not('[type="button"],[type="checkbox"][type="radio"], .copy').val('');
				
				// get rid of error messages 
				$new_clone.find( '.error' ).remove(); 
				// specific changes for specific fields
				$new_clone.find('.preview-image').attr('src', '').addClass('hidden' ).hide(); 
				
				if ( $parent_clone.size() > 0 ){
					$new_clone.insertAfter( $parent_clone ).fadeIn(); 
				} else {
					$new_clone.prependTo( $cloneable ).fadeIn(); 				
				}
				
				update_cloneable(); 
			}
		}
		function remove_clone( $button ){
			if ( ! $button.hasClass('disabled') ){
				var $clone = $button.parents('.clone'); 
				var counter = $clones.size();
				
				$clone.slideUp('fast', function(){ 
					$clone.remove();
					
					reset_value_keys();
					
					update_cloneable(); 
				});
			}		
		}
		function update_cloneable(){
			//update all the value keys
			reset_value_keys( );
		
			$remove_buttons = $cloneable.find( '.remove' ); 
			$add_buttons = $cloneable.find('.add' );
			$clones = $cloneable.find( '.clone' ); 
									
			if ( $clones.size() == min_size ){
				$remove_buttons.addClass('disabled' ); 
			} else {
				$remove_buttons.removeClass('disabled' ); 						
			}
			if ( $clones.size() == max_size ){
				$add_buttons.addClass('disabled' ); 
			} else {
				$add_buttons.removeClass('disabled' ); 			
			}
			
			$add_buttons.unbind('click.cloneable').on( 'click.cloneable' , function ( ){
				add_clone( $(this) );
			});			
			$remove_buttons.unbind('click.cloneable').on( 'click.cloneable' , function ( ){
				remove_clone( $(this) ); 
			}); 
						
		}
	
		
		if ( $cloneable.parents('.no-sort').size() == 0 ){	
			$cloneable.sortable({
				update: reset_value_keys	
			}); 
		}		

		update_cloneable(); 		
		
	});		
	

		
});

