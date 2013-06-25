<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_group extends Cloud_Field {
	public static function create_field( $args ){
		$field = new self( $args ); 
	}	
	protected function get_field_html( ){		

		$this->min_clones = isset( $this->info['cloneable']['min'] ) ? $this->info['cloneable']['min'] : 0; 
		$this->max_clones = isset( $this->info['cloneable']['max'] ) && $this->info['cloneable']['max'] ? $this->info['cloneable']['max'] : 1000; 

	

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
				for( $i = 0; $i < $this->max_clones; $i++ ){
					if ( isset( $this->info['value'][$i] ) ){
						$groups[$i] = $this->make_group( $i, $group); 					
					} else {
						if ( $i == ( $this->min_clones ) ){
							break;
						} else {
							$groups[$i] = $this->make_group( $i, '' ); 
						}
					}
				}
			} else {
				for( $i = 0; $i < $this->max_clones; $i++ ){
					if ( $i == ( $this->min_clones ) ){
						break;
					} else {
						$groups[$i] = $this->make_group( $i, '' ); 
					}
				}			
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
			$subfield_args['validation_error'] = isset( $this->spec['validation_error'][ $group_number ][ $subfield_slug ] ) ? $this->spec['validation_error'][ $group_number ][ $subfield_slug ] : '' ; 	
			$subfield_args['layout'] = array( array( 'label', 'field', 'error' ), 'description' );
			ob_start();
				$subfield_class_name::create_field( $subfield_args ); 
			$subfields .= ob_get_clean();
		}
		return $subfields;
	}
	private function get_model_group(){
		return $this->make_group( 0, ''); 								
	}
	public static function enqueue_scripts_and_styles( $field_type ){
		self::enqueue_script( 'jquery-ui-core' );
		self::enqueue_script( 'jquery-ui-sortable' ); 		
		// if they exist, enqueues css and js files with this fields name
		parent::enqueue_scripts_and_styles( $field_type ); 
	}
	// generates all the html for the groups so it can be stored and moved as $this->field
	protected function get_groups_html(){ 
		$data = '';
		$data .= $this->min_clones ? ' data-min="'.$this->min_clones.'"' : ''; 
		$data .= $this->max_clones ? ' data-max="'.$this->max_clones.'"' : ''; 	
			
		ob_start(); ?>
		<ul class="cloneable groups cf" <?php echo $data; ?> >
			<li class="clone group to-clone cf">
				<?php echo $this->get_model_group(); ?>
				<?php echo $this->add_and_remove ; ?>				
			</li>
			<?php foreach ( $this->field_groups as $group ){ ?>
			<li class="clone group cf">
				<?php echo $group; ?>
				<?php echo $this->add_and_remove ; ?>
			</li>
			<?php } ?>
			<div class="no-clones cf">
				<?php $empty_text = isset( $this->info['cloneable']['zero_text'] ) ? $this->info['cloneable']['zero_text'] : 'None created. Add the first.' ; ?>
				<div class="empty-text"><?php echo $empty_text; ?></div>					
				<div class="add-remove"><a class="add">+</a></div>
			</div>	
		</ul>
	<?php
		return ob_get_clean(); 
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}