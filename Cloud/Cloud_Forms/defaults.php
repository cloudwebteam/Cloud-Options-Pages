<?php

// fallback defaults for every possible option should be here. 

global $cloud_form_defaults; 
$cloud_form_defaults = array (
	'forms'	=> array (
		'title'			=> 'Default Page Title',
		'layout'		=> 'standard',
		'style'			=> 'standard',
		'description' 	=> null,
		'ajax' 			=> false,
		'submit_text' 	=> 'Save',
		'sections'		=> array (
		)	
	), 
	
	'sections'	=> array (
		'title'			=> 'Default Section Title',
		'layout'		=> 'standard',
		'width'			=> 6,
		'description'	=> null,
		'fields'		=> array ( 
		)		
	),
	
	'fields'	=> array (
		'general' => array(
			'title'				=> 'Default Field Title',
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> null,
			'width' 			=> 6,
			'description'		=> null,
			'default' 			=> '',			
			'subfields'			=> null,
			'clone_controls'	=> true,
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		), 
		'checkbox' => array(
			'title'				=> 'Checkbox',
			'layout'			=> array(array( 'field' , 'error','label' ), 'description'),
			'cloneable'			=> false,
			'checkbox_value' 	=> 1,
			'multiple' 			=> false,
			'options' 			=> false,
			'size'				=> null,
			'width' 			=> 6,
			'description'		=> null,
			'default' 			=> '',			
			'subfields'			=> null,
			'clone_controls'	=> true,
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'color'		=> array(
		
			'title'				=> 'Color',					
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> null,
			'width' 			=> 6,			
			'description'		=> null,
			'subfields'			=> null,
			'default'			=> '#FFFFFF',									
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),	
		'date'		=> array(
			'title'				=> 'Date',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 8,	
			'width' 			=> 6,
			'date_format'		=> 'mm/dd/yy', // see http://docs.jquery.com/UI/Datepicker/formatDate for options		
			'date_format_php'	=> 'D, M jS',
			'default' 			=> '',			
			'clone_controls'	=> true	, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'datetime'		=> array(
			'title'				=> 'Date/Time',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 19,
			'width' 			=> 6,	
			'date_format'		=> 'yy/mm/dd', // see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',
			'default' 			=> '',		
			'clone_controls'	=> true	 , 
			'sort'				=> true	, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'divider' => array(
			'title'				=> 'Divider',
			'layout'			=> array('label', 'field', 'description'),
			'size'				=> null,
			'width' 			=> 6,
			'description'		=> null,
		),		
		'group'		=> array(
			'title'			=> 'Group',		
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'size'			=> null,
			'width' 		=> 6,			
			'description'	=> null,
			'subfields'		=> null,
			'default' 		=> '',			
			'clone_controls'=> true	,
			'sort'			=> true, 
			'required' 		=> false,
			'validate' 		=> false,
			'error'			=> false			
		),
		'info'		=> array(
			'title'			=> 'Info',
			'layout'		=> array('label', 'field' , 'description'),
			'description'	=> null,
			'width' 		=> 6,	
		),
		'select'	=> array(
			'title'			=> 'Select Menu',				
			'multiple'		=> false,
			'options'		=> 'page',
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'size'			=> 30,
			'width' 		=> 6,			
			'description'	=> null,
			'default' 		=> '',			
			'clone_controls'=> true	,
			'sort'			=> true, 
			'required' 		=> false,
			'validate' 		=> false,
			'error'			=> false			
		),
		'startend'		=> array(
			'title'				=> 'Start/End',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 19,
			'width' 			=> 6,	
			'field_type' 		=> 'date', // date,time, datetime
			'date_format'		=> 'mm-dd-yy', // see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',
			'start_label' 		=> 'Start',
			'end_label' 		=> 'End',
			'default' 			=> '',		
			'clone_controls'	=> true	 , 
			'sort'				=> true	, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'text' => array(
			'title'				=> 'Text Input',						
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'width' 			=> 6,	
			'default' 			=> '',					
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'textarea' => array(
			'title'			=> 'Textarea',
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'description'	=> null,
			'rows'			=> 3,
			'cols'			=> 57,
			'width' 		=> 6,
			'default' 		=> '',							
			'clone_controls'=> true, 
			'sort'			=> true, 
			'required' 		=> false,
			'validate' 		=> false,
			'error'			=> false			
		), 		
		
		'time' => array(
			'title'			=> 'Time',						
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'description'	=> null,
			'size'			=> 6,	
			'width' 		=> 6,
			'time_format' 	=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',			
			'default' 		=> '',			
			'clone_controls'=> true, 
			'sort'			=> true, 
			'required' 		=> false,
			'validate' 		=> false,
			'error'			=> false			
		),
		'toggle' => array(
			'title'				=> 'Toggle',
			'layout'			=> array(array( 'field' , 'error','label' ), 'description'),
			'checkbox_value' 	=> 1,
			'size'				=> null,
			'width' 			=> 6,
			'description'		=> null,
			'show' 				=> false,
			'hide'				=> false,
			'default' 			=> '',			
			'subfields'			=> null,
			'clone_controls'	=> true,
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),			
	),

); 

// fallback defaults for every possible option should be here. 

global $cloud_forms_defaults_wp ; 
$cloud_forms_defaults_wp = array (

	'top_level' => array (
		'image'		=> 'IMAGE_URL', 
		'priority'	=> 52,
		'subpages'	=> array (
		)
	), 
	'subpages'	=> array (
		'menu_title'	=> 'Default',
		'capability'	=> 'create_users',
		'_has_settable_defaults' => false,
	), 
	
	'sections'	=> array (
		'context' => 'normal', 
		'priority' => 'low' , 
		'_has_settable_defaults' => false,			
	),
	
	'fields'	=> array (
		'general' => array(
			'editor_list'		=> false,
			'settable_defaults' => false,
			'code_link' 		=> true, 
			'_lock'				=> false			
		), 
		'checkbox' => array(
			'title'				=> 'Checkbox',
			'layout'			=> array(array( 'field' , 'error','label' ), 'description'),
			'cloneable'			=> false,
			'checkbox_value' 	=> 1,
			'multiple' 			=> false,
			'options' 			=> false,
			'size'				=> null,
			'width' 			=> 6,
			'description'		=> null,
			'editor_list'		=> false,
			'default' 			=> '',			
			'subfields'			=> null,
			'settable_defaults' => false,
			'code_link' 		=> true, 
			'clone_controls'	=> true,
			'sort'				=> true, 
			'_lock'				=> false			
		),
		'color'		=> array(
		
			'title'				=> 'Color',					
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> null,
			'width' 			=> 6,			
			'description'		=> null,
			'editor_list'		=> false,
			'subfields'			=> null,
			'settable_defaults' => false,
			'default'			=> '#FFFFFF',									
			'code_link' 		=> true, 
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'_lock'				=> false, 				
		),	
		'date'		=> array(
			'title'				=> 'Date',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 8,	
			'width' 			=> 6,
			'date_format'		=> 'mm/dd/yy', // see http://docs.jquery.com/UI/Datepicker/formatDate for options		
			'date_format_php'	=> 'D, M jS',
			'default' 			=> '',			
			'editor_list'		=> false,			
			'code_link' 		=> true, 
			'clone_controls'	=> true	, 
			'sort'				=> true,
			'_lock'				=> false												
		),		
		'datetime'		=> array(
			'title'				=> 'Date/Time',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 19,
			'width' 			=> 6,	
			'date_format'		=> 'yy/mm/dd', // see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',
			'default' 			=> '',		
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'clone_controls'	=> true	 , 
			'sort'				=> true	,
			'_lock'				=> false				
													
		),
		'group'		=> array(
			'title'			=> 'Group',		
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'size'			=> null,
			'width' 		=> 6,			
			'description'	=> null,
			'subfields'		=> null,
			'default' 		=> '',			
			'editor_list'	=> false,			
			'code_link' 	=> true, 
			'clone_controls'=> true	,
			'sort'			=> true, 
			'_lock'			=> false									
		),
		'info'		=> array(
			'title'			=> 'Info',
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'description'	=> null,
			'width' 		=> 6,	
			'default' 		=> '',								
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true, 
			'_lock'			=> false											
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
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true,
			'use_image'		=> true,
			'image_size' 	=> 'full',
			'_lock'			=> false											
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
		'select'	=> array(
			'title'			=> 'Select Menu',				
			'multiple'		=> false,
			'options'		=> 'page',
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'size'			=> 30,
			'width' 		=> 6,			
			'description'	=> null,
			'default' 		=> '',			
			'editor_list'	=> false,
			'code_link' 	=> true, 
			'clone_controls'=> true	,
			'sort'			=> true,
			'_lock'			=> false											
		),
		'startend'		=> array(
			'title'				=> 'Start/End',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 19,
			'width' 			=> 6,	
			'field_type' 		=> 'date', // date,time, datetime
			'date_format'		=> 'mm-dd-yy', // see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',
			'start_label' 		=> 'Start',
			'end_label' 		=> 'End',
			'default' 			=> '',		
			'editor_list'		=> false,
			'code_link' 		=> true, 
			'clone_controls'	=> true	 , 
			'sort'				=> true	,
			'_lock'				=> false				
													
		),		
		'text' => array(
			'title'				=> 'Text Input',						
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'width' 			=> 6,	
			'default' 			=> '',					
			'editor_list'		=> false,			
			'code_link' 		=> true, 
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'_lock'				=> false						
		),
		'textarea' => array(
			'title'			=> 'Textarea',
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
		
		'time' => array(
			'title'			=> 'Time',						
			'layout'		=> array('label', 'field' , 'error', 'description'),
			'cloneable'		=> false,
			'description'	=> null,
			'size'			=> 6,	
			'width' 		=> 6,
			'time_format' 	=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',			
			'default' 		=> '',			
			'editor_list'	=> false,			
			'code_link' 	=> true, 
			'clone_controls'=> true, 
			'sort'			=> true,
			'_lock'			=> false				
		),
		'toggle' => array(
			'title'				=> 'Toggle',
			'layout'			=> array(array( 'field' , 'error','label' ), 'description'),
			'checkbox_value' 	=> 1,
			'size'				=> null,
			'width' 			=> 6,
			'description'		=> null,
			'show' 				=> false,
			'hide'				=> false,
			'editor_list'		=> false,
			'default' 			=> '',			
			'subfields'			=> null,
			'settable_defaults' => false,
			'code_link' 		=> true, 
			'clone_controls'	=> true,
			'sort'				=> true, 
			'_lock'				=> false			
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

