CloudField.on( 'init', function( $, $context ){
	var selector = '.type-file';
	var $fields = $( selector, $context ).add( $context.filter( selector ) );
	$fields.each( function(){
		if ( $(this).find( '.clone' ).size() > 0 ){
			return ; // it has clones that each need to be initialized.
		}
		
		var $field = $(this);
		var $uploader = $field.find( '.uploader' );
		var $innerContainer = $field.find( '.inner-container'); 
		var uploaderData = $uploader.data('uploader');
		var $remove = $field.find( '.remove-file');
		var endPoint = uploaderData[ 'isWP' ] ? cloud.wp_ajax : cloud.cloud_ajax;

		var $filePathInput = $field.find( 'input[name="'+uploaderData['name']+ '"]' );
		var $display = $field.find( '.display');
		var $button = $field.find( '.upload-button' );
		$uploader.fineUploader({
			debug : false,
			button : $button,
			dragAndDrop : {
				//extraDropzones : [ $field.find('.drop-zone') ],
				disableDefaultDropzone : true
			},
			mode : 'basic',
			request : {
				params : {
					action : 'cloud_upload_file',
					allowedExtensions : uploaderData.allowedExtensions,
					uploadDir : uploaderData.uploadDir,
					wpMedia : uploaderData.wpMedia
				},
				paramsInBody : true,
				endpoint: endPoint,
				autoUpload: false,
			},
			failedUploadTextDisplay: {
				mode: 'custom',
				maxChars: 40,
				responseProperty: 'error',
				enableTooltip: true
			}	
		}).on( 'error', function( event, id, name, reason ){
		}).on( 'complete', function( event, id, name, response ){
			if ( response.success ){
				var image_path = uploaderData['uploadDir'] + '/' + response.uploadName; 

				var image_src = get_image_src( image_path ); 
				console.log( image_src ) ; 
				$innerContainer.addClass('has-value');
				$display.find( 'img').attr('src', image_src ); 
				$display.find( '.name' ).html( name ); 
				$display.find( '.upload-name' ).html( response.uploadName ); 				
				$filePathInput.val( image_path ); 
			} else {
				console.log( name, response ); 
			}
		});

		$remove.on( 'click', function(){
			$innerContainer.removeClass('has-value');
			$display.find( 'img').attr('src', '' ); 
			$display.find( '.name').html(''); 
			var uploadName = $display.find( '.upload-name' ).html(); 				
			$display.find( '.upload-name' ).html(''); 
			$filePathInput.val(''); 
			$.ajax({
				type : 'post', 
				dataType : 'json',
				url : endPoint, 
				data : {
					action : 'cloud_delete_upload', 
					fileName : uploadName, 
					uploadDir : uploaderData['uploadDir']
				}, 
				success : function( response ){
					console.log( response );
				}
			}); 
		});

	});
	function get_image_src( path ){
		var path_parts = path.split('/'); 
		var url_parts = cloud.cloud_url.split( '/' ); 
		var path_index = '';
		var url_index = ''; 
		for( i in path_parts ){
			if ( ! path_index ){
				if ( path_parts[i]){
					for ( j in url_parts ){
						if ( url_parts[j] ){
							if ( url_parts[j] === path_parts[i] ){
								path_index = i; 
								url_index = j;
							}
						}
					}
				}
			}
		}
		var path = path_parts.slice( path_index ).join('/'); 
		var url = url_parts.slice( 0, url_index ).join('/');
		return url + '/' + path ; 
	}
});
