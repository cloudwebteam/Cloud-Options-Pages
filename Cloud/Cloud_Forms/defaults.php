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

