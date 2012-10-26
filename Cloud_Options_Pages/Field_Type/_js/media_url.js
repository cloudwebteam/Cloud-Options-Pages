jQuery( function($){ 
	console.log( 'called' );
	if ( wp_vars['is_options_page'] ){
	console.log( 'is not options page' ); 
	 	// insert upload links
	    $('.upload_button').click(function() {
	         targetfield = jQuery(this).siblings('input');
	         button = $(this);
	         image = $(this).siblings('img'); 
	         tb_show('', 'media-upload.php?post_id=0&amp;TB_iframe=true&type=image');
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