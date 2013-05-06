<?php 
class Cloud_Forms_StandAlone extends Cloud_Forms {
	protected $forms = array(); 
	protected $directories_to_load = array('Field');	
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
	// all this does is add a 'validation_error' item to the field specs of those fields that failed to validate, and return a general success or failure message	
	protected function validate_forms(){
		foreach( $this->spec as $form_slug => $form_array ){
			if ( $this->validation_enabled && isset( $_POST['form_id'] ) && $_POST['form_id'] == $form_slug ){
				$validation_results = Validator::validate( $_POST, $form_array )  ;			
				$this->has_validation_errors = $validation_results['success'] ? false : true ;

				if ( isset( $this->spec[ $form_slug][ 'sections' ] ) ){
					$this->spec[ $form_slug ][ 'sections' ] = $validation_results['updated_form_spec']; 
				} else {
					$this->spec[ $form_slug ][ 'fields' ]  = $validation_results['updated_form_spec']; 				
				}
			}		
		}
	
	}
	/***====================================================================================================================================
			LOAD SCRIPTS AND STYLES
		==================================================================================================================================== ***/
	protected function set_local_javascript_vars(){
		$this->global_js_vars = array( 
			'ajax_url' => self::$dir . '/ajax/standAlone.php',
			'cloud_url' => self::$dir
		); 
	}
	protected function order_styles_and_scripts(){
		array_walk( self::$styles, array( $this, 'filter_out_styles_without_needed_dependencies'), &self::$styles );
		self::$styles = $this->sort_array_by_dependencies( self::$styles );	
		
		array_walk( self::$scripts, array( $this, 'filter_out_scripts_without_needed_dependencies'), &self::$scripts );
		self::$scripts = $this->sort_array_by_dependencies( self::$scripts );	
			
	} 
	public function print_styles(){
		foreach( self::$styles as $style ){ ?>
		<link rel="stylesheet" href="<?php echo $style['path']; ?>" />
		<?php }
	}
	public function print_scripts(){ 
		if ( $this->global_js_vars ){ ?>
		<script>
			/* <! [CDATA[ */
			var cloud = <?php echo json_encode( $this->global_js_vars ) ; ?>;
			/* []> */			
		</script>
		<?php }
		
		foreach( self::$scripts as $script ){ ?>
		<script src="<?php echo $script['path']; ?>" ></script>
		<?php }
	}
	/***====================================================================================================================================
			HANDLE ADDING FORMS
		==================================================================================================================================== ***/

	protected function construct_forms(){
		$forms = array();
		foreach( $this->spec as $form_slug => $form_spec ){
			
			if ( isset( $form_spec['sections'] ) ){

				$layout = Layout_Form::get_layout_function( $form_spec['layout'] );
				$forms[ $form_slug ] = Layout_Form::$layout( $form_slug, $form_spec ); 
			} else {
				$forms[ $form_slug ] = Layout_Section::standAlone( $form_slug, $form_spec ); 
			}
			
		}
		return $forms; 
	}
	public function register( $arg1, $arg2 = false ){
		if ( is_array( $arg1 ) ){
			foreach( $arg1 as $form_slug => $form ){
				$this->passed_in[ $form_slug ] = $form;
				$this->spec[ $form_slug ] = $this->merge_with_defaults( $form_slug, $form );
			}
		} else {
			$form_slug = $arg1; 
			$form = $arg2; 
			$this->passed_in[ $form_slug ] = $arg2;
			$this->spec[ $form_slug ] = $this->merge_with_defaults( $form_slug, $form );
		}
	}	
	public function head(){
		if ( $this->spec ){
			$this->validate_forms() ;	
			$this->forms = $this->construct_forms(); 
			$this->order_styles_and_scripts();
			$this->print_styles();
			$this->print_scripts(); 
		}
	}
	public function display( $form_slug ){
		echo $this->forms[ $form_slug ] ;
	}

	public static function get_folder_url(){
		return self::$dir .'/'. basename( __FILE__, '.php' )  ; 	
	}	
	public static function get_include_path(){
		return self::$ABS .'/'. basename( __FILE__, '.php' ) ; 	
	}
	public function ajax_validate_form(){
		$form_slug = $_POST['ajax_form_id']; 
		$submission_data = $_POST['ajax_form_data'] ;
		parse_str( $submission_data, $submission_data ) ;

		$Forms = Cloud_Forms_StandAlone::get_instance(); 
		if ( isset( $Forms->spec[ $form_slug ] ) ){
			$validation_results = Validator::validate( $submission_data, $Forms->spec[ $form_slug ] );  
			$response = $validation_results ; 
		} else {
			$response = array( 
				'error' => 'Not a valid form name' 
			); 
		}
		echo json_encode( $response );
		die; 
	}
}

