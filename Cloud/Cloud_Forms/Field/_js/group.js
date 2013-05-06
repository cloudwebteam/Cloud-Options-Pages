jQuery( function($){

	var setup_remove_click = function( elems ){
		elems.unbind('click');
		elems.click( function(){
			if ( ! jQuery(this).hasClass('disabled') ){
				var group = jQuery(this).parents('.group'); 
				var container = jQuery(this).parents('.groups');
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
	var setup_reordering = function( multiple ){
		if ( multiple.parents('.no-sort').size() == 0 ){	
			multiple.each( function(){
				var group_container = $(this);
				var container_height = group_container.height();
				group_container.sortable({
					update: function(){
						reset_value_keys( multiple );
						//group_container.height( 'auto' );
						//container_height = group_container.height() ;
					},
					stop : function(){
						//group_container.height('auto');
					},
					start: function(){					
						//group_container.height( container_height ) ;	
					}				
				});
			});
		}
	};
	var setup_add_click = function( elems ){
		elems.unbind('click');
		elems.click( function(){
		
			var container = $(this).parents('.groups');	
			var counter = container.find('.group').size();
			var group = $(this).parents('.group');
			
			container.height( 'auto' );
			// copy an existing group 
			var new_input = group.clone(true).hide();
			//get rid of the values
			new_input.find('input').not('[type="button"], .copy').val('');
			// specific changes for specific fields
			new_input.find('.error').remove();			
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
			var group = $(this);		
			var inputs = $(this).find('input, textarea, select').not('[type="button"], .copy'); 
			//increment the inputs' name attributes so that it is saved as a unique value
			inputs.each( function(){
				var prev_name = $(this).attr('name');
				if ( prev_name !== undefined ){
					$(this).attr('name', prev_name.replace(/\[\d+\]/g, '['+counter+']' ) );
				}
				var prev_id = $(this).attr('id');
				if ( typeof( prev_id ) !== 'undefined' ){
					var new_id = prev_id.replace(/-\d+/g, '-'+counter ); 					
					$(this).attr('id', new_id  );
				
					group.find("label[for='"+prev_id+"']").attr('for', new_id  );
				}
			});
			// change the "code" link
			if ( $(this).find('input.copy').size() > 0 ){
				var prev_copy_to_use = $(this).find('input.copy').attr('value') ;
				$(this).find('input.copy').val( prev_copy_to_use.replace(/ \d+/g, ' ' + counter ) );
			}
			counter++;
		});
	}
	$('.groups').each( function(){
		if ( $(this).find('.group').size() == 1 ){
			$(this).find('.remove').addClass('disabled');
		} else {
			setup_remove_click( $(this).find('.remove') );
		}
		setup_reordering( $(this) );
	});	
	setup_add_click( $('.groups').find('.add') ) ;

});