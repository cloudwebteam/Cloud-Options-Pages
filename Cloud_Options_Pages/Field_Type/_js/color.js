jQuery(document).ready(function($) {
    if ( wp_vars['is_options_page'] ){
 
	 	// insert upload links
	    $('.upload_button').click(function() {
	         targetfield = jQuery(this).prev('.upload_url');
	         tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	         return false;
	    });
	    window.send_to_editor = function(html) {
	         imgurl = jQuery('img',html).attr('src');
	         jQuery(targetfield).val(imgurl);
	         tb_remove();
	    }
	}	
				
				
	// miniColors

	$('.check-all').click( function(){
		$(this).parent('.selection-tools').next('.form-table').find('.option_enabler').not( ':checked').click();
	});
	$('.uncheck-all').click( function(){
		$(this).parent('.selection-tools').next('.form-table').find('.option_enabler:checked').click();
	
	});
	$(".color-picker").miniColors({
		letterCase: 'uppercase',
		change: function(hex, rgb) {
		},
		open: function(hex, rgb) {
		},
		close: function(hex, rgb) {
		}
	});
	$('.option_enabler.color').click( function(){
		console.log('checked');
		$(this).prev('.option').toggle('fast');
	}); 
	
	var $set_default_colors_popup = $("#set-default-colors-popup");
    $set_default_colors_popup.dialog({                   
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
        		theme.set_colors_as_default(); 

            }
        }
    });	
    $('.default-swab').click( function(){
    	$(this).next('.option').find('.color-picker:visible').miniColors('value',  helpers.rgb2hex($(this).css('backgroundColor')) ); 
    }); 
	$('#colors-as-defaults').click( function(e){
		e.preventDefault(); 
		$set_default_colors_popup.dialog('open');
	}); 
	$('#colors-from-defaults').click( function(e){
		e.preventDefault(); 
		theme.set_colors_from_default();
	});
});
theme = {
	ajax_url : wp_vars.ajax_url,
	nonce : wp_vars.nonce,
	set_colors_as_default : function(){
		inputs_to_set = jQuery('input.color-picker:visible'); /* gets only the visible (enabled) color fields */ 
		data = {
			action : 'theme_set_colors_as_defaults',
			inputs : inputs_to_set.serializeArray(),
			nonce : theme.nonce
		};
		jQuery.ajax({
			type : 'post',
			url : theme.ajax_url,
			data : data,
			//dataType : 'json',
			success : function(response){
				 jQuery('#colors-as-defaults').find('.success').fadeIn('fast').delay(3000).fadeOut('fast');
			}
		}); 
	},
	set_colors_from_default	: function(){
		console.log('triggered');
		data = {
			action : 'theme_set_colors_from_defaults',
			nonce : theme.nonce
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
helpers = {
	rgb2hex : function(rgb) {
	    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	    function hex(x) {
	        return ("0" + parseInt(x).toString(16)).slice(-2);
	    }
	    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}
}

