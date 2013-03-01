jQuery( function($){
	// "link_popup_id" from localize script
	var popup = $( '#' + link_popup_id + ' #wp-link-dialog' ) ; 
	var popup_nonce = popup.find('#_ajax_linking_nonce').val() ; 
	var popup_search = popup.find( '.link-search-field' );
	var content_list = {
		list : popup.find( '.query-results ul' ), 
		init : function( e ){
			this.list.find('li a').each( function(){
				$(this).click( function(e){
					e.preventDefault();
					var value_to_save = {
						post : $(this).data('post_id')
					} ;
					post_info_target.val( JSON.stringify( value_to_save ) );
					post_info_target.siblings( '.preview' ).html( '<div class="inner">' + $(this).parents( 'li' ).find( '.to_insert' ).html() + '</div>' ) ;
					post_info_target.siblings( '.current-data').find( '.post-title' ).text( $(this).find( '.title').text() ) ; 
					post_info_target.siblings( '.current-data').find( '.post-type' ).text( $(this).find( '.type').text() ) ; 					
					$.fancybox.close();
					
				});
			});
		}
	}; 
	var none_found = popup.find( '.none-found');
	var loader_icon = popup.find('.loading img');
	var ajax = {
		get_list: function( search ) {	
			if ( search ){
				var data = {
					action : 'options-page-link-popup',
					search : search,
					'_ajax_linking_nonce' : popup_nonce,
					to_get : post_info_to_get, 
					image_size : post_info_image_size
				};
				loader_icon.show();
				$.post( wp_vars.ajax_url, data, function( response ) {
					if ( response ){
						none_found.hide();
						content_list.list.html( response ).fadeIn('fast');
						content_list.init();
					} else {
						content_list.list.hide();
						none_found.fadeIn('fast');
					}
					loader_icon.hide();
				}, "html" );			
			} else {
				content_list.list.html( '' );
			}
		}, 
		get_initial_list : function() {
			var data = {
				action : 'options-page-link-popup',
				search : '',
				'_ajax_linking_nonce' : popup_nonce,
				to_get : post_info_to_get, 
				image_size : post_info_image_size
										
			};
			content_list.list.hide();
			$.post( wp_vars.ajax_url, data, function( response ) {
				if ( response ){
					none_found.hide();
					content_list.list.html( response ).slideDown('fast');
					content_list.init();
				} else {
					content_list.list.hide();
					none_found.fadeIn('fast');
				}
				loader_icon.hide();
			}, "html" );				
		}
	}

	popup_search.keyup( function(){
		ajax.get_list( $(this).val() );
	});	
	
	$('.field.type-post .select-post').click( function(e){
		e.preventDefault();
		post_info_field = $(this).parents('.field' );
		post_info_target = $(this).parents( '.input').find('.target-field').first().addClass( 'target' ); 
		post_info_to_get = post_info_target.data('to_get'); 
		post_info_image_size = post_info_target.data('image_size'); 
		var popup_content = post_info_field.find('.popup > div'); 
		$.fancybox.open({ 
			href : '#' + popup.attr('id'), 
			title : 'Get Existing Content Info ( ' + post_info_to_get + ' ) ',
			beforeShow : ajax.get_initial_list(),
			afterClose : function(){
				popup_search.val(''); 
				content_list.list.html( '' ); 
				post_info_target.removeClass('target' );
			}, 
			height: 400, 
			width: 300,
			autoSize: false
		} );
	});
	
});