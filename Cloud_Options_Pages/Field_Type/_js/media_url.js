jQuery( function($){ 
	if ( wp_vars['is_options_page'] ){
 
	 	// insert upload links
	    $('.upload_button').click(function() {
	         targetfield = jQuery(this).siblings('input');
	         button = $(this);
	         image = $(this).siblings('img'); 
	         tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&post_id=0');
	         return false;
	    });
	    window.send_to_editor = function(html) {
	         imgurl = jQuery('img',html).attr('src');
	         jQuery(targetfield).val(imgurl);
	         image.slideUp('fast', function(){
	         	$(this).removeClass('hidden'); 
	         	$(this).attr('src', imgurl);
	         	$(this).slideDown('fast');
	         }); 
	         tb_remove();
	    }
	}   
});