<?php 
class Cloud_Field {
	public static $default_type = 'text' ;
	public static $default_layout = 'custom' ; 
	public static $class_prefix = 'Cloud_Field_' ; 
	protected static $default_value ;
	protected $layout = 'default'; // fallback layout type
	protected $layouts = array();
	
	protected $info ; 
	protected $label ; 
	protected $field ; 
	protected $attributes = array();
	
	protected function __construct( $class_name, $args){
		$this->args = $args ;
		// where is the field being placed? Metabox? Options page? Somewhere else?
		$this->context = isset( $args['context'] ) ? $args['context'] : 'options-page' ; 
		
		// set type for reference if needed
		$this->type = $args['info']['type'] ;
		
		// setup basic field attributes
 		$this->info = $this->get_field_info($args);

 		// create standard label to be placed
		$this->label = '<div class="label">' . $this->get_label( $this->info ) . '</div>'; 
		
		// create standard description to be placed
		$this->description = $this->get_description( $this->info );
		
		// create attributes to be set on the field container
		$this->attributes = 'class="' . implode(' ', $this->get_attributes( $this->info ) ).'"';
		
		if ( $this->info['cloneable'] ){
			// create field ( this method should be implemented by each field subclass )
			$this->make_cloneable( $args );
		} else {
			// create field ( this method should be implemented by each field subclass )
			$this->field = '<div class="input">'.$this->get_field_html( $args ) .'</div>'; 
		}
		
		// get extra components needed to build the field ( optionally implemented )
		$this->get_extra_components( $args );
		
		// figure out what layout to use
		$this->layout = $this->get_layout( $class_name, $this->info );
		
		// arrange the title, field, description, and components
		$this->field_components = $this->arrange_field_components( ); 
		
 		// echo the field using the appropriate layout function. 
		$this->{ $this->layout }( $args ); 
	}

	public function enqueue_field_scripts_and_styles(){
		self::register_scripts_and_styles( get_called_class() );  // would be best, but only in 5.3... should fallback and not break
	
	}
	/****
		* standardizes handling of a field attributes
	****/
	public static function get_class_name( $type ){
		return self::$class_prefix . $type ;
	}
	public function get_field_info( $args ){
		$Options = Cloud_Options::get_instance(); 

		$info = array(); 
		switch ($this->context ){
			case 'options-page' :
				$Options_Pages = Cloud_Options_Pages::get_instance();
				
				$top_level_slug = $args['top_level'];		
				$page_slug = $args['subpage'];
				$section_slug = $args['section'];
				$field_slug = $args['field']; 
				$input_id = $section_slug . '_' . $field_slug ;				
				$enabled = $Options_Pages->is_option_enabled( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
				$user_enabled_override_name = $page_slug.'[enabled]['.$section_slug.']['.$field_slug.']';
				$value = $Options_Pages->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
				$name =  $page_slug.'['.$section_slug.']['.$field_slug.']'; 
				// only interior here, so it can be added to
				$to_retrieve = '"'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'"' ;			
				$function_to_retrieve = 'get_theme_options' ;
				break; 
			case 'metabox' :
				global $post ;
				$Metaboxes = Cloud_Metaboxes::get_instance();
			
				$metabox_slug = $args['metabox'];
				$field_slug = $args['field']; 
				$input_id = $metabox_slug . '_'. $field_slug ;
				$enabled = $Metaboxes->is_option_enabled( $post->ID, $metabox_slug, $field_slug ); 
				$user_enabled_override_name =  $metabox_slug.'[enabled]['.$field_slug.']';
				$value = $Metaboxes->get_option( $post->ID, $metabox_slug, $field_slug ); 
				$name =  $metabox_slug . '['.$field_slug.']'; 
				// only interior here, so it can be added to
				$to_retrieve = '"'. $post->ID.'", "'. $metabox_slug . '" , "'. $field_slug.'"' ;
				$function_to_retrieve = 'get_metabox_options' ;
				break ;
		}
		$cloneable =  isset( $args['info']['cloneable'] ) ? $args['info']['cloneable'] : false;
		$subfield_slug = isset( $args['subfield'] ) ? $args['subfield'] : '' ; 
		$default_value =  isset( $args['info']['default'] ) ? $args['info']['default'] : ''; 
			
		// part of a group?
		if ( $subfield_slug ){

			$group_number = isset( $args['group_number'] ) ? $args['group_number'] : 0 ;		
			$value = $value && isset( $value[$group_number][$subfield_slug] ) ? $value[$group_number][$subfield_slug] : ''; 
			$name = $name . '['.$group_number.']['.$subfield_slug.']'; 	
			$input_id = $input_id . '_' . $subfield_slug . '-' .$group_number ;				
			
			$to_retrieve = 	$function_to_retrieve .'( '. $to_retrieve .', '. $group_number .' , "' .$subfield_slug .'" ) ';	
			$cloneable = false;
			if ( $this->context == 'options-page' ){
				$enabled = true ; 
			} else if ( $this->context == 'metabox' ){
				$enabled = $Metaboxes->is_option_enabled( $post->ID, $metabox_slug, $field_slug, $group_number, $subfield_slug ); 
			}
			$user_enabled_override_name = $user_enabled_override_name . '['.$group_number.']['.$subfield_slug.']' ;
			$default_value = isset( $saved_default ) ? $saved_default : ( isset( $args['info']['default'] ) ? $args['info']['default'] : '' ); 
		// plain old field
		} else {
			$to_retrieve = 	$function_to_retrieve .'( '. $to_retrieve .' ) ';			
		}
		
		
		$info['title'] = $args['info']['title'];
		$info['to_retrieve'] = 	$to_retrieve;				
		$info['cloneable'] = $cloneable ;
		
		// setup lock on various things we might want to lock down. 			 
		if ( $args['info']['_lock'] ){
			$info['clone_controls'] = false;
			$info['code_link'] = false;
			$info['sort'] = false;
		} else {
			$info['clone_controls'] = isset( $args['info']['clone_controls'] ) ? $args['info']['clone_controls'] : true; 
			$info['code_link'] = isset( $args['info']['code_link'] ) ? $args['info']['code_link'] : true; 
			$info['sort'] = isset( $args['info']['sort'] ) ? $args['info']['sort'] : true; 
		}
	
		$info['name'] = $name; 
		$info['description'] = isset( $args['info']['description'] ) ? $args['info']['description'] : null;
		$info['id']   = $input_id;
		$info['value'] = $value !== false && $value !== null ? $value : $default_value;
		$info['default'] = $default_value; 
		$info['settable_defaults'] = isset( $args['info']['settable_defaults'] ) ? $args['info']['settable_defaults'] : false;
		
		$info['enabled'] = $enabled ;
		$info['enabled_name'] = $user_enabled_override_name; 		
		$info['parent_layout'] = isset( $args['parent_section_layout'] ) ? $args['parent_section_layout'] : 'standard' ;
		$info['layout'] = isset ($args['info']['layout'] ) ? $args['info']['layout'] : 'default';
		$info['prefix'] = $Options->prefix; 		
		$info['fields'] = isset( $args['info']['fields'] ) ? $args['info']['fields'] : ''; 
		$info['width'] = isset( $args['info']['width'] ) ? $args['info']['width'] : 6; 
		$info['is_subfield'] = $subfield_slug !== '' ? true : false;

		return $info;
	}
	
	// each field needs to know how to create itself. This is where they do it. 
	protected function get_field_html($args){ echo 'this field needs to implement get_field_html()'; }
	
	// optional, allows each field to create its own necessary components
	protected function get_extra_components( $args ){}
	
	// arranges the parts inside the field, using the 'layout' parameter, if provided. Otherwise, it uses default
	protected function arrange_field_components( ){
		$layout_array = $this->info['layout'] ; 
		$label_in_left_column = $this->layout === 'standard' ; 
		ob_start(); 
	
		if ( is_array( $layout_array ) && sizeof( $layout_array ) > 0 ){
			// for each element of the layout array, make a row. 
			foreach( $layout_array as $row ){ 

				// if, an array, then foreach element of the array, 
				// check if it is a valid field element, then place it in the row. 
				if ( is_array( $row ) && sizeof( $row ) > 0 ){ ?>
					<div class="field-row">
					<?php foreach( $row as $row_item ){ 
						if ( $label_in_left_column && $row_item == 'label' ){
						 // do nothing
						} else { 				
							if ( isset( $this->$row_item ) && $this->$row_item && is_string( $this->$row_item ) ){
								echo $this->$row_item ; 
							}
						}
					} ?>
					</div> 
				<?php
				// otherwise, check if the provided string is a valid field element, then place it in the row. 
				} else {
					// if its a standard layout ( table based  ) the label is needed on the left, so don't put it here					
					if ( $label_in_left_column && $row == 'label' ){
						// do nothing						
					} else {
						if ( isset( $this->$row ) && $this->$row && is_string( $this->$row ) ){ ?>
							<div class="field-row"><?php echo $this->$row ; ?></div>
						<?php }
					}
				}
			}
		}
		return ob_get_clean() ;
	}
	// wraps the field/fields in html that makes it cloneable
	protected function make_cloneable( $args ){
		switch ($this->context ){
			case 'options-page' :
				$Options_Pages = Cloud_Options_Pages::get_instance();
				
				$top_level_slug = $args['top_level'];		
				$page_slug = $args['subpage'];
				$section_slug = $args['section'];
				$field_slug = $args['field']; 	
				$this->saved_values = $Options_Pages->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
				break; 
			case 'metabox' :
				global $post ;
				$Metaboxes = Cloud_Metaboxes::get_instance();
				$metabox_slug = $args['metabox'];
				$field_slug = $args['field']; 	
				$this->saved_values = $Metaboxes->get_option( $post->ID, $metabox_slug, $field_slug ) ;
				break ;
		}
		$this->args = $args;		
		$name = $this->info['name']; 
		$value = $this->info['value'] ;
		$parent_to_retrieve = $this->info['to_retrieve']; 
		$this->info['settable_defaults'] = false ;		



		$clones = array(); 
		if ( is_array( $this->saved_values ) ){

			foreach ( $this->saved_values as $clone_number => $clone_value ){
				switch ( $this->context ){
					case 'options-page' : 
						$parent_to_retrieve = 	'get_theme_options( "'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'" , ' . $clone_number .' )';	
						break;
					case 'metabox' : 
						$parent_to_retrieve = 'get_theme_options( "'. $post->ID.'", "'. $metabox_slug . '" , "'. $field_slug.'" , ' . $clone_number .' )';	
						$clones[$clone_number] = $this->make_clone( $clone_number, $clone_value, $name, $parent_to_retrieve ); 
						break;
				} 
			} 
		} else {
			$clones[0] = $this->make_clone( 0, '', $name, $parent_to_retrieve); 
		}
		$output = '<div class="input">';
		$output .= '<ul class="cloneable cf">';
		foreach( $clones as $clone_number => $clone ){
			$output .= '<li class="clone cf">' ;
			$output .= '<div class="number">'.($clone_number+1).'</div>';
			$output .= $clone;
			if ( $this->info['clone_controls'] ){
				$output .= '<div class="add-remove"><a class="remove">-</a><a class="add">+</a></div>';
			}
			$output .= '</li>';
		}
		$output .= '</ul>';
		$output .= '</div>' ;
		
		$this->field = $output;
		$this->info['to_retrieve'] = $parent_to_retrieve ; 
		 
	}
	protected function make_clone( $clone_number, $clone_value, $name, $to_retrieve  ){	
	
		$this->info['name'] = $name.'['.$clone_number.']';
		$this->info['value'] = $clone_value;	
		$this->info['to_retrieve'] = $to_retrieve ;
		$clone = $this->get_field_html( $this->args ); 

		return $clone . self::get_copy_to_use( $this->info );
	}
	protected function get_label($field_info){
		$to_use = self::get_copy_to_use( $field_info); 
		$label = $to_use."<label class='title' for='".$field_info['prefix'] . $field_info['id'] . "' >" . $field_info['title'] ."</label>";
		return $label;
	}
	protected static function get_copy_to_use( $field_info ){
		if ($field_info['code_link'] ){
			$to_use = "<span class='copy_to_use'><a rel='copy_to_use'>&#36;use;</a><span class='copy-container'><input class='copy' type='text' value='".$field_info['to_retrieve']."' /></span></span>";
		} else {
			$to_use = '';
		}
		return $to_use;
	}
	protected function get_description( $field_info ){
		$description = isset( $field_info['description']) && $field_info['description'] !== '' ? '<span class="description">'.$field_info['description'] . '</span>' : '';
		return $description;
	}
	protected function get_attributes( $field_info ){

		$classes = array(); 
		$classes[] = 'field' ;
		$classes[] = $this->info['is_subfield'] ? 'field_slug-' . $this->args['subfield'] : 'field_slug-' . $this->args['field'] ; 
		$classes[] = 'cf' ;
		$classes[] = 'type-'.$this->type ;
		$classes[] = $this->info['sort'] ? '' : 'no-sort';
		if (  $this->info['cloneable'] ){
			$classes[] = 'has-cloneable' ;
		}
		if ( $field_info['parent_layout'] === 'grid' ){
			$classes[] = isset( $field_info['width'] ) ? 'span' . $field_info['width'] : 'span6';
		}
		$classes[] = isset( $field_info['style'] ) ? $field_style['style'] :  '' ;
		
		return $classes ;
	}
	public static function get_layout_function( $layout = null , $field_type = null , $section_layout_type ){
		self::$default_type = 'text'; // fallback field type
		self::$default_layout = 'standard'; // fallback layout type
		self::$default_value = 'default';
		
		// if they passed in an array, rather than a string, route it to the 'custom' function
		if ( is_array( $layout ) ){
			$layout = 'custom'; 
		}
		
		if ( $field_type && method_exists( $field_type , $layout) ){
			$chosen_layout = $layout;
		} else {
			$chosen_layout = self::$default_layout;
		}

		// handles when a parent's layout necessitates a certain fields layout
		switch ( $section_layout_type ){
		
			// make sure if the section is a table ('standard'), then these are table rows ('standard')
			case 'standard' : 
				$chosen_layout = 'standard';
				break;
		
		}
		return $chosen_layout;
		
	}	
	protected function get_layout( $class_name, $field_info ){

		if ( isset( $field_info['layout'] ) ){
			if ( is_array( $field_info['layout'] ) ){
				$layout = 'custom';
			} else if ( method_exists( $class_name, $field_info['layout'] ) ){
				$layout = $field_info['layout'];
			} else {
				$layout = self::$default_layout; 		
			}
		} else {
			$layout = self::$default_layout; 
		}

		if ( $field_info['parent_layout'] === 'standard' ) {
			$layout = 'standard'; 
		}
		
		if ( $this->context == 'metabox' ){
			if ( is_callable( array( $this, 'metabox' ) ) ){
				$layout = 'metabox' ;
			} else {
				$layout = 'custom' ;
			}
			if ( $this->info['is_subfield'] ){
				$layout = 'standard' ;
			}			
		}
		return $layout;
	}
	public static function get_option( $value, $spec ){
		return $value ;
	}
	
	public function standard ( ){ ?>
		<tr valign="top" <?php echo $this->attributes; ?>>
			<th scope="row"><?php echo $this->label; ?></th>
			<td>	
				<?php echo $this->field_components ; ?>
			</td>
		</tr>
		<?php
	}	
	public function custom( ){
		$layout_details = $this->info['layout']; 
		?>
			<div <?php echo $this->attributes; ?>>
				<?php echo $this->field_components ; ?>
			</div>		
		<?php
	}	
	protected static function register_scripts_and_styles( $class_name, $subfield_types = null ){
		$field_type = substr( $class_name, strlen(Cloud_Field::$class_prefix) ); 
		if ( $field_type ){			
			if ( file_exists( dirname( __FILE__ ).'/'.basename( __FILE__, '.php' ) . '/_js/'.$field_type.'.js' ) ){
				wp_enqueue_script( $class_name, self::get_folder_url(). '/_js/'.$field_type.'.js', array( 'jquery', 'Options' ), '');
			} 
			if ( file_exists( dirname( __FILE__ ).'/'.basename( __FILE__, '.php' ) . '/_css/'.$field_type.'.css' ) ){
				wp_enqueue_style( $class_name, self::get_folder_url(). '/_css/'.$field_type.'.css', array( 'Options' ));
			}
		}
		if ( is_array( $subfield_types ) && sizeof( $subfield_types ) > 0 ){
			foreach ( $subfield_types as $subfield_type ){
				$subfield_class = Cloud_Field::get_class_name( $subfield_type ) ; 
				if ( class_exists( $subfield_class ) ){
					call_user_func( array( $subfield_class, 'enqueue_field_scripts_and_styles' ) ); 		
				}
			}			
		}
	}
	public static function get_folder_url(){
		return Cloud_Options::get_folder_url() . '/'. basename( __FILE__, '.php') ;
	}
	public static function get_include_path(){
		return Cloud_Options::get_include_path() . '/'. basename( __FILE__, '.php') ;
	} 	
}