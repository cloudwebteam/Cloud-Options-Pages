jQuery( function($){

// handle copy_to_use code link
   
   // popup useful code snippets
   $('a[rel="copy_to_use"]').click( function(e){
   		e.preventDefault();
   		$(this).siblings('.copy-container').show().find('input').select().blur( function(){
   			$(this).parents('.copy-container').hide();
   		});
   		
   }); 
   
 // handle cloneable fields
	var setup_remove_click = function( elems ){
		elems.unbind('click');
		elems.click( function(){
			if ( ! jQuery(this).hasClass('disabled') ){
				var clone = jQuery(this).parents('.clone'); 
				var container = jQuery(this).parents('.cloneable');
				var counter = container.find('.clone').size();
				
				clone.slideUp('fast', function(){ 
					jQuery(this).remove();
					reset_value_keys(container );
					counter--;				
					if ( counter == 1 ){
						var clones = container.find('.clone');
					
						clones.first().find('.remove').addClass('disabled');
					}							
				});
				
				
			}
		});
	}
	var setup_add_click = function( elems ){
		elems.unbind('click');
		elems.click( function(){
			var container = $(this).parents('.cloneable'); 	
			var counter = container.find('.clone').size();
			var clone = $(this).parents('.clone');
/* 			var clone_type = new_input.data('type') ; */
						
			// copy an existing clone 
			var new_input = clone.clone(true).hide();
			//get rid of the values
			new_input.find('input').not('[type="button"], .copy').val('');
			
			// specific changes for specific fields
			new_input.find('.media-url img').addClass('hidden');
			clone.after( new_input );
			new_input.fadeIn(); 

			//update all the value keys
			reset_value_keys( container );
			
			container.find('.remove').removeClass('disabled');
		
			setup_remove_click( container.find('.remove') );
			setup_add_click( container.find('.add') );
		});
	};
	var reset_value_keys = function( container ){
		clones = container.find( '.clone' );
		var counter = 0;
		clones.each( function(){
			var inputs = $(this).find('input, textarea').not('[type="button"], .copy'); 
			//increment the inputs' name attributes so that it is saved as a unique value
			inputs.each( function(){
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

	$('.cloneable').each( function(){
		if ( $(this).parents('.no-sort').size() == 0 ){	
			$(this).sortable({
				update: function(){
					reset_value_keys( $(this) );
				}	
			}); 
		}
		if ( $(this).find('.clone').size() == 1 ){
			$(this).find('.remove').addClass('disabled');
		} else {
			setup_remove_click( $(this).find('.remove') );
		}
	});	
	setup_add_click( $('.cloneable').find('.add') ) ;
	
	

		
});

