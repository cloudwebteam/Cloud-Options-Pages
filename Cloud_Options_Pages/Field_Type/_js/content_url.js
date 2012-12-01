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
					$('.field.type-content_url input.target').val( $(this).attr('href') );
					
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
					'_ajax_linking_nonce' : popup_nonce					
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
				'_ajax_linking_nonce' : popup_nonce					
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
	
	$('.field.type-content_url input').click( function(e){
		var field = $(this).parents('.field' );
		var input = $(this).addClass( 'target' ); 
		var popup_content = field.find('.popup > div'); 
		$.fancybox.open({ 
			href : '#' + popup.attr('id'), 
			title : 'Get Existing Content URL',
			beforeShow : ajax.get_initial_list(),
			afterClose : function(){
				popup_search.val(''); 
				content_list.list.html( '' ); 
				input.removeClass('target' );
			}, 
			height: 400, 
			width: 300,
			autoSize: false
		} );
	});
	
});