<?php

// fallback defaults for every possible option should be here. 

global $cloud_form_defaults; 
$cloud_form_defaults = array (
	'forms'				=> array (
		'title'				=> 'Default Form Title',
		'layout'			=> 'standard', // tabs, tabs_animated, standard, table<custom html> 
										// depending if form is simple or multi-section, insert fields as [field_slug] or sections as [section_slug] 
		'header_layout'		=> false, // <custom html> (insert [title] and [description] where desired )
		'footer_layout' 	=> false, // <custom html> (insert [submit] and [description] where desired )
		'description' 		=> null,
		'ajax' 				=> false, // if true, real-time validation and submission is enabled
		'submit_text' 		=> 'Save', // submit button text
        'hide_on_success' 	=> false, // show the form once successfully submission
		'success'       	=> 'Form successfully validated and sent', // success message (on submit)
		'success_function' 	=> false, // PHP function called on succes, receives $form_data as arg, eg. 'my_fnct', or ( 'CLASS', 'my_fnct' )
		'success_function_js' => false, // 'my_fnct', a global javascript function to call on successful submit
		'maps_api_key' 			=> false, // a google maps API key (if using maps)
		// if a multi-section form
		'sections'			=> array(),
		// if a simple form		
		'fields'			=> array()
	), 
	
	'sections'			=> array(
		'title'				=> 'Default Section Title',
		'layout'			=> 'standard',  // tabs, tabs_animated, standard, <custom HTML> ( insert fields as [field_slug] ); 
		'header_layout'		=> false, // <custom html> (insert [title] and [description] where desired )
		'description'		=> null,
		'fields'			=> array()
	),
	
	'fields'			=> array (
		// these fields are used on most fields, and function similarly everywhere.
		'general' 			=> array( // NOTE: If string provided, defaults to text input with string as the title.
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
			'size'				=> null, // set the size attribute on the input (text, textarea, etc )
			'description'		=> null,
			'disabled' 			=> false, // disable the input 
			'default' 			=> '', // set a default value, if no data has been saved (and is not set in $_POST after submit )
			//VALIDATION OPTIONS
			'required' 			=> false, // bool or string, A string will be used as the error message that shows up.
			'validate' 			=> false, // string. Validation type. EG. 'email', 'zip', 'phone', 'pin', OR a regex pattern ( '/[A-Z]{3,}/' )
			'error'				=> false, // string. Error to show on validation error. A falsy value will show the default message.
		), 
		'checkbox' 			=> array(
			'title'				=> 'Checkbox',
			'layout'			=> array(array( 'input' , 'error','label' ), 'description'),
			'cloneable'			=> false,
			'checkbox_value' 	=> 1, // string, int, bool. what value is sent when the checkbox is checked
			'multiple' 			=> false, // bool. if true, provide an array to 'options'
			'options' 			=> false, 	// array. Required 'multiple' to be set to true. 
											// NOTE: array keys will be used as value
			'description'		=> null,
			'disabled' 			=> false,
			'default' 			=> '',
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
			'disabled' 			=> false,
			'default'			=> '#FFFFFF', // should be a hex color code
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),	
		'date'				=> array(
			'title'				=> 'Date',		
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'size'				=> 8,	
			'default' 			=> '',		
			'disabled' 			=> false,				
			// see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'date_format'		=> 'mm/dd/yy',
			'min_date' 			=> false,
			'max_date' 			=> false,			
			// what format to retrieve when using get_theme_options or get_metabox_options
			// see http://php.net/manual/en/function.date.php for options
			'date_format_php'	=> 'D, M jS', 
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
			'disabled' 			=> false,
			'default' 			=> '',			
			'date_format'		=> 'yy/mm/dd',	// see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			// what format to retrieve when using get_theme_options or get_metabox_options
			// see http://php.net/manual/en/function.date.php for options
			'date_format_php'	=> 'D, M jS g:i a', 
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		// a text heading to divide sections of the form (no input)
		'divider' 			=> array(
			'title'				=> 'Divider',
			'layout'			=> array('label', 'input', 'description'),
			'size'				=> null,
			'description'		=> null,
		),	
		'file' 			=> array(
			'title'				=> 'File Upload',
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'description'		=> null,
			'disabled' 			=> false,
			'default' 			=> '',							
			'upload_as_media' 	=> true,
			'allowed_extensions' => false,
			'upload_dir'		=> Cloud_ABS .'/uploads', 	
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
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
			'subfields'			=> array(), // an array of fields identical to what 'fields' accepts.
			'default' 			=> '',			
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		// text block helper text (no input)
		'info'				=> array(
			'title'				=> 'Info',
			'layout'			=> array('label', 'input' , 'description'),
			'description'		=> null,
		),
		'map' 				=> array(
			'title'				=> 'Map Input',						
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'description'		=> null,
			'width' 			=> 400, // int. in pixels
			'height'			=> 300,	// int. in pixels
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
			'size' 				=> 50,
			'description'		=> null,
			'disabled' 			=> false,			
			'size'				=> 55,	
			'required' 			=> false,
			'validate' 			=> '/[a-zA-Z0-9]+/', // regex to check password against
			'error'				=> false, // error message to show
			'confirm' 			=> false, // require a password confirmation (and obviously show the confirmation field and validate it)						
			'confirm_label' 	=> 'Confirm', // if 'confirm' is true			
			'confirm_error' 	=> array( // errors if confirmation fails
				'empty' 		=> 'Please confirm', // if no confirmation provided
				'error'			=> 'Does not match' // if 
			)
		),
		'radio'				=> array(
			'title'				=> 'Radio Group',				
			'multiple'			=> false, // bool.
			'use_query' 		=> false, // bool. If true, pass a WP query array (same as get_posts( $query ) ) to options
			'options'			=> 'page', // array. Requires multiple to be true.
											// array of options (keys as value to save)
											// OR a WP query array, if 'use_query' => true
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
		// a jquery-UI slider
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
		// a dropdown menu
		'select'			=> array(
			'title'				=> 'Select Menu',				
			'multiple'			=> false, // bool. allow multiple items to be selected
			'use_query' 		=> false, // bool. If true, pass a WP query array (same as get_posts( $query ) ) to options
			'options'			=> 'page', // string, array. 
											// if string, options : POST_TYPE, TAXONOMY_SLUG, or 'states'
											// if array:  
												// array of options (keys as value to save)
												// OR a WP query array, if 'use_query' => true											
			'layout'			=> array('label', 'input' , 'error', 'description'),
			'cloneable'			=> false,
			'size'				=> 30,
			'description'		=> null,
			'disabled' 			=> false,
			'first_option' 		=> array( // bool, array. false for no extra first option
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
			'field_type' 		=> 'date', // date, time, datetime
			'date_format'		=> 'yy/mm/dd',	// see http://docs.jquery.com/UI/Datepicker/formatDate for options
			'time_format' 		=> 'h:mm tt',	// see http://trentrichardson.com/examples/timepicker/ for options
			// what format to retrieve when using get_theme_options or get_metabox_options
			// see http://php.net/manual/en/function.date.php for options
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
			'size'				=> 35,	
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
			'rows'				=> 3, // rows attr of textarea
			'cols'				=> 57, // cols attr of textarea
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
			// what format to retrieve when using get_theme_options or get_metabox_options
			// see http://php.net/manual/en/function.date.php for options
			'date_format_php'	=> 'D, M jS g:i a', 
			'disabled' 			=> false,
			'default' 			=> '',			
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),
		// show/hide other fields based on one field
		'toggle' 			=> array(
			'title'				=> 'Toggle',
			'layout'			=> array( 'label', array( 'input', 'error' ), 'description'),
			'checkbox_value' 	=> 1, // value to save to DB on check, only if options is string
			'size'				=> null, 
			'description'		=> null,
			'disabled' 			=> false, 
			'default' 			=> '',	
						
			'toggle_type' 		=> 'checkbox', // checkbox, radio, select, or text (can't be multiple)												
			'options' 			=> false, // array, string. Array if multiple is desired. Can't be used with input => 'text'			
			'show' 				=> false, // array. If selected, show these fields. Otherwise hide them. 
			'hide'				=> false, // array. If selected, hide these fields. Otherwise show them.
											// eg array( 'next_field_slug' )
											// NOTE, if multiple options
												// array( 
												//	'option_slug1' => array( 'next_field_slug'),
												//	'option_slug2' => array( 'another_field_slug'),
												// )
			'required' 			=> false,
			'validate' 			=> false,
			'error'				=> false			
		),			
	),

); 

