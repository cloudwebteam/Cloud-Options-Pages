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
		$subfields_names = self::get_subfield_scripts_and_styles(); 
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__, $subfields_names ); 
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
	private static function get_subfield_scripts_and_styles( ){
		Cloud_Options_Pages::get_instance(); 
		$options_pages_array = Cloud_Options_Pages::$options_pages;
		$sub_fields = array() ; 		
		if ( isset( $_GET['page'] ) && $_GET['page'] ){ // options page ?
			foreach( $options_pages_array as $top_level ){
				foreach( $top_level['subpages'] as $subpage_slug => $subpage ){
					if ( $subpage_slug === $_GET['page'] ){
						foreach ($subpage['sections'] as $section) {
							foreach ($section['fields'] as $field ){
								if ($field['type'] === 'group' ){
									foreach( $field['fields'] as $subfield ){
										$sub_fields[] = $subfield['type'];
									}
								}
							}
						}
					}
				}
			}
			$valid_subfields = array();
			foreach ( $sub_fields as $subfield_type ){ 
				$type 	= isset( $subfield_type ) ? $subfield_type : 'text' ;
				if( class_exists( $type ) ){
					$valid_subfields[] = $type ; 
				}
			}	
			return $valid_subfields;			
		} else {
			return false;
		}
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