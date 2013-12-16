<?php global $Forms;
$test_options = array(
	'one' => 'Option 1',
	'two' => 'Option 2',
	'three' => 'Option 3'
);
function get_options_fields( $fields = false, $defaults = array() ){
	global $test_options;
	$all_fields = array(
		'checkbox' => array(
			'type' => 'checkbox'
		),
		'checkbox_multiple' => array(
			'title' => 'Checkbox with Multiple Options',
			'type' => 'checkbox', 
			'options' => $test_options
		),
		'color' => array(
			'type' => 'color'
		),
		'date' => array(
			'type' => 'date'
		),
		'datetime' => array( 
			'type' => 'datetime'
		),
		'divider' => array(
			'type' => 'divider'
		),
		'file' => array(
			'type' => 'file'
		),
		'group' => array(
			'type' => 'group', 
			'subfields' => array( 
				'text' => 'Text'
			)
			// 'subfields' => get_options_fields_minus( array( 'group' ) )
		), 
		'hidden' => array(
			'type' => 'hidden'
		),
		'info' => array(
			'type' => 'info'
		),
		'map' => array(
			'type' => 'map'
		),
		'media' => array(
			'type' => 'media'
		),
		'number' => array(
			'type' => 'number'
		),
		'password' => array(
			'type' => 'password'
		),
		'post' => array(
			'type' => 'post'
		),
		'radio' => array(
			'type' => 'radio',
			'options' => $test_options
		),
		'range_slider' => array(
			'type' => 'range_slider'
		),
		'select' => array(
			'type' => 'select', 
			'options' => $test_options
		),
		'startend' => array(
			'type' => 'startend',
		),
		'text' => 'Text',
		'textarea' => array(
			'type' => 'textarea'
		),
		'time' => array(
			'type' => 'time'
		),
		'toggle' => array(
			'type' => 'toggle',
			'show' => array( 'toggle_test')
		),
		'toggle_test' => 'Should hide if toggle not checked.',
		'wysiwyg' => array( 
			'type' => 'wysiwyg'
		)
	);
	if ( is_array( $fields ) ){
		$return_fields = array();
		foreach( $fields as $slug ){
			if ( isset( $all_fields[ $slug ] ) ){
				$return_fields[ $slug ] = $all_fields[ $slug ];
			}
		}
		return merge_field_defaults( $return_fields, $defaults );
	} else {
		return merge_field_defaults( $all_fields, $defaults ); 
	}
}
function merge_field_defaults($fields, $defaults){
	foreach( $fields as $key => $field ){
		if ( isset( $defaults[$key] )){
			$fields[$key]['default'] = $defaults[$key];
		}
	}
	return $fields;
}
function get_options_fields_minus( $fields = array(), $defaults = array() ){
	$all_fields = get_options_fields();
	foreach( $fields as $slug ){
		if ( isset( $all_fields[ $slug ] ) ){
			unset( $all_fields[ $slug ] );
		}
	}
	return merge_field_defaults( $all_fields, $defaults );	
}
$Forms->add_pages( array(
	'options-test' => array(
		'title' => 'Options Test',
		'priority' => 51,
		'image' => 'dashicons-hammer', // gear
		'subpages' => array(
			'options-test' => array(
				'title' => 'Options Test (w sections), with description',
				'menu_title' => 'Test w sections',
				'description' => 'My special page description goes here.',
				'layout' => 'tabs',
				'sections' => array(
					'first' => array(
						'title' => 'First Section, all fields',
						'description' => 'My section description',
						'fields' => get_options_fields()
					),
					'second' => array(
						'title' => 'Second Section, no description',
						'fields' => get_options_fields( array( 'text', 'textarea', 'wysiwyg') )
					)
				)
			),
			'options-test-2' => array(
				'title' => 'Subpage Test (w sections)',
				'menu_title' => 'Subpage w sections',
				'sections' => array(
					'first' => array(
						'title' => 'First Section, all fields',
						'description' => 'My section description',
						'fields' => get_options_fields()
					),
					'second' => array(
						'title' => 'Second Section, no description',
						'fields' => get_options_fields( array( 'text', 'textarea', 'wysiwyg') )
					)
				)
			)
		)
	),
	'options-no-sections-test' => array(
		'title' => 'Options No Sections Test',
		'image' => 'dashicons-admin-generic',
		'subpages' => array(
			'options-no-sections-test' => array(
				'title' => 'Options Test (no sections), with description',
				'description' => 'My subpage description',
				'menu_title' => 'Test w/o sections',
				'fields' => get_options_fields()
			),
			'options-test-2' => array(
				'title' => 'Subpage Test (no sections)',
				'menu_title' => 'Subpage w/o sections',
				'fields' => get_options_fields( array( 'text', 'textarea', 'wysiwyg') )
			)
		)
	)
));
