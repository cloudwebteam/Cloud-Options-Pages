CloudField.on( 'init', function( $, $context ){
	var selector = '.type-file';
	var $fields = $( selector, $context ).add( $context.filter( selector ) );
	$fields.each( function(){
		if ( $(this).find( '.clone' ).size() > 0 ){
			return ; // it has clones that each need to be initialized.
		}
		
		var $field = $(this);
		var $uploader = $field.find( '.uploader' ); 
		var uploaderData = $uploader.data('uploader'); 		
		var $remove = $field.find( '.remove-file') ; 
		var endPoint = uploaderData[ 'isWP' ] ? cloud.wp_ajax : cloud.cloud_ajax ; 

		var $filePathInput = $field.find( 'input[name="'+uploaderData['name']+ '"]' ); 
		var $display = $field.find( '.display'); 
		var $button = $field.find( '.upload-button' ); 
		$uploader.fineUploader({
			debug : true,
			button : $button, 
			dragAndDrop : {
				//extraDropzones : [ $field.find('.drop-zone') ],
				disableDefaultDropzone : true
			}, 
			mode : 'basic',
			request : {
				params : { 
					action :'cloud_upload_file', 
					allowedExtensions : uploaderData[ 'allowedExtensions'], 
					uploadDir : uploaderData['uploadDir'], 
					wpMedia : uploaderData['wpMedia']
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
			}	, 
			validation : {
				itemLimit : 1, 
				acceptFiles : uploaderData['allowedExtensions'].join( ', '),
				allowedExtensions : uploaderData[ 'allowedExtensions']
			}	
		}).on( 'error', function( event, id, name, reason ){
		}).on( 'complete', function( event, id, name, response ){
			if ( response.success ){
				$button.hide(); 
				$display.find( '.name' ).html( name ); 
				$display.find( '.upload-name' ).html( response.uploadName ); 				
				$filePathInput.val( uploaderData['uploadDir'] + '/' + response.uploadName ); 
				$display.show(); 
			} else {
				console.log( name, response ); 
			}
		});


		$remove.on( 'click', function(){
			$button.show(); 
			$display.hide(); 
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
		})

	});
});
