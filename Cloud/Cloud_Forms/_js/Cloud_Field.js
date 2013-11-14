var CloudField = ( function($){
	
	var actions = {
		init : []
	}
	function on_action( hook_name, fnct, args ){
		if ( typeof args == 'undefined' ){
			args = false; 
		}
		if ( actions.hasOwnProperty( hook_name ) ){
			actions[ hook_name ].push({ fnct_name:  fnct, args : args }); 
		} else {
			console.warn( 'CloudField has no action ' + hook_name ); 
		}
	} 
	function trigger( hook_name, args ){
		if ( actions.hasOwnProperty( hook_name ) ){
			for( i in actions[ hook_name ] ){
				actions[ hook_name ][i].fnct_name( $, args, actions[ hook_name ][i].args ); 
			}
		}
	}
	
	
	var setup_cloneables = function( jQuery, $context ){
		$('.cloneable', $context ).each( function(){
		
			if ( $(this).parents('.to-clone').size() > 0 ){
				return false; 
			}
			// has the thing already been initialized? then stop
			if ( $(this).data( 'cloneable' ) == true ){
				return false; 
			}
			$(this).data( 'cloneable', true );
			var $cloneable = $(this); 
			var $clones, $add_buttons, $remove_buttons, $to_clone ; 
								
			var min_size = typeof $cloneable.data('min') !== 'undefined' ? $cloneable.data('min') : 0; 
			var max_size = typeof $cloneable.data('max') !== 'undefined' ? $cloneable.data('max') : false; 
			
			
			$to_clone = $cloneable.getClonePart( '.to-clone' ).clone( false ).removeClass('to-clone');
			$cloneable.getClonePart( '.to-clone' ).remove(); 	
						
			function reset_value_keys(){
				$clones = $cloneable.getClonePart( '.clone' ); 
				$add_buttons = $cloneable.getClonePart( '.add' );
				$remove_buttons = $cloneable.getClonePart( '.remove' ); 
								
				var counter = 0;
				
				$clones.each( function(){ 
					var $clone = $(this);
					var $inputs = $clone.find( 'input, textarea, select' ).not('[type="button"], .copy_to_use input'); 
					//increment the inputs' name attributes so that it is saved as a unique value					
					$inputs.each( function(){
						var $parentField = $(this).parents( '.field' );
						var prev_name = $(this).attr('name');
						
						if ( prev_name !== undefined ){
							var is_grandparent_clone = false; 
							if ( $(this).parents( '.clone' ).size() == 2 ){
								if ( $(this).parents( '.clone' ).first()[0] !== $clone[0] ){
									is_grandparent_clone = true; 
								}
							}
							if ( is_grandparent_clone ){
								$(this).attr('name', replaceNthMatch( $(this).attr('name' ), /(\[\d+\])/g, 'first', '['+ counter+']' ) ); 
							} else {
								$(this).attr('name', replaceNthMatch( $(this).attr('name' ), /(\[\d+\])/g, 'last', '['+ counter+']' ) ); 							
							}
						}
						var prev_id = $(this).attr('id');
						if ( typeof( prev_id ) !== 'undefined' ){

							var new_id = prev_id.replace(/(-\d+)/g, '-'+counter ); 					
							$(this).attr('id', new_id  );
		
							$clone.getClonePart("label[for='"+prev_id+"']").attr('for', new_id  );
						}
					});
					// change the "code" link
					var $copy_to_use = $(this).getClonePart('.copy_to_use').find( 'input' );
					$copy_to_use.each( function(){
						var is_grandparent_clone = false; 
						if ( $(this).parents( '.clone' ).size() == 2 ){
							if ( $(this).parents( '.clone' ).first()[0] !== $clone[0] ){
								is_grandparent_clone = true; 
							}
						}
						var prev_copy_to_use = $(this).attr('value') ;		
						if ( is_grandparent_clone ){
							$(this).val( replaceNthMatch( prev_copy_to_use, /( \d+)/g, 'first', ' ' + counter ));
						} else {
							$(this).val( replaceNthMatch( prev_copy_to_use, /( \d+)/g, 'last', ' ' + counter ));
						}					

					});
					counter++;
					
					// change the clone number
					
					$clone.getClonePart('.number').text( counter ); 
				});
							
			}		
			function add_clone( $button ){
				if ( ! $button.hasClass('disabled') ){				
					var counter = $clones.size();
					if ( $button.parents( '.no-clones' ).size() > 0 ){
						var $parent_clone = $(); 
					} else {
						var $parent_clone = $button.parents('.clone').first();  
					}
					// copy an existing clone 
					var $new_clone = $to_clone.clone( false ).hide();
					//get rid of the values
					$new_clone.getClonePart('input, textarea, select').not('[type="button"],[type="checkbox"][type="radio"], .copy_to_use input').val('');
					
					// get rid of error messages 
					$new_clone.getClonePart( '.error' ).remove(); 
					// specific changes for specific fields
					$new_clone.getClonePart('.preview-image').attr('src', '').addClass('hidden' ).hide(); 

					if ( $parent_clone.size() > 0 ){

						$new_clone.insertAfter( $parent_clone ).fadeIn(); 
					} else {
						$new_clone.prependTo( $cloneable ).fadeIn(); 				
					}
					update_cloneable(); 
					setup_copy_to_use( $new_clone );
					trigger( 'init', $new_clone ); 
					$new_clone.find( 'input[type="text"], input[type="number"], textarea, select' ).focus();
				}
			}
			function remove_clone( $button ){
				if ( ! $button.hasClass('disabled') ){
					var $clone = $button.parents('.clone').first(); 
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

										
				if ( $clones.size() === min_size ){
					$remove_buttons.addClass('disabled' ); 
				} else {
					$remove_buttons.removeClass('disabled' ); 						
				}
				if ( $clones.size() === max_size ){
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
			trigger( 'init', $cloneable ); 
			
		}); 
	
	}
	var setup_copy_to_use = function( jQuery, $context ){
		// popup useful code snippets
		$('.copy_to_use', $context ).click( function(e){
			$('.copy_to_use.active' ).removeClass('active' ); 
			e.preventDefault();
			var $input = $(this).find( 'input'); 
			var $container = $(this); 
			$input.select();
			$container.addClass('active'); 
		
		});    

	}
	
   
   	$( function(){
		$('.cloud-form').each( function(){		
			var $form = $(this);
			on_action( 'init', setup_copy_to_use );
			on_action( 'init', setup_cloneables );
			trigger('init', $form ); 
			
		}); 
	}); 
	return {
		on : on_action
	}
}( jQuery ) );

// for cloneable, avoids selecting parts of children clones
jQuery.fn.getClonePart = function( selector ){
	if ( this.hasClass('cloneable') && this.parents( '.cloneable' ).size() > 0 ){
		var $found = this.find( selector ); 
		return $found ; 
	} else if ( this.hasClass('clone' ) && this.parents('.cloneable').size() == 2 ){	
		var $found = this.find( selector ); 
		return $found ; 	
	} else {
		var selectors = selector.split( ',' );
		var $found = this.find( selector ); 
		for ( i in selectors ){
			selectors[i] = '.clone .cloneable ' + selectors[i] ; 
		}
		$found = $found.not( selectors.join(', ') ); 
		return $found;
	}
}	
var replaceNthMatch = function (original, pattern, n, replace) {
	var parts, tempParts;
	
	if (pattern.constructor === RegExp) {
	
	// If there's no match, bail
		if (original.search(pattern) === -1) {
			return original;
		}
	
		// Every other item should be a matched capture group;
		// between will be non-matching portions of the substring
		parts = original.split(pattern);
	
		// If there was a capture group, index 1 will be
		// an item that matches the RegExp
		if (parts[1].search(pattern) !== 0) {
			throw {name: "ArgumentError", message: "RegExp must have a capture group"};
		}
	} else if (pattern.constructor === String) {
		parts = original.split(pattern);
		// Need every other item to be the matched string
		tempParts = [];
	
		for (var i=0; i < parts.length; i++) {
			tempParts.push(parts[i]);
	
			// Insert between, but don't tack one onto the end
			if (i < parts.length - 1) {
				tempParts.push(pattern);
			}
		}
		parts = tempParts;
	}  else {
		throw {name: "ArgumentError", message: "Must provide either a RegExp or String"};
	}
	
	
	switch( n ){
		case 'first' : 
			n = 1; 
			break; 
		case 'last' : 
			n = Math.floor( parts.length / 2 ); 		
			break; 
	}
	// Parens are unnecessary, but explicit. :)	
	indexOfNthMatch = (n * 2) - 1;
	
	if (parts[indexOfNthMatch] === undefined) {
		// There IS no Nth match
		return original;
	}
	
	if (typeof(replace) === "function") {
		// Call it. After this, we don't need it anymore.
		replace = replace(parts[indexOfNthMatch]);
	}
	
	// Update our parts array with the new value
	parts[indexOfNthMatch] = replace;
	
	// Put it back together and return
	return parts.join('');
} 

