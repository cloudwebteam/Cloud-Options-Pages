CloudField.on( 'init', function( $, $context ){
	var $ = jQuery ; 
	var selector = '.field.type-map'; 
	var $fields = $( selector, $context ).add( $context.filter( selector ) ); 
	
	var defaults = {
		zoom : 7, 
		mapType : 'ROADMAP',
		latitude : 35.469618,
		longitude : -97.514648
	} ; 
	https://maps.google.com/?ll=35.469618,-97.514648&spn=4.200281,4.790039&t=m&z=8
	$fields.each( function(){
		var $field = $(this) ;
		var $latitude = $(this).find( 'input.latitude' );
		var $longitude = $(this).find( 'input.longitude' );	
		var $zoom = $(this).find( 'input.zoom' ); 
		var $map = $(this).find( '.map-container' ); 
		$map.on( 'mousedown', function(e){
			e.stopPropagation(); 
		}); 
					
		var map = false ; 
		var marker = false; 
		var addMarkerWindow = false;
		var mapZoom ; 											
		
		function initialize(){
			var latitude = $latitude.val();
			var longitude = $longitude.val(); 
			var zoom = $zoom.val(); 		
		
			var settings = {}; 
			if ( latitude ) settings.latitude = latitude ; 
			if ( longitude ) settings.longitude = longitude ; 
			if ( zoom && parseInt( zoom ) > 6 && parseInt( zoom ) < 20 ) settings.zoom = parseInt( zoom ) ; 	
			
			var settings = $.extend( defaults, settings ); 					

			var mapOptions = {
				zoom : settings.zoom, 
				mapTypeId : google.maps.MapTypeId[ settings.mapType ],
				center : new google.maps.LatLng( settings.latitude, settings.longitude ),
			}
			map = new google.maps.Map($map[0], mapOptions );
			
			
			var updateMarker = function( latitude, longitude ){
				var position = typeof( latitude ) === 'object' ? latitude : new google.maps.LatLng( latitude, longitude ) ; 		
				if ( marker ){
					marker.setPosition( position );
				} else {
					marker = new google.maps.Marker({
						position: position,
						map: map,
						draggable : true,
						title: "Latitude: " + position.lat() + ', ' +  "Longitude: " + position.lng() 
					});		
					google.maps.event.addListener( marker, 'click', function(){
						addMarkerWindow = new google.maps.InfoWindow({
							content : "<b>Latitude:</b> " + position.lat() + '<br />' +  "<b>Longitude:</b> " + position.lng() , 
							position : position, 
							map : map
						}); 			
					}); 
					google.maps.event.clearListeners(map, 'click' );
					google.maps.event.addListener(map, 'zoom_changed', function(){
						$zoom.val( map.getZoom() ); 
					}); 
				}
				map.setCenter( position ) ;
				saveMarkerPosition(); 
				
				return marker ; 
						
			}	
						
			
			
			// if there is a saved lat and long
			if ( latitude && longitude ){
				marker = updateMarker( latitude, longitude ); 		
			}
			
	
	
			google.maps.event.addListener(map, 'click', function( e) {
				mapZoom = map.getZoom();
				if ( ! marker ){ 
					setTimeout( function(){
						handleAddMarkerWindow( e.latLng );
					}, 600); 
				}
			});
			function handleAddMarkerWindow( latLng ){
				if ( mapZoom == map.getZoom()  ){
					$( '.place-marker', $field ).live( 'click.gmap', function(){
						marker = updateMarker( latLng ); 
						addMarkerWindow.close();
						$( '.place-marker', $field ).die( 'click.gmap' ); 
						addMarkerWindow = false; 
					}); 						
					if ( addMarkerWindow ){
						addMarkerWindow.close(); 
						$( '.place-marker', $field ).die( 'click.gmap' ); 
						addMarkerWindow = false; 
					} else {
						addMarkerWindow = new google.maps.InfoWindow({
							content : '<a class="place-marker" href="#marker">Place marker</a>', 
							position : latLng, 
							map : map
						}); 
						google.maps.event.addListener( addMarkerWindow, 'closeclick', function(){
							$( '.place-marker', $field ).die( 'click.gmap' ); 
							addMarkerWindow = false; 						
						}); 
					}				
				}
			}
			$latitude.add( $longitude ).on( 'change', function(){
				updateMarker( $latitude.val(), $longitude.val() );
			}); 
					
		}
		var checkIfVisible = setInterval( function(){
			if ( $map.is(':visible') ){
				clearInterval( checkIfVisible ); 
				initialize(); 
			}
		}, 100 ); 
		function saveMarkerPosition(){
			var position = marker.getPosition();
			$latitude.val( position.lat() ); 
			$longitude.val( position.lng() ); 		
		}

		var updatePosition  = function( latitude, longitude ){
			$latitude.val( latitude ); 
			$longitude.val( longitude ).change(); 				
		}
	
		
		$map.data( 'map', {
			updateMarker: updatePosition
		}); 	
	}); 
});
