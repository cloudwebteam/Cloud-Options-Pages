<?php

// fallback defaults for every possible option should be here. 

global $cloud_form_defaults; 
$cloud_form_defaults = array (
	'forms'				=> array (
		'title'				=> 'Default Page Title',
		'layout'			=> 'standard',
		'description'		=> null,
		'ajax'				=> false,
		'submit_text'		=> 'Save',
        'hide_on_success'	=> false, // show the form once successfully submissi
		'success'			=> 'Form successfully validated and sent',
		'success_function'	=> false,
		'success_function_js' => false, // the name of a javascript function to call on successful submit
		'sections'			=> array (
		)	
	), 
	
	'sections'			=> array(
		'title'				=> 'Default Section Title',
		'layout'			=> 'standard',
		'description'		=> null,
		'fields'			=> array ( 
		)
	),
	
	'fields'			=> array (
		'general' 			=> array(
			'title'				=> 'Default Field Title',
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> null,
			'description'		=> null,
			'disabled' 			=> false,
			'default' 			=> '',			
			'subfields'			=> null,
			'clone_controls'	=> true,
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		), 
		'checkbox' 			=> array(
			'title'				=> 'Checkbox',
			'layout'			=> array(array( 'field' , 'error','label' ), 'description'),
			'cloneable'			=> false,
			'checkbox_value' 	=> 1,
			'multiple' 			=> false,
			'options' 			=> false,
			'size'				=> null,
			'description'		=> null,
			'disabled' 			=> false,
			'default' 			=> '',			
			'subfields'			=> null,
			'clone_controls'	=> true,
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'color'				=> array(
			'title'				=> 'Color',					
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> null,
			'description'		=> null,
			'subfields'			=> null,
			'disabled' 			=> false,
			'default'			=> '#FFFFFF',									
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),	
		'date'				=> array(
			'title'				=> 'Date',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 8,	
			'date_format'		=> 'mm/dd/yy', // see http://docs.jquery.com/UI/Datepicker/formatDate for options		
			'date_format_php'	=> 'D, M jS',
			'min_date' 			=> false,
			'max_date' 			=> false,
			'disabled' 			=> false,
			'default' 			=> '',			
			'clone_controls'	=> true	, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'datetime'			=> array(
			'title'				=> 'Date/Time',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 19,
			'date_format'		=> 'yy/mm/dd', // see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',
			'disabled' 			=> false,
			'default' 			=> '',		
			'clone_controls'	=> true	 , 
			'sort'				=> true	, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'divider' 			=> array(
			'title'				=> 'Divider',
			'layout'			=> array('label', 'field', 'description'),
			'size'				=> null,
			'description'		=> null,
		),		
		'group'				=> array(
			'title'				=> 'Group',		
			'layout'			=> array('label', 'description', 'field' , 'error' ),
			'cloneable'			=> array(	
				'min' 				=> 0,
				'max' 				=> false, 
				'zero_text' 		=> 'None created. Add the first.'
			),
			'size'				=> null,
			'description'		=> null,
			'subfields'			=> null,
			'default' 			=> '',			
			'clone_controls'	=> true	,
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'info'				=> array(
			'title'				=> 'Info',
			'layout'			=> array('label', 'field' , 'description'),
			'description'		=> null,
		),
		'map' 				=> array(
			'title'				=> 'Map Input',						
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'width' 			=> 400,
			'height'			=> 300,	
			'api_key' 			=> false,
			'default' 			=> '',					
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'number' 			=> array(
			'title'				=> 'Number',						
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 8,	
			'min' 				=> 0, 
			'max' 				=> false,
			'disabled' 			=> false,
			'default' 			=> '',					
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'password' 			=> array(
			'title' 			=> 'Password', 
			'layout'			=> array('label', 'field', 'description', 'error' ),
			'password_label' 	=> 'Password',			
			'confirm_label' 	=> 'Confirm', 
			'size' 				=> 50,
			'description'		=> null,
			'disabled' 			=> false,			
			'size'				=> 55,	
			'required' 			=> false,
			'validate' 			=> '/[a-zA-Z0-9]+/',
			'error'				=> false,
			'confirm' 			=> false, 
			'confirm_error' 	=> array(
				'empty' 		=> 'Please confirm', 
				'error'			=> 'Does not match'
			)
		),
		'radio'				=> array(
			'title'				=> 'Radio Group',				
			'multiple'			=> false,
			'use_query' 		=> false, 
			'options'			=> 'page',
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> 30,
			'description'		=> null,
			'disabled' 			=> false,
			'default' 			=> '',			
			'clone_controls'	=> true	,
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'range_slider' 		=> array( 
			'title'				=> 'Text Input',						
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'disabled' 			=> false,
			'default' 			=> '',	
			'min' 				=> 0, 
			'max'				=> 100,
			'step' 				=> 1,				
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
			
		), 
		'select'			=> array(
			'title'				=> 'Select Menu',				
			'multiple'			=> false,
			'use_query' 		=> false, 
			'options'			=> 'page',
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> 30,
			'description'		=> null,
			'disabled' 			=> false,
			'first_option' 		=> array(
				'value' 			=> '', 
				'text' 				=> 'Please select one...'
			),
			'default' 			=> '',			
			'clone_controls'	=> true	,
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'startend'			=> array(
			'title'				=> 'Start/End',		
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 19,
			'field_type' 		=> 'date', // date,time, datetime
			'date_format'		=> 'mm-dd-yy', // see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',
			'start_label' 		=> 'Start',
			'end_label' 		=> 'End',
			'disabled' 			=> false,
			'default' 			=> '',		
			'clone_controls'	=> true	 , 
			'sort'				=> true	, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'text' 				=> array(
			'title'				=> 'Text Input',						
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'disabled' 			=> false,
			'default' 			=> '',					
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'textarea' 			=> array(
			'title'				=> 'Textarea',
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'rows'				=> 3,
			'cols'				=> 57,
			'disabled' 			=> false,
			'default' 			=> '',							
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		), 		
		
		'time' 				=> array(
			'title'				=> 'Time',						
			'layout'			=> array('label', 'field' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 6,	
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',			
			'disabled' 			=> false,
			'default' 			=> '',			
			'clone_controls'	=> true, 
			'sort'				=> true, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'toggle' 			=> array(
			'title'				=> 'Toggle',
			'layout'			=> array( 'label', array( 'field', 'error' ), 'description'),
			'checkbox_value' 	=> 1,
			'size'				=> null,
			'description'		=> null,
			'show' 				=> false,
			'hide'				=> false,
			'field' 			=> 'checkbox', // checkbox or radio
			'options' 			=> false,
			'disabled' 			=> false,
			'toggle_type' 		=> 'checkbox',
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

