jQuery( function($){ 
		// insert upload links
	var field = $('.field.type-media_url'); 
    field.find('.upload_button').click(function() {
         old_send_to_editor_function = window.send_to_editor ; 
         window.send_to_editor = customized_send_to_editor ;

         targetfield = $(this).siblings('input');
         button = $(this);
         tb_show('', 'media-upload.php?post_id=0&amp;TB_iframe=true&type=image');
         

         return false;
    });
    var customized_send_to_editor = function(html) {
         imgurl = jQuery('img',html).attr('src');
         jQuery(targetfield).val(imgurl);

     	 image = targetfield.siblings('img.preview-image'); 
         if ( image.size() > 0 ){
         
	         image.slideUp('fast', function(){
	         	$(this).removeClass('hidden'); 
	         	$(this).attr('src', imgurl);
	         	$(this).slideDown('fast');
	         }); 
	     }
	     window.send_to_editor = old_send_to_editor_function ;
         tb_remove();
	}      	
});