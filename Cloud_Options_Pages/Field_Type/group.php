<?php 
class group extends Field_Type {
	protected $info ;
	protected $fields ;
	protected $label ;
	protected $url_button ; 
	protected $saved_values ;

	public static function create_field( $args ){

		$field_type = __CLASS__;
		$field = new $field_type( $args ); 

	}	
	protected function __construct( $args ){
		$Options_Page = Cloud_Options_Pages::get_instance(); 	
	

		$info = array(); 
		
		$top_level_slug = $args['top_level'];		
		$page_slug = $args['subpage'];
		$section_slug = $args['section'];
		$field_slug = $args['field']; 	
		$subfield_slug = isset( $args['subfield'] ) ? $args['subfield'] : '' ; 
		
		$this->saved_values = $Options_Page->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
		$this->args = $args;
		$this->field_groups = $this->set_fields( $args );
		$this->add_and_remove = '<div class="add-remove"><a class="add">+</a><a class="remove">-</a></div>';
		parent::__construct( __CLASS__, $args ); 
	

	}

	public function enqueue_field_scripts_and_styles(){
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
	}
	
	private function set_fields(){

		if ( isset( $this->args['info']['fields'] ) && is_array($this->args['info']['fields'] )){	
			$groups = array();
			
			if ( is_array( $this->saved_values ) && is_array( $this->saved_values[0] ) ){
				foreach ( $this->saved_values as $group_number => $group ){
					$groups[$group_number] = $this->make_group( $group_number, $group); 
				} 
			} else {
				$groups[0] = $this->make_group( 0, ''); 								
			}
		}


		return $groups;
	
	}	
	private function make_group( $group_number, $group ){
		$fields = '' ; 
	
		foreach ( $this->args['info']['fields'] as $subfield_id => $subfield ){ 
			$type 	= isset( $subfield['type'] ) ? $subfield['type'] : 'standard' ;
			$field_type = class_exists( $type ) ? $type : parent::$default_type;
			
			// gotta compile an array that will be able to create the field
			$field_args = $this->args; 
			
			$field_args['subfield']	= $subfield_id;
			$field_args['group_number'] = $group_number; 
			$field_args['group_values'] = $group ; 			
			$field_args['info']	= $subfield; 
			ob_start();
				$field_type::create_field( $field_args ); 
			$fields .= ob_get_clean();
		}	
		return $fields;
	}
	private function get_image(){
		if ( isset( $this->info['value'] ) && $this->info['value'] !== '' ){
			return '<img class="preview-image img-polaroid" src="'.$this->info['value'].'" title="'.$this->info['value'].'" />';	
		} else {
			return '<img class="hidden preview-image img-polaroid" title="'.$this->info['value'].'" />';	
		}
	}	
	/* LAYOUTS */
	
	public function standard ( $args ){
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->label; ?></th>
			<td class="multiple">
				<?php foreach ( $this->field_groups as $group ){ ?>
				<div class="group">
					<?php echo $group; ?>
					<?php echo $this->add_and_remove ; ?>
				</div>
				<?php } ?>
			</td>
		</tr>
		<?php
	}
	public function expandable( $args ){
		$field_info = parent::get_field_info($args);
			
	}
	public function custom( $args ){
		$layout_details = $this->info['layout']; ?>
		<div class="multiple">
			<?php foreach ( $this->field_groups as $group ){ ?>
			<div class="group">
				<?php echo $group; ?>
				<?php echo $this->add_and_remove ; ?>
			</div>
			<?php } ?>
		</div>		
		<?php
	}
	
}