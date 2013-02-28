<?php 
class Cloud_Forms_StandAlone extends Cloud_Forms {
	protected $forms = array(); 
	protected $directories_to_load = array('Field');	

	// singleton get method
	public static function get_instance(){
		if ( !self::$instance ){
			self::$instance = new self(); 
		}
		return self::$instance; 
	}
	protected function init(){
		$this->load_directories( array( 'Layout', 'Field') );
		$this->forms = $this->construct_forms(); 
		

	}
	/***====================================================================================================================================
			CREATING SPEC
		==================================================================================================================================== ***/
	protected function merge_with_defaults(){
		$defaults = $this->defaults; 
		$passed_in = self::$passed_in;  
		$_master = array();
		foreach ( $passed_in as $form_slug => $form ){	
			$_form =& $_master[$form_slug];
			
			foreach ( $defaults['subpages'] as $subpage_slug => $default_value ) {
				if ( isset( $form[$subpage_slug] ) ){
					$set_value = $form[$subpage_slug];
				} else {
					if ( isset ( $top_level_page['defaults']['subpages'][$subpage_slug] ) ) {
						$set_value = $top_level_page['defaults']['subpages'][$subpage_slug];
					} else {
						$set_value = $default_value;
					}					
				}
				$_form[$subpage_slug] = $set_value;
			}						
			foreach ( $form['sections'] as $section_slug => $section){
				$_form['sections'][$section_slug] = array();  
				$_section =& $_form['sections'][$section_slug];	
				$section['subpage_slug'] = $subpage_slug;
				$section['section_slug'] = $section_slug;
				$_section = $this->finish_merge_with_defaults( 'sections', $section, $form ); 
			}
		}
		return $_master;	
	}
	/***====================================================================================================================================
			LOAD SCRIPTS AND STYLES
		==================================================================================================================================== ***/
	protected function load_global_scripts(){
		// use $this->enqueue_script();
	}
	protected function load_global_styles(){	
		// use $this->enqueue_style();	
	}	
	/***====================================================================================================================================
			HANDLE ADDING FORMS
		==================================================================================================================================== ***/
	public static function add_form( $array ){
		foreach( $array as $key => $array){
			self::$passed_in[ $key ] = $array;
		}
	}
	protected function construct_forms(){
		foreach( $this->spec as $form_slug => $form_spec ){
			$layout = Layout_Form::get_layout_function( $form_spec['layout'] );
			Layout_Form::$layout( $form_slug, $form_spec );
		}
	}
	public function display( $form_slug ){
		echo $this->forms[ $form_slug ] ;
	}
	
}

