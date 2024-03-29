<?php 
// fallback defaults for every possible option should be here. 

global $cloud_form_defaults_wp ; 
$cloud_form_defaults_wp = array (

	'top_level' => array (
		'image'		=> 'dashicons-admin-generic', 
		'priority'	=> 52,
		'subpages'	=> array (
		)
	), 
	'subpages'	=> array (
		'menu_title'	=> 'Default',
		'title' 		=> 'Default Title',
		'capability'	=> 'create_users',
		'submit_text' 	=> 'Save Changes',
		'layout'			=> 'standard', // tabs, tabs_animated, standard, <custom html> (insert sections as [section_slug] )
		'description' => false,
		'header_layout'		=> false, // <custom html> (insert [title] and [description] where desired )
		'footer_layout' 	=> false, // <custom html> (insert [submit] and [description] where desired )
	), 	
	'forms' => array(
	),
	'sections'	=> array (
		'context' => 'normal', 
		'priority' => 'low' , 
	),
	
	'fields'	=> array (
		'general' => array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false			
		), 
		'checkbox' => array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false			
		),
		'color'		=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 				
		),	
		'date'		=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false										
		),		
		'datetime'		=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 																		
		),
		'divider' => array (
		),
		'file'		=> array(
		),		
		'group'		=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 										
		),
		'info'		=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 													
		),
		'map'		=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 													
		),		
		'media' => array(
			'title'			=> 'Media',		
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'size'			=> 55,
			'width' 		=> 6,			
			'description'	=> null,
			'get' 			=> 'url',
			'default' 		=> '',	
			'disabled'		=> false,		
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true,
			'use_image'		=> true,
			'image_size' 	=> 'full',
			'_lock'			=> false											
		),	
		'number' => array( 
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 			
		), 
		'password' 	=>  array(
		),
		'post' => array(
			'title'			=> 'Post Info',						
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'image_size' 	=> 'thumbnail',
			'description'	=> null,
			'size'			=> 55,	
			'get' 			=> 'ID',
			'preview'	 	=> true,
			'width' 		=> 6,	
			'default' 		=> '',							
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true,
			'_lock'			=> false				
		),	
		'radio'	=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 											
		),
		'range_slider'	=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 											
		),				
		'select'	=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 											
		),
		'startend'		=> array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 				
		),		
		'text' => array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 						
		),
		'textarea' => array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 										
		), 		
		
		'time' => array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 					
		),
		'toggle' => array(
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'_lock'				=> false, 			
		),			
		'wysiwyg' => array(
			'title'			=> 'WYSIWYG',
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'description'	=> null,
			'rows'			=> 3,
			'cols'			=> 57,
			'width' 		=> 6, 
			'default' 		=> '',			
			'editor_list'	=> false,			
			'code_link' 	=> true, 
			'clone_controls'=> true,
			'sort'			=> true,
			'_lock'			=> false				
									
		), 
	),

); 