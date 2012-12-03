jQuery(document).ready(function($) {
/* 	cloud.add_action( 'field_clone', color_picker.init ); */
	
/*
	var color_picker.init( input ){
		if ( input.hasClass( '.color-picker-input') ){
		}
	}	
*/
	// miniColors
	$(".color-picker-input").each( function(){
		$(this).miniColors({
			letterCase: 'uppercase',
		});
	});
	$('.option_enabler.color').click( function(){
		var field_container = $(this).parents('.field');

		field_container.find('.color-toggle').toggle('fast');
	}); 
	
    $('.default-swab').click( function(){
		var color_container = $(this).parents('.option');    
		if ( typeof $(this).css('backgroundColor') !== 'undefined' ){
	    	color_container.find('.color-picker-input:visible').miniColors('value',  helpers.rgb2hex($(this).css('backgroundColor')) ); 
	    }
    }); 
    
	// handle fields with enable/default options
/*
	$('.check-all').click( function(){
		$(this).parent('.selection-tools').next('.form-table').find('.option_enabler').not( ':checked').click();
	});
	$('.uncheck-all').click( function(){
		$(this).parent('.selection-tools').next('.form-table').find('.option_enabler:checked').click();
	});
	
*/
	var $set_default_values_popup = $("#set-default-values-popup");
	var options_page = $('.options-page'); 
	if ( $set_default_values_popup.size() > 0 ){
	    $set_default_values_popup.dialog({                   
	        'dialogClass'   : 'wp-dialog',           
	        'modal'         : true,
	        'autoOpen'      : false, 
	        'closeOnEscape' : true,      
	        'buttons'       : {
	        	"Cancel" : function(){
	        		$(this).dialog('close');
	        	},
	            "Confirm": function() {
	                $(this).dialog('close');
	        		theme.set_values_as_default(options_page); 
	
	            }
	        }
	    });	
	}
	$('#values-as-defaults').click( function(e){
		e.preventDefault(); 
		$set_default_values_popup.dialog('open');
	}); 
	$('#values-from-defaults').click( function(e){
		e.preventDefault(); 
		theme.set_values_from_default();
	});	    
});
helpers = {
	rgb2hex : function(rgb) {
	    rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/);
	    function hex(x) {
	        return ("0" + parseInt(x).toString(16).toUpperCase() ).slice(-2);
	    }
	    return "#" + hex( rgb[1] ) + hex( rgb[2] ) + hex(rgb[3]);
	}
}
theme = {
	ajax_url : wp_vars.ajax_url,
	nonce : wp_vars.nonce,
	set_values_as_default : function( options_page ){
		inputs_to_set = jQuery('input.color-picker-input:visible'); /* gets only the visible (enabled) color fields */ 
		data = {
			action : 'set_values_as_defaults',
			inputs : inputs_to_set.serializeArray()
		};
		jQuery.ajax({
			type : 'post',
			url : theme.ajax_url,
			data : data,
			success : function(response){
				options_page.find( 'form' ).submit();
			}
		}); 
	},
	set_values_from_default	: function( options_page ){
		inputs_to_set = jQuery('input.color-picker-input'); /* ALL color fields, enabled and disabled */ 	
		data = {
			action : 'set_values_from_defaults',
			inputs : inputs_to_set.serializeArray()			
		};
		jQuery.ajax({
			type : 'post',
			url : theme.ajax_url,
			data : data,
			dataType : 'json',
			success : function(response){			
				window.location.reload();
			}
		}); 	
	},	
}
