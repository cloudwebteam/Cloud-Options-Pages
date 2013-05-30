
jQuery(document).ready(function($){
	var fields = $('.field.type-media'); 
	fields.each( function(){
		$(this).find( '.upload_button' ).click(function(e) {
			var $button = $(this); 
			// gotta reinit on click...because it might be in a group or cloneable and still be bound to cloned item
			if ( $button.parents('.clone' ).size() !== 0 ){
				var $field = $button.parents('.clone' ) ; 
			} else {
				var $field = $button.parents('.field.type-media' ) ; 			
			}
			var _custom_media = true;
			var _orig_send_attachment = wp.media.editor.send.attachment;
			var _custom_media = true ;	
			var send_attachment_bkp = wp.media.editor.send.attachment;
			var $targetfield = $field.find('input.url_field');
			var $image_container = $field.find( '.image' ); 
			var $preview_image = $image_container.find('img'); 		
			var to_insert = $targetfield.data('to_insert') ;		
			
			$field.find( '.remove-media').click( function(){
				$targetfield.val('');
				$preview_image.slideUp('fast', function(){
			     	$(this).addClass('hidden'); 
			     	$(this).attr('src', '');
			    }); 	
			});
			$('.add_media').on('click', function(){
				_custom_media = false;
			});	
						
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
					$targetfield.val( JSON.stringify( value_to_save ) ) ;
					if ( to_insert === 'ID' ){
						$targetfield.val( attachment.id );
					} else if ( to_insert === 'url' ){				
						$targetfield.val( url ) ; 
					} else if ( to_insert === 'image' ){
						var image = '<img src="'+url+'" title="'+attachment.title+'" class="media" />'; 
						$targetfield.val( htmlEntities( image ) ); 
					}
					
			         $preview_image.slideUp('fast', function(){
			         	$(this).removeClass('hidden'); 
			         	$(this).attr('src', image_url ).attr( 'title', attachment.title ); 
			         	$(this).slideDown('fast');
			         }); 

				} else {
					return _orig_send_attachment.apply( this, [props, attachment] );
				};
			}
			wp.media.editor.open( $button);
			return false;
					
		});
	}); 
});
function htmlEntities(str) {
    return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;');
}