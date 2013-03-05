<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_group extends Cloud_Field {
	public static function create_field( $args ){
		$field = new self( $args ); 
	}	
	protected function get_field_html( ){		
		$this->subfields = isset( $this->spec['subfields'] ) && is_array( $this->spec['subfields'] ) ? $this->spec['subfields'] : false ;

		$this->field_groups = $this->get_subfields( );
		if ( $this->info['clone_controls'] ){
			$this->add_and_remove = '<div class="add-remove"><a class="remove">-</a><a class="add">+</a></div>';		
		} else {
			$this->add_and_remove = '' ; 
		}
				
		$field = $this->get_groups_html(); 

		return $field ;
	}
	private function get_subfields( ){
		$groups = array();
		if ( $this->subfields ){				
			if ( is_array( $this->info['value'] ) ){
				foreach ( $this->info['value'] as $group_number => $group ){
					$groups[$group_number] = $this->make_group( $group_number, $group); 
				} 
			} else {
				$groups[0] = $this->make_group( 0, ''); 								
			}
		}

		return $groups;

	}	
	private function make_group( $group_number, $group ){
		$subfields = '' ; 
	
		foreach ( $this->subfields as $subfield_slug => $subfield_spec ){ 

			$type 	= $subfield_spec['type'] ;
			$subfield_type = class_exists( parent::get_class_name( $type ) ) ? $type : parent::$default_type;
			$subfield_class_name = parent::get_class_name( $subfield_type );
						
			// gotta compile an array that will be able to create the field
			$subfield_args = $subfield_spec ; 
			$subfield_args['subfield_slug']	= $subfield_slug;
			$subfield_args['group_number'] = $group_number; 
			$subfield_args['group_values'] = $group ; 			
			$subfield_args['layout'] = array( array( 'label', 'field' ), 'description' );
			ob_start();
				$subfield_class_name::create_field( $subfield_args ); 
			$subfields .= ob_get_clean();
		}
		return $subfields;
	}
	public function enqueue_scripts_and_styles(){
		$this->enqueue_script( 'jquery-ui-core' );
		$this->enqueue_script( 'jquery-ui-sortable' ); 		
		// if they exist, enqueues css and js files with this fields name
		parent::enqueue_scripts_and_styles( ); 
	}
	// generates all the html for the groups so it can be stored and moved as $this->field
	protected function get_groups_html(){ 
		ob_start(); 
	?>
		<ul class="groups cf">
			<?php foreach ( $this->field_groups as $group ){ ?>
			<li class="group cf">
				<?php echo $group; ?>
				<?php echo $this->add_and_remove ; ?>
			</li>
			<?php } ?>
		</ul>
	<?php
		return ob_get_clean(); 
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}