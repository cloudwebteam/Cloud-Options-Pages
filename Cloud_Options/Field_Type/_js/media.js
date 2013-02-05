
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
				var url = attachment.sizes[ props.size ].url ;				
			
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
		         	$(this).attr('src', url ).attr( 'title', attachment.title ); 
		         	$(this).slideDown('fast');
		         }); 
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		}
		wp.media.editor.open(button);
		return false;
	});
	$('.add_media').on('click', function(){
		_custom_media = false;
	});
});
function htmlEntities(str) {
    return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;');
}