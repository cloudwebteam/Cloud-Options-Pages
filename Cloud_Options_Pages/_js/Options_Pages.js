jQuery( function($){	
	
	$('a[data-toggle="tab"]').on('shown', function (e) {
		//save the latest tab; use cookies if you like 'em better:
		localStorage.setItem('lastTab', $(this).attr('href') );
	});
	
	//go to the latest tab, if it exists:
	var lastTab = localStorage.getItem('lastTab');
	if (lastTab) {
		$('#page-tabs a[href="'+lastTab+'"]').tab('show');
	} else { 
		$('#page-tabs a:first').tab('show');	
	}
	
    $('#scroll-nav').scrollspy();
        
    $('#scroll-nav ul li a').on('click', function(e) {
        e.preventDefault();
        target = this.hash;
        console.log(target);
        $.scrollTo(target, 300);
   });
   
   // popup useful code snippets
   $('a[rel="copy_to_use"]').click( function(e){
   		e.preventDefault();
   		$(this).siblings('.copy').show().select().blur( function(){
   			$(this).hide();
   		});
   		
   }); 
   
   
     
}); 