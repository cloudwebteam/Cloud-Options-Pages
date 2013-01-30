<?php

/***====================================================================================================================================
		BASIC STRUCTURE
	==================================================================================================================================== ***/
array(
	'page1' => array(
		// top level options
		'subpages' => array(
			'page1' => array(
				// page options
				'sections' => array(
					'section1' => array(
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
					'section2' => array(
						// ... same as above
					)
				)
			),
			'subpage2' => array(
				// ... same as above
			)
		)
	),
	'page2' => array(
		// ... same as above
	)
);

/***====================================================================================================================================
		CASCADING DEFAULTS
	==================================================================================================================================== ***/
/*
If subpage option not specified, checks the following to fill out options:
	its parent top level defaults['subpages'] array() ; 
	defaults.php
If section option not specified, checks the following to fill out options:
	its parent subpage defaults['sections'] array() ; 
	its top level defaults['sections'] array() ; 
	defaults.php
If field option not specified, checks the following to fill out options:
	its parent section defaults['fields'] array() ; 
	its parent subpage defaults['fields'] array() ; 
	its top level defaults['fields'] array() ; 
	defaults.php
If sub-field option not specified, checks the following to fill out options:
	its parent field defaults['subfields'] array() ; 
	its parent section defaults['subfields'] array() ; 
	its parent subpage defaults['subfields'] array() ; 
	its top level defaults['subfields'] array() ; 
	defaults.php
*/	
/***====================================================================================================================================
		OPTIONS PAGES DEMO
	==================================================================================================================================== ***/
$options_page_array = array (
	'top_level' => array (
		'image' 	=> 'IMAGE URL', // Full path to the menu icon
		'priority'  => 52, // Where in the menu it is located ( 0 = top )
		'subpages'		=> array (
			// one of the subpage keys MUST match the top level key		
			'top_level' => array ( 
				'title' 		=> 'Subpage Title on Page',
				'menu_title' 	=> 'Subpage Menu Title',
				'layout'		=> 'tab', // Options: standard, tab, scroll, grid
				'sections'		=> array (
					'header'	=> array(
						'title' 	=> 'Section Title',
						'description'	=> 'A section description for me!', // optional, shows just below section title
						'layout' => 'standard', // Options: standard, grid
						'width' => 6, // only matters if parent subpage layout is grid, based on 12 column grid
						'fields'		=> array(
							//common to all 
							'all_fields' => array(
								'width' 			=> 4, // only matters if parent section layout is grid, based on 12 column grid
								'title'				=> 'Field Title', // Text for label
								'description'		=> null, // text for description
								'default' 			=> '',	// if no value has been saved, what is the default?								
								'layout'			=> array('label', 'field', 'description'), // arrangement of label, field, and description. Each item in array represents a row. Can be nested in to sub-arrays.
								'cloneable'			=> false, // Makes the field cloneable ( not available for groups )
								'editor_list'		=> false, // Should it show in WP's post editor as a shortcode option?	
								'_lock'				=> false, // disable code link, clone controls, and sorting for cloneable/groups. 
								'code_link' 		=> true, // disable/enable "use" code link next to options
								'clone_controls'	=> true, // disable/enable cloning plus/minus
								'sort'				=> true, // disable/enable cloning/group sorting capabilities
							),
							'color' => array(
								'type' => 'color',
							),

							'date' => array(
								'type' => 'date', 
								'date_format' => 'D, M d, yy' // see http://docs.jquery.com/UI/Datepicker/formatDate for options
							),
							'datetime' => array(
								'type' => 'datetime',
								'date_format' => 'D, M d, yy', // see http://docs.jquery.com/UI/Datepicker/formatDate for options
								'time_format' => 'hh:mm tt'	// see http://trentrichardson.com/examples/timepicker/ for options						
							),
							'group' => array(
								'type' => 'group',
								'subfields' => array(
									// same arrays as for fields
									'two' => array()
								)
							),
							'info' => array(
								'type' => 'info',
								'description' => '<h3>Info here!</h3><p>A piece of pertinent information here!</p>'	// Just give whatever HTML you want here. That's all this does!							
							),
							'media' => array(
								'type' => 'media', 
								'get' => 'url', // supports url, ID, image
							),
							'post' => array(
								'type' => 'post',
								'get' => 'image', // Supports image, ID
								'image_size' => 'full' // any registered image size
							),							
							'select' => array( 
								'type' => 'select', 
								'options' => 'post' // supports arrays of options with/without keys, any post type
							),
							'text' => array(
							),
							'textarea' => array(
								'type' => 'textarea',
							),
							'time' => array(
								'type' => 'time', 
								'time_format' => "h 'hours and' m 'minutes'" // see http://trentrichardson.com/examples/timepicker/ for options
							), 
							'wysiwyg' => array( 
								'type' => 'wysiwyg'
							)																
						)

					)
				)
			),
		)
	)
);
Cloud_Options::add_pages( $options_page_array );






