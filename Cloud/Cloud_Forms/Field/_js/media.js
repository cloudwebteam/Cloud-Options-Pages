
jQuery(document).ready(function($){
	var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment;
	var field = $('.field.type-media'); 

	field.find('.upload_button').click(function(e) {

		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $(this);
		var targetfield = button.parent().siblings('input.url_field');
     	var preview_image = button.parent().siblings('.image').find('img.preview-image'); 		
		var to_insert = targetfield.data('to_insert') ;
		_custom_media = true;
		wp.media.editor.send.attachment = function(props, attachment){
			if ( _custom_media ) {
				if ( attachment.type === 'image' ){
					var url = attachment.sizes[ props.size ].url ;
					var image_url = url ;
				} else {
					var url = attachment.url;
					var image_url = attachment.icon;
				}

				var value_to_save = {
					media : attachment.id
				} ;	
				targetfield.val( JSON.stringify( value_to_save ) ) ;
				if ( to_insert === 'ID' ){
					targetfield.val( attachment.id );
				} else if ( to_insert === 'url' ){				
					targetfield.val( url ) ; 
				} else if ( to_insert === 'image' ){
					var image = '<img src="'+url+'" title="'+attachment.title+'" class="media" />'; 
					targetfield.val( htmlEntities( image ) ); 
				}
		         
		         preview_image.slideUp('fast', function(){
		         	$(this).removeClass('hidden'); 
		         	$(this).attr('src', image_url ).attr( 'title', attachment.title ); 
		         	$(this).slideDown('fast');
		         }); 
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		}
		wp.media.editor.open(button);
		return false;
	});
	field.find( '.remove').click( function(){
		var parent_field = $(this).parents('.field.type-media');
		var preview_image = parent_field.find( 'img.preview-image');
		var input = field.find( 'input.url_field' ); 
		input.val('');
		preview_image.slideUp('fast', function(){
	     	$(this).addClass('hidden'); 
	     	$(this).attr('src', '');
	    }); 	
	});
	$('.add_media').on('click', function(){
		_custom_media = false;
	});
});
function htmlEntities(str) {
    return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;');
}