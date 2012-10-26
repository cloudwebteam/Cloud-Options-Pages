jQuery( function($){ 
	if ( wp_vars['is_options_page'] ){
	 	// insert upload links
	    $('.upload_button').click(function() {
	         targetfield = jQuery(this).siblings('input');
	         button = $(this);
	         tb_show('', 'media-upload.php?post_id=0&amp;TB_iframe=true&type=image');
	         return false;
	    });
	    window.send_to_editor = function(html) {
	         url = $(html).attr('href');
	         jQuery(targetfield).val(url);
	         tb_remove();
	    }
	}   
});