<?php

// fallback defaults for every possible option should be here. 

global $options_pages_defaults; 
$options_pages_defaults = array (

	'top_level' => array (
		'image'		=> 'IMAGE_URL', 
		'priority'	=> 52,
		'subpages'	=> array (
		)
	), 
	
	'subpages'	=> array (
		'title'			=> 'Default Page Title',
		'menu_title'	=> 'Default',
		'capability'	=> 'create_users',
		'layout'		=> 'standard',
		'style'			=> 'standard',
		'description' 	=> null,
		'_has_settable_defaults' => false,
		'sections'		=> array (
		)	
	), 
	
	'sections'	=> array (
		'title'			=> 'Default Section Title',
		'layout'		=> 'standard',
		'width'			=> 6,
		'description'	=> null,
		'metabox_context' => 'normal', 
		'metabox_priority' => 'low' , 
		'_has_settable_defaults' => false,		
		'fields'		=> array ( 
		)		
	),
	
	'fields'	=> array (
		'general' => array(
			'title'				=> 'Default Field Title',
			'layout'			=> 'standard',
			'cloneable'			=> false,
			'size'				=> null,
			'description'		=> null,
			'editor_list'		=> false,
			'subfields'			=> null,
			'settable_defaults' => false,
			
			'code_link' 		=> true, 
			'clone_controls'	=> true,
			'sort'				=> true, 
			'_lock'				=> false			
		), 
		'color'		=> array(
		
			'title'				=> 'Color',		
			'size'				=> 5,
			'settable_defaults' => true,
			'layout'			=> 'standard',
			'cloneable'			=> false,
			'size'				=> null,
			'description'		=> null,
			'editor_list'		=> false,
			'subfields'			=> null,
			'settable_defaults' => true,
			'default'			=> '#FFFFFF',									
			'code_link' 		=> true, 
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'_lock'				=> false, 				
		),
		'content_url' => array(
			'title'			=> 'Content URL',						
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'description'	=> null,
			'size'			=> 55,			
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true,
			'_lock'			=> false				
								
		),		
		'date'		=> array(
			'title'				=> 'Date',		
			'layout'			=> 'standard',
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 8,	
			'editor_list'		=> false,			
			'code_link' 		=> true, 
			'clone_controls'	=> true	, 
			'sort'				=> true,
			'_lock'				=> false															
		),		
		'datetime'		=> array(
			'title'				=> 'Date/Time',		
			'layout'			=> 'standard',
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 19,
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'clone_controls'	=> true	 , 
			'sort'				=> true	,
			'_lock'				=> false				
													
		),
		'group'		=> array(
			'title'			=> 'Group',		
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'size'			=> null,
			'description'	=> null,
			'subfields'		=> null,
			'editor_list'	=> false,			
			'code_link' 	=> true, 
			'clone_controls'=> true	,
			'sort'			=> true, 
			'_lock'			=> false									
		),
		'info'		=> array(
			'title'			=> 'Info',
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'description'	=> null,
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true, 
			'_lock'			=> false											
		),
		'media_url' => array(
			'title'			=> 'URL',		
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'size'			=> 55,
			'description'	=> null,
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true,
			'use_image'		=> true,
			'_lock'			=> false											
		),		
		'select'	=> array(
			'title'			=> 'Select Menu',				
			'multiple'		=> false,
			'options'		=> 'page',
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'size'			=> 30,
			'description'	=> null,
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true	,
			'sort'			=> true,
			'_lock'			=> false											
		),
		'text' => array(
			'title'				=> 'Text Input',						
			'layout'			=> 'standard',
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'editor_list'		=> false,				
			'code_link' 		=> true, 
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'_lock'				=> false						
		),
		'time' => array(
			'title'			=> 'Time',						
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'description'	=> null,
			'size'			=> 6,	
			'editor_list'	=> false,			
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true,
			'_lock'			=> false				
					
		),		
		'textarea' => array(
			'title'			=> 'Textarea',
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'description'	=> null,
			'rows'			=> 3,
			'cols'			=> 57,
			'editor_list'	=> false,			
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true,
			'_lock'			=> false										
		), 
		'url' 		=> array(
			'title'			=> 'Default URL Title',	
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'size'			=> 55,
			'description'	=> null,
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true,
			'sort'			=> true,
			'_lock'			=> false				
							
		),	
		'wysiwyg' => array(
			'title'			=> 'WYSIWYG',
			'layout'		=> 'standard',
			'cloneable'		=> false,
			'description'	=> null,
			'rows'			=> 3,
			'cols'			=> 57,
			'editor_list'	=> false,			
			'code_link' 	=> true, 
			'clone_controls'=> true,
			'sort'			=> true,
			'_lock'			=> false				
									
		), 
	),

); 