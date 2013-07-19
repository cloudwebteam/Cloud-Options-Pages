CloudField.on( 'init', function( $, $context ){
	var selector = '.field.type-post'; 
	var $fields = $( selector, $context ).add( $context.filter( selector ) ); 
	
	var $popup = $( '#wp_link_popup #wp-link-dialog' ) ; 
	var $popup_nonce = $popup.find('#_ajax_linking_nonce').val() ; 
	var $popup_search = $popup.find( '.link-search-field' );	
	
	var $none_found = $popup.find( '.none-found');
	var $loader_icon = $popup.find('.loading img');
	
	

		
	$fields.each( function(){
		var $field = $(this); 
		var $button = $field.find( '.select-post' ); 
		var $targetfield = $field.find( '.target-field' ); 
		var post_info_to_get = $targetfield.data('to_get'); 
		var post_info_image_size = $targetfield.data('image_size'); 
		var $popup_content = $field.find( '.popup > div' ); 
		var $remove = $field.find( '.remove-post' ); 
		var $post_title = $field.find( '.post-title' ); 
		var $post_type = $field.find( '.post_type' ); 
		var content_list = {
			list : $popup.find( '.query-results ul' ), 
			init : function( e ){
				this.list.find('li a').each( function(){
					$(this).click( function(e){
						e.preventDefault();
						var value_to_save = {
							post : $(this).data('post_id')
						} ;

						
						$targetfield.val( JSON.stringify( value_to_save ) );
						$targetfield.siblings( '.preview' ).html( '<div class="inner">' + $(this).parents( 'li' ).find( '.to_insert' ).html() + '</div>' ) ;
						$post_title.text( $(this).find( '.title').text() ) ; 
						$post_type.text( $(this).find( '.type').text() ) ; 					
						$remove.removeClass( 'hidden' ); 
						$.fancybox.close();
						
					});
				});
			}
		}; 
	
		var ajax = {
			get_list: function( search ) {	
				if ( search ){
					var data = {
						action : 'options-page-link-popup',
						search : search,
						'_ajax_linking_nonce' : $popup_nonce,
						to_get : post_info_to_get, 
						image_size : post_info_image_size
					};
					$loader_icon.show();
					$.post( cloud.wp_ajax, data, function( response ) {
						if ( response ){
							$none_found.hide();
							content_list.list.html( response ).fadeIn('fast');
							content_list.init();
						} else {
							content_list.list.hide();
							$none_found.fadeIn('fast');
						}
						$loader_icon.hide();
					}, "html" );			
				} else {
					content_list.list.html( '' );
				}
			}, 
			get_initial_list : function() {
				var data = {
					action : 'options-page-link-popup',
					search : '',
					'_ajax_linking_nonce' : $popup_nonce,
					to_get : post_info_to_get, 
					image_size : post_info_image_size
											
				};
				content_list.list.hide();
				$.post( cloud.wp_ajax, data, function( response ) {
					if ( response ){
						$none_found.hide();
						content_list.list.html( response ).slideDown('fast');
						content_list.init();
					} else {
						content_list.list.hide();
						$none_found.fadeIn('fast');
					}
					$loader_icon.hide();
				}, "html" );				
			}
		}			
		$button.click( function(e){
			e.preventDefault(); 
			$.fancybox.open({ 
				href : '#' + $popup.attr('id'), 
				title : 'Get Existing Content Info ( ' + post_info_to_get + ' ) ',
				beforeShow : ajax.get_initial_list(),
				afterClose : function(){
					$popup_search.val(''); 
					content_list.list.html( '' ); 
					$targetfield.removeClass('target' );
				}, 
				height: 400, 
				width: 300,
				autoSize: false
			} );			
		});
		$remove.click( function(e){
			$targetfield.val('');
			$post_title.text( 'None Selected' ); 
			$post_type.text( 'Post' ); 
			$remove.addClass( 'hidden' ); 
		}); 
		$popup_search.keyup( function(){
			ajax.get_list( $(this).val() );
		});				
	}); 
	

});