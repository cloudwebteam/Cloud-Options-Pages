<?php

// fallback defaults for every possible option should be here. 

global $cloud_form_defaults; 
$cloud_form_defaults = array (
	'forms'				=> array (
		'title'				=> 'Default Form Title',
		'layout'			=> 'standard', // tabs, tabs_animated, standard
		'description' 		=> null,
		'ajax' 				=> false, // if true, real-time validation and submission is enabled
		'submit_text' 		=> 'Save', // submit button text
        'hide_on_success' 	=> false, // show the form once successfully submission
		'success'       	=> 'Form successfully validated and sent', // success message (on submit)
		'success_function' 	=> false, // PHP function called on succes, receives $form_data as arg, eg. 'my_fnct', or ( 'CLASS', 'my_fnct' )
		'success_function_js' => false, // 'my_fnct', a global javascript function to call on successful submit
		// if a multi-section form
		'sections'			=> array(),
		// if a simple form		
		'fields'			=> array()
	), 
	
	'sections'			=> array(
		'title'				=> 'Default Section Title',
		'layout'			=> 'standard',  // tabs, tabs_animated, standard, <custom HTML>
		'description'		=> null,
		'fields'			=> array()
	),
	
	'fields'			=> array (
		// these fields are used on most fields, and function similarly everywhere.
		'general' 			=> array(
			'title'				=> 'Default Field Title',
			'layout'			=> array('label', 'input' , 'error', 'description'), 
				// the layout and order of the field elements
				// one '.field-row' per array element
				// nest arrays to add multiple items per row. 
					// eg. array( 'label', array( 'input', 'description', 'error' ) ) 
						// will put the label on its own line, and input, description, & error on the second.
				// NOTE unused elements will be appended to the field if the element exists and isn't specificied here. 
			'cloneable'			=> false, // array( 'clone
			// options 
			// 'min' => 0, // 0+
			// 'max' => false, // 1+
			// 'controls' => true, // user can add/subtract?
			// 'sort => true  // user can drag-and-drop to re-order?
			'size'				=> null,
			'description'		=> null,
			'disabled' 			=> false,
			'default' 			=> '',			
			'subfields'			=> null, 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		), 
		'checkbox' 			=> array(
			'title'				=> 'Checkbox',
			'layout'			=> array(array( 'input' , 'error','label' ), 'description'),
			'cloneable'			=> false,
			'checkbox_value' 	=> 1,
			'multiple' 			=> false,
			'options' 			=> false,
			'size'				=> null,
			'description'		=> null,
			'disabled' 			=> false,
			'default' 			=> '',			
			'subfields'			=> null,
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'color'				=> array(
			'title'				=> 'Color',					
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> null,
			'description'		=> null,
			'subfields'			=> null,
			'disabled' 			=> false,
			'default'			=> '#FFFFFF',									
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),	
		'date'				=> array(
			'title'				=> 'Date',		
			'layout'			=> array('label', 'input' , 'error', 'description'),
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
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'datetime'			=> array(
			'title'				=> 'Date/Time',		
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'subfields'			=> null,
			'size'				=> 19,
			'date_format'		=> 'yy/mm/dd', // see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',
			'disabled' 			=> false,
			'default' 			=> '',		
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'divider' 			=> array(
			'title'				=> 'Divider',
			'layout'			=> array('label', 'input', 'description'),
			'size'				=> null,
			'description'		=> null,
		),		
		'group'				=> array(
			'title'				=> 'Group',		
			'layout'			=> array('label', 'description', 'input' , 'error' ),
			'cloneable'			=> array(	
				'min' 				=> 0,
				'max' 				=> false, 
				'zero_text' 		=> 'None created. Add the first.'
			),
			'size'				=> null,
			'description'		=> null,
			'subfields'			=> null,
			'default' 			=> '',			
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'info'				=> array(
			'title'				=> 'Info',
			'layout'			=> array('label', 'input' , 'description'),
			'description'		=> null,
		),
		'map' 				=> array(
			'title'				=> 'Map Input',						
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'width' 			=> 400,
			'height'			=> 300,	
			'api_key' 			=> false,
			'default' 			=> '',					
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'number' 			=> array(
			'title'				=> 'Number',						
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 8,	
			'min' 				=> 0, 
			'max' 				=> false,
			'disabled' 			=> false,
			'default' 			=> '',					
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'password' 			=> array(
			'title' 			=> 'Password', 
			'layout'			=> array('label', 'input', 'description', 'error' ),
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
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> 30,
			'description'		=> null,
			'disabled' 			=> false,
			'default' 			=> '',			
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'range_slider' 		=> array( 
			'title'				=> 'Text Input',						
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'disabled' 			=> false,
			'default' 			=> '',	
			'min' 				=> 0, 
			'max'				=> 100,
			'step' 				=> 1,				
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
			
		), 
		'select'			=> array(
			'title'				=> 'Select Menu',				
			'multiple'			=> false,
			'use_query' 		=> false, 
			'options'			=> 'page',
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> 30,
			'description'		=> null,
			'disabled' 			=> false,
			'first_option' 		=> array(
				'value' 			=> '', 
				'text' 				=> 'Please select one...'
			),
			'default' 			=> '',			
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'startend'			=> array(
			'title'				=> 'Start/End',		
			'layout'			=> array('label', 'input' , 'error', 'description'),
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
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),		
		'text' 				=> array(
			'title'				=> 'Text Input',						
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 55,	
			'disabled' 			=> false,
			'default' 			=> '',					
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'textarea' 			=> array(
			'title'				=> 'Textarea',
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'rows'				=> 3,
			'cols'				=> 57,
			'disabled' 			=> false,
			'default' 			=> '',							
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		), 		
		
		'time' 				=> array(
			'title'				=> 'Time',						
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 6,	
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			'date_format_php'	=> 'D, M jS g:i a',			
			'disabled' 			=> false,
			'default' 			=> '',			
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		'toggle' 			=> array(
			'title'				=> 'Toggle',
			'layout'			=> array( 'label', array( 'input', 'error' ), 'description'),
			'checkbox_value' 	=> 1,
			'size'				=> null,
			'description'		=> null,
			'show' 				=> false,
			'hide'				=> false,
			'input' 			=> 'checkbox', // checkbox or radio
			'options' 			=> false,
			'disabled' 			=> false,
			'toggle_type' 		=> 'checkbox',
			'default' 			=> '',			
			'subfields'			=> null,
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),			
	),

); 

