<?php 
class Cloud_Forms_StandAlone extends Cloud_Forms {
	protected $forms = array(); 
	protected $forms_html = array(); 
	protected $validation_enabled = true; 
	protected $has_validation_errors = false;
	// singleton get method
	public static function get_instance(){
		if ( !self::$instance ){
			self::$instance = new self(); 
		}
		return self::$instance; 
	}
	protected function init(){	
	}
	/***====================================================================================================================================
			CREATING SPEC
		==================================================================================================================================== ***/
	protected function merge_with_defaults( $form_slug, $form ){
		$defaults = $this->defaults; 
		$_form = array() ;
		
		// if it has sub-sections, deal with it as a complex form
		if ( isset( $form['sections'] ) ){
			foreach ( $defaults['forms'] as $subpage_slug => $default_value ) {
				if ( isset( $form[$subpage_slug] ) ){
					$set_value = $form[$subpage_slug];
				} else {
					if ( isset ( $top_level_page['defaults']['forms'][$subpage_slug] ) ) {
						$set_value = $top_level_page['defaults']['forms'][$subpage_slug];
					} else {
						$set_value = $default_value;
					}					
				}
				$_form[$subpage_slug] = $set_value;
			}						
			foreach ( $form['sections'] as $section_slug => $section){
				$_form['sections'][$section_slug] = array();  
				$_section =& $_form['sections'][$section_slug];	
				$section['form_slug'] = $form_slug;
				$section['section_slug'] = $section_slug;
				$_section = $this->finish_merge_with_defaults( $section, $form ); 
			}
		
		// if it doesn't, just proceed with a simple form. 
		} else {		
			$form['form_slug'] = $form_slug ; 
			// merge in the defaults' 'forms' array, and unset the sections (since obviously this form has no sections)
			foreach ( $defaults['forms'] as $option_key => $default_value ) {
				if ( isset( $form[$option_key] ) ){
					$set_value = $form[$option_key] ;
				} else {
					$set_value = $default_value;
				}
				$_form[$option_key] = $set_value;
			}			
			$_form = array_merge( $_form, $this->finish_merge_with_defaults( $form ) ); 
			unset( $_form['sections'] ); 
		}		
		return $_form;	
	}

	/***====================================================================================================================================
			LOAD SCRIPTS AND STYLES
		==================================================================================================================================== ***/
	protected function set_local_javascript_vars(){
		self::$loader->add_global_js_var( 'cloud', array(
			'cloud_ajax' => self::$dir . '/ajax/StandAlone.php',
			'cloud_url' => self::$dir
		) ); 
	}
	protected function get_needed_field_scripts_and_styles(){
		function run_field_enqueue_function( $item, $key ){
			if ( $key === 'type' ){
				$field_type = $item ; 
				$field_classname = Cloud_Field::get_class_name( $field_type );
				$field_classname::enqueue_scripts_and_styles( $field_type ) ;	// only this early to get them in Wordpress's queue early enough
			}
		}
		if ( $this->forms ) {
			array_walk_recursive( $this->forms, 'run_field_enqueue_function' ); 
		}

	}		

	protected function validate_spec(){
		foreach( $this->forms as $form_slug => $form ){
			$this->validate_form( $form_slug ); 
		}
	}	

	/***====================================================================================================================================
			HANDLE ADDING FORMS
		==================================================================================================================================== ***/

	protected function construct_forms(){
		$forms = array();
		foreach( $this->forms as $form_slug => $form_spec ){
			if ( isset( $form_spec['sections'] ) ){
				$layout = Layout_Form::get_layout_function( $form_spec['layout'] );
				$forms[ $form_slug ] = Layout_Form::$layout( $form_slug, $form_spec ); 
			} else {
				$forms[ $form_slug ] = Layout_Section::standAlone( $form_slug, $form_spec ); 
			}
			
		}
		return $forms; 
	}
	public function add_forms( $arg1, $arg2 = false ){

		if ( is_array( $arg1 ) ){
			foreach( $arg1 as $form_slug => $form ){
				$this->passed_in[ $form_slug ] = $form;
				$this->forms[ $form_slug ] = $this->merge_with_defaults( $form_slug, $form );
			}
		} else {
			$form_slug = $arg1; 
			$form = $arg2; 
			$this->passed_in[ $form_slug ] = $arg2;
			$this->forms[ $form_slug ] = $this->merge_with_defaults( $form_slug, $form );
		}
        $this->validate_form( $form_slug ) ; // checks if this form has been submitted, adds validation properties
	}	
	public function head(){	
		if ( $this->forms ){
			$this->get_needed_field_scripts_and_styles();
		
			$this->forms_html = $this->construct_forms();
			$this->print_styles();
			$this->print_scripts(); 
		} 
	}
	public function display( $form_slug ){
		if ( isset( $this->forms_html[ $form_slug ] ) ){
			echo $this->forms_html[ $form_slug ] ;
		} else { ?>
			<div class="cloud cloud-form form-not-found" id="form-<?php echo $form_slug; ?>" >Form "<?php echo $form_slug; ?>" has not been registered</div>
		<?php }
	}

	public static function get_folder_url(){
		return self::$dir .'/'. basename( __FILE__, '.php' )  ; 	
	}	
	public static function get_include_path(){
		return self::$ABS .'/'. basename( __FILE__, '.php' ) ; 	
	}

}
