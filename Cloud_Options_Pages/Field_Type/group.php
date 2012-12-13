<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_group extends Field_Type {
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
		parent::__construct( __CLASS__, $args ); 	
	}	
	protected function get_field_html( $args ){
		$Options_Page = Cloud_Options_Pages::get_instance(); 	
		$info = array(); 
		switch ( $this->context ) {
			case 'options-page' : 
				$top_level_slug = $args['top_level'];		
				$page_slug = $args['subpage'];
				$section_slug = $args['section'];
				$field_slug = $args['field']; 	
				$subfield_slug = isset( $args['subfield'] ) ? $args['subfield'] : '' ; 
				
				$this->saved_values = $Options_Page->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
				break; 
			case 'metabox' : 
				global $post ;
				$metabox_slug = $args['metabox'] ;
				$field_slug = $args['field'] ;

				$this->saved_values = $Options_Page->get_metabox_option( $post->ID, $metabox_slug, $field_slug ); 
				break;
		}
		$this->args = $args;		
		$this->field_groups = $this->set_fields( $args );
		
		return $this->field_groups;
	}
	private function set_fields(){
		if ( isset( $this->args['info']['subfields'] ) && is_array($this->args['info']['subfields'] )){	
			$groups = array();
			
			if ( is_array( $this->saved_values ) ){
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
	
		foreach ( $this->args['info']['subfields'] as $subfield_id => $subfield ){ 
			$type 	= isset( $subfield['type'] ) ? $subfield['type'] : 'standard' ;
			$field_type = class_exists( Field_Type::get_class_name( $type ) ) ? $type : parent::$default_type;
			$field_type_class_name = Field_Type::get_class_name( $field_type );
			// switch "copy_to_use" availability together
			$subfield['code_link'] = $this->info['code_link']; 
 
			// gotta compile an array that will be able to create the field
			$field_args = $this->args; 
			$field_args['subfield']	= $subfield_id;
			$field_args['group_number'] = $group_number; 
			$field_args['group_values'] = $group ; 			
			$field_args['info']	= $subfield; 
			$field_args['parent_section_layout'] = 'default';
			if ( $this->context == 'options-page' ){
				$field_args['info']['settable_defaults'] = false ;
			}
			ob_start();
				$field_type_class_name::create_field( $field_args ); 
			$fields .= ob_get_clean();
		}	
		return $fields;
	}

	protected function get_field_components( $args ){
		if ( $this->info['clone_controls'] ){
			$this->add_and_remove = '<div class="add-remove"><a class="add">+</a><a class="remove">-</a></div>';		
		} else {
			$this->add_and_remove = '' ; 
		}
	}
	public function enqueue_field_scripts_and_styles(){
		$subfields_names = self::get_subfield_scripts_and_styles(); 
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__, $subfields_names ); 
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' ); 
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
								if ($field['type'] === 'group' && isset( $field['subfields']  ) && is_array($field['subfields'] ) ){
									foreach( $field['subfields'] as $subfield ){
										$sub_fields[] = isset( $subfield['type'] ) ? $subfield['type'] : self::$default_type ;
									}
								}
							}
						}
					}
				}
			}
		} else if ( sizeof( Cloud_Options_Pages::$metaboxes ) > 0 ) {
			foreach ( Cloud_Options_Pages::$metaboxes as $metabox ) {
				foreach ( $metabox['fields'] as $field ){
					if ($field['type'] === 'group' && isset( $field['subfields']  ) && is_array($field['subfields'] ) ){
						foreach( $field['subfields'] as $subfield ){
							$sub_fields[] = isset( $subfield['type'] ) ? $subfield['type'] : self::$default_type ;
						}
					}
				}
			}		
			return $sub_fields;			
		} else {
			return false;
		}
	}
	
	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	public function standard ( $args ){
		?>
		<tr valign="top" <?php echo $this->attributes; ?>>
			<th scope="row"><?php echo $this->label; ?></th>
			<td class="multiple">
				<?php foreach ( $this->field_groups as $group ){ ?>
				<div class="group">
					<?php echo $group; ?>
					<?php echo $this->add_and_remove ; ?>
				</div>
				<?php } ?>
				<?php echo $this->description; ?>
			</td>
		</tr>
		<?php
	}
	public function expandable( $args ){
		$field_info = parent::get_field_info($args);
			
	}
	public function custom( $args ){
		$layout_details = $this->info['layout']; ?>
		<div <?php echo $this->attributes ; ?>>
			<p><?php echo $this->label; ?></p>
			<ul class="groups">
				<?php foreach ( $this->field_groups as $group ){ ?>
				<li class="group">
					<?php echo $group; ?>
					<?php echo $this->add_and_remove ; ?>
				</li>
				<?php } ?>
			</ul>
			<?php echo $this->description; ?>
		</div>
	<?php
	}
	
}