<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_startend extends Cloud_Field {
	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$this->size = isset( $this->spec['size'] ) ? $this->spec['size'] : ''; 	


		$this->field_type = isset( $this->spec['field_type'] ) ? $this->spec['field_type'] : 'date' ;
		$this->date_format = isset( $this->spec['date_format'] ) ?  $this->spec['date_format'] : 'mm/dd/yy'  ;
		$this->time_format = isset( $this->spec['time_format'] ) ?  $this->spec['time_format'] : 'hh:mm tt' ; 

		$this->start_value = isset( $this->info['value']['start'] ) ? $this->info['value']['start'] : '' ;
		$this->start_utc = $this->parse_dynamic_value( $this->start_value );
		$this->end_value = isset( $this->info['value']['end'] ) ? $this->info['value']['end'] : '' ;	
		$this->end_utc = $this->parse_dynamic_value( $this->end_value ) ;	
			
		$this->start_utc_field = $this->info['save_json'] ? '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '[start]" value=\'' . $this->start_value . '\' />' : '' ;
		$this->end_utc_field = $this->info['save_json'] ? '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '[end]" value=\'' . $this->end_value . '\' />' : '' ;	

		$field = $this->make_start_or_end_field( 'start' ); 
		$field .= $this->make_start_or_end_field( 'end' ); 	
		
		return $field ;
	}	
	protected function get_dynamic_field(){
	}
	protected function parse_dynamic_value( $value ){
		$json_array = json_decode( $value , true) ; 
		return isset( $json_array['datetime'] ) ? $json_array['datetime'] : false ;	
	}
	protected function make_start_or_end_field( $start_or_end ){
		$fields = '<span class="selector '.$start_or_end.'" data-field_type="' . $this->field_type. '" >'; 
		switch( $this->field_type ){
			case 'time' : 
				$fields .= $this->get_sub_label( $start_or_end ); 			
				$fields .= $this->start_utc_field;
				$fields .= $this->get_time_field( $start_or_end ) ;	
				break; 
			case 'datetime' : 
				$fields .= $this->get_sub_label( $start_or_end ); 						
				$fields .= $this->start_utc_field;
				$fields .= $this->get_date_field( $start_or_end ) ;
				$fields .= $this->get_time_field( $start_or_end ) ;
				break;
			case 'date' : 
			default: 
				$fields .= $this->get_sub_label( $start_or_end );				
				$fields .= $this->start_utc_field;
				$fields .= $this->get_date_field( $start_or_end ) ;
				break;
		}
		$fields .= '</span>'; 		
		return $fields;
	}
	protected function get_sub_label( $start_or_end ){
		$label = '<span class="'.$start_or_end.'-label">'.ucfirst( $start_or_end ).'</span>' ;
		return $label;
	}
	protected function get_date_field( $start_or_end ){
		$name = $this->info['save_json'] ? '' : 'name="'.$this->info['name'].'['.$start_or_end.']"' ;
	
		$value = $start_or_end === 'start' ? $this->start_value : $this->end_value ; 

		$field = '<input data-dateformat="'.$this->date_format.'" '.$name.' type="text" id="' . $this->info['id'] . '-'.$start_or_end . '" class="datepicker '.$start_or_end.'" size="'.$this->size.'" value="' .  $value. '" '.$this->info['disabled'] .' />' ;
		return $field ;
	}
	protected function get_time_field( $start_or_end ){
		$name = $this->info['save_json'] ? '' : 'name="'.$this->info['name'].'['.$start_or_end.']"' ;
		$value = $start_or_end === 'start' ? $this->start_value : $this->end_value ; 
		
		$field = '<div class="input input-append bootstrap-timepicker">' ; 
        $field .= '<input name="'.$this->info['name'] . '['.$start_or_end.']" type="text" '.$name.' value="'.$value.'" class="timepicker '.$start_or_end.' input-small" '.$this->info['disabled'] .' >' ;
        $field .= '<span class="add-on"><i class="icon-time"></i></span>' ;
        $field .= '</div>' ;	
        return $field ;
    }		
	public static function enqueue_scripts_and_styles( $field_type = false ){

		self::enqueue_script( 'bootstrap-timepicker' );
		self::enqueue_style( 'bootstrap-timepicker' );
		
		self::enqueue_script( 'jquery-ui-datepicker' );

		parent::enqueue_scripts_and_styles( $field_type ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	
}