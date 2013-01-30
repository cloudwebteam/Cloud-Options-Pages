<?php

/***====================================================================================================================================
		BASIC STRUCTURE
	==================================================================================================================================== ***/
// identical to section layout for options pages
'metaboxes' => array(
	'metabox1' => array(
		// section options
		'fields' => array(
			'field1' => array( 
				// field options
			),
			'field2' => array(
				// ... same as above
			)
		)
	),
	'metabox2' => array(
		// ... same as above
	)
) ;

/***====================================================================================================================================
		CASCADING DEFAULTS
	==================================================================================================================================== ***/
/*
If field option not specified, it checks the following to fill out options:
	its parent metabox defaults['fields'] array() ; 
	defaults.php
If sub-field option not specified, it checks the following to fill out options:
	its parent field defaults['subfields'] array() ; 
	its parent metabox defaults['subfields'] array() ; 
	defaults.php
*/	
/***====================================================================================================================================
		METABOXES DEMO
	==================================================================================================================================== ***/
	
// examples of how to specificy which posts to place the metabox on.
$which_posts = 14 ; // post ID
$which_posts = 'Hello World' ; // post title
$which_posts = 'hello-world' ; // post slug ( can match multiple )
$which_posts = array( 
	'post_type' => 'page' //takes all args that get_posts() takes
) ; 	

$metaboxes_array = array (
	'metabox1'	=> array(
		'title' 	=> 'Metabox Title',
		'description'	=> 'A metabox description for me!', // optional, shows just below section title
		'context' => 'normal', // normal, advanced, side 		
		'priority' => 'high' , // high, core, default, low (dictates up or down position)
		'fields'		=> array(
			// all fields identical to options pages
			'one' => array()
		)

	)
);

$priority = 'high' ; // same as context above, but applies to all metaboxes in array without it specified ( a default for all metaboxes in array)
$context = 'normal' ; // same as priority above, but applies to all metaboxes in array without it specified ( a default for all metaboxes in array)

Cloud_Options::add_metaboxes( $which_posts, $metaboxes_array, $context, $priority );






