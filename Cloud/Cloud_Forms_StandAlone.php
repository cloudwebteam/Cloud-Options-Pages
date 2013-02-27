<?php 
class Cloud_Forms_StandAlone extends Cloud_Forms {
	protected static $forms;
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
			self::$forms[ $key ] = $array;
		}
	}	
}
Cloud_Forms_StandAlone::add_form( array(
	'test' => array(
		'fields' => array(
			'one' => array()
		)
	)
)  );

