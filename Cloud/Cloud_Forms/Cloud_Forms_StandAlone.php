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
		$this->order_styles_and_scripts(); 		
		$this->print_styles() ;
		$this->print_scripts() ;
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

	protected function order_styles_and_scripts(){
		array_walk( self::$styles, array( $this, 'filter_out_styles_without_needed_dependencies'), &self::$styles );
		usort( self::$styles, array( $this, 'sort_array_by_dependencies') );	
		
		array_walk( self::$scripts, array( $this, 'filter_out_scripts_without_needed_dependencies'), &self::$scripts );
		usort( self::$scripts, array( $this, 'sort_array_by_dependencies') );		
	} 
	protected function print_styles(){
		foreach( self::$styles as $style ){ ?>
		<link rel="stylesheet" href="<?php echo $style['path']; ?>" />
		<?php }
	}
	protected function print_scripts(){
		foreach( self::$scripts as $script ){ ?>
		<script src="<?php echo $script['path']; ?>" ></script>
		<?php }
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
		$forms = array();
		foreach( $this->spec as $form_slug => $form_spec ){
			$layout = Layout_Form::get_layout_function( $form_spec['layout'] );
			ob_start();
				Layout_Form::$layout( $form_slug, $form_spec );
			$forms[ $form_slug ] = ob_get_clean();
		}
		return $forms; 
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
}

