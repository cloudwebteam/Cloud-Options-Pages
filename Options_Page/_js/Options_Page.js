jQuery( function($){
	$('#page-tabs a:first').tab('show');
	
    $('#scroll-nav').scrollspy();
        
    $('#scroll-nav ul li a').on('click', function(e) {
        e.preventDefault();
        target = this.hash;
        console.log(target);
        $.scrollTo(target, 300);
   });
   
   
    if ( wp_vars['is_options_page'] ){
 
	 	// insert upload links
	    $('.upload_button').click(function() {
	         targetfield = jQuery(this).prev('input');
	         tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&post_id=10');
	         return false;
	    });
	    window.send_to_editor = function(html) {
	         imgurl = jQuery('img',html).attr('src');
	         jQuery(targetfield).val(imgurl);
	         targetfield.nextAll('.preview-image').attr('src', imgurl);
	         tb_remove();
	    }
	}   
}); 