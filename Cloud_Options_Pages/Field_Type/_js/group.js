jQuery( function($){

	var setup_remove_click = function( elems ){
		elems.unbind('click');
		elems.click( function(){
			if ( ! jQuery(this).hasClass('disabled') ){
				var group = jQuery(this).parents('.group'); 
				var container = jQuery(this).parents('.multiple');
				var counter = container.find('.group').size();
				
				group.slideUp('fast', function(){ 
					jQuery(this).remove();
					reset_value_keys(container );
					counter--;				
					if ( counter == 1 ){
						var groups = container.find('.group');
					
						groups.first().find('.remove').addClass('disabled');
					}							
				});
				
				
			}
		});
	}
	var setup_add_click = function( elems ){
		elems.unbind('click');
		elems.click( function(){
			var container = $(this).parents('.multiple'); 	
			var counter = container.find('.group').size();
			var group = $(this).parents('.group');
			
			// copy an existing group 
			var new_input = group.clone(true).hide();
			//get rid of the values
			new_input.find('input').not('[type="button"], .copy').val('');
			// specific changes for specific fields
			new_input.find('.media-url img').addClass('hidden');
			group.after( new_input );
			new_input.fadeIn(); 
			
			//update all the value keys
			reset_value_keys( container );
			
			container.find('.remove').removeClass('disabled');
		
			setup_remove_click( container.find('.remove') );
			setup_add_click( container.find('.add') );
		});
	};
	var reset_value_keys = function( container ){
		groups = container.find( '.group' );
		var counter = 0;
		groups.each( function(){
			var inputs = $(this).find('input, textarea').not('[type="button"], .copy'); 
			//increment the inputs' name attributes so that it is saved as a unique value
			inputs.each( function(){
				var prev_name = $(this).attr('name');
				if ( prev_name !== undefined ){
					$(this).attr('name', prev_name.replace(/\[\d\]/g, '['+counter+']' ) );
				}
			});
			// change the "code" link
			var prev_copy_to_use = $(this).find('input.copy').attr('value') ;			
			$(this).find('input.copy').attr('value', prev_copy_to_use.replace(/"\d"/g, '"'+counter+'"' ) );
						
			counter++;
		});
	}
	$('.multiple').each( function(){
		if ( $(this).find('.group').size() == 1 ){
			$(this).find('.remove').addClass('disabled');
		} else {
			setup_remove_click( $(this).find('.remove') );
		}
	});	
	setup_add_click( $('.multiple').find('.add') ) ;

});