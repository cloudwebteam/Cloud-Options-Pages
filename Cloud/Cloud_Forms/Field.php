<?php 
class Cloud_Field {
	public static $default_type = 'text' ;
	public static $default_layout = 'custom' ; 
	public static $class_prefix = 'Cloud_Field_' ; 
	protected static $default_value ;
	protected $layout = 'default'; // fallback layout type
	protected $layouts = array();
	
	protected $info ; 
	protected $components ; 
	protected $attributes = array();
	
	protected function __construct( $spec ){
		$this->spec = $spec ;
		$this->enqueue_scripts_and_styles(); 
		$this->info = $this->setup_information(); 
		$this->attributes = $this->get_attributes() ;
		$this->layout = $this->get_layout();
		$this->components = $this->construct_field();	
		
		// arrange the title, field, description, and components
		$this->arranged_components = $this->arrange_field_components( ); 

 		// echo the field using the appropriate layout function. 
		$this->{ $this->layout }(); 
	}
	protected function setup_information(){
		$form_slug = $this->spec['subpage_slug'];
		$section_slug = $this->spec['section_slug'];
		$field_slug = $this->spec['field_slug']; 
		$input_id = $form_slug . '_' . $section_slug . '_' . $field_slug ;				
		$name =  $section_slug.'['.$field_slug.']';
		$value = $this->get_value( $field_slug, $section_slug );

		$cloneable =  isset( $this->spec['cloneable'] ) ? $this->spec['cloneable'] : false;
		$subfield_slug = isset( $this->spec['subfield'] ) ? $this->spec['subfield'] : '' ; 
		$default_value =  isset( $this->spec['default'] ) ? $this->spec['default'] : ''; 
			
		// part of a group?
		if ( $subfield_slug ){
			$group_number = isset( $this->spec['group_number'] ) ? $this->spec['group_number'] : 0 ;		
			$value = $value && isset( $value[$group_number][$subfield_slug] ) ? $value[$group_number][$subfield_slug] : ''; 
			$name = $name . '['.$group_number.']['.$subfield_slug.']'; 	
			$input_id = $input_id . '_' . $subfield_slug . '-' .$group_number ;				
			$cloneable = false;
		}

		$info = array(); 		
		$info['title'] = $this->spec['title'];
		$info['cloneable'] = $cloneable ;
	
		$info['clone_controls'] = isset( $this->spec['clone_controls'] ) ? $this->spec['clone_controls'] : true; 
		$info['sort'] = isset( $this->spec['sort'] ) ? $this->spec['sort'] : true; 
	
		$info['name'] = $name; 
		$info['description'] = $this->spec['description'] ;
		$info['id']   = Cloud_prefix . $input_id;
		$info['value'] = $value !== false && $value !== null ? $value : $default_value;
		$info['default'] = $default_value; 
		
		$info['layout'] = isset ($this->spec['layout'] ) ? $this->spec['layout'] : 'default';
		$info['width'] = isset( $this->spec['width'] ) ? $this->spec['width'] : 6; 
		$info['is_subfield'] = $subfield_slug !== '' ? true : false;

		return $info;
	}
	protected function get_value( $field_slug, $section_slug = '' , $form_slug = '' ){
		if ( $field_slug && $section_slug && $form_slug ){
			return isset( $_REQUEST[$form_slug][$section_slug][$field_slug] ) ? $_REQUEST[$form_slug][$section_slug][$field_slug] : false ; 	
		} else if ( $field_slug && $section_slug ){
			return isset( $_REQUEST[$section_slug][$field_slug] ) ? $_REQUEST[$section_slug][$field_slug] : false ; 	
		} else {
			return isset( $_REQUEST[$field_slug] ) ? $_REQUEST[$field_slug] : false ; 	
		}
	}
	protected function construct_field(){
		$components['label'] = '<div class="label">' .$this->get_label() .'</div>' ; 
		$components['description'] = $this->get_description( );
		if ( $this->info['cloneable'] ){
			$components['field'] = $this->make_cloneable( );
		} else {
			$components['field'] = '<div class="input">'.$this->get_field_html( ) .'</div>'; 		
		}				
		$extra_components = $this->make_extra_components( );		
		if ( is_array($extra_components) && sizeof( $extra_components ) > 0 ){
			$components = array_merge( $components, $extra_components ); 
		}	
		return $components;
		// get extra components needed to build the field ( optionally implemented )
	}
	/****
		* standardizes handling of a field attributes
	****/
	public static function get_class_name( $type ){
		return self::$class_prefix . $type ;
	}
	public static function get_field_type( $class ){
		return substr( $class, strlen( self::$class_prefix ) ); 
	}
	// each field needs to know how to create itself. This is where they do it. 
	protected function get_field_html(){ echo 'this field needs to implement get_field_html()'; }
	
	// optional, allows each field to create its own necessary components, returns array of components. 
	protected function make_extra_components( ){
		return array();
	}
	
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
							if ( isset( $this->components[$row_item] ) && $this->components[$row_item] && is_string( $this->components[$row_item] ) ){
								echo $this->components[$row_item] ; 
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
						if ( isset( $this->components[$row] ) && $this->components[$row] && is_string( $this->components[$row] ) ){ ?>
							<div class="field-row"><?php echo $this->components[$row] ; ?></div>
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
	protected function get_label(){
		$label = "<label class='title' for='". $this->info['id'] . "' >" . $this->info['title'] ."</label>";
		return $label;
	}
	protected function get_description( ){
		if ( isset( $this->info['description'] ) && $this->info['description'] ){
			$description = '<span class="description">'.$this->info['description'] . '</span>' ;
		} else {
			$description = false;
		}
		return $description;
	}
	protected function get_attributes( ){

		$classes = array(); 
		$classes[] = 'field' ;
		$classes[] = $this->info['is_subfield'] ? 'field_slug-' . $this->spec['subfield_slug'] : 'field_slug-' . $this->spec['field_slug'] ; 
		$classes[] = 'cf' ;
		$classes[] = 'type-'.$this->spec['type'] ;
 
		$classes[] = $this->info['sort'] ? '' : 'no-sort';
		if (  $this->info['cloneable'] ){
			$classes[] = 'has-cloneable' ;
		}
		if ( isset( $this->info['parent_layout'] ) && $this->info['parent_layout'] === 'grid' ){
			$classes[] = isset( $this->info['width'] ) ? 'span' . $this->info['width'] : 'span6';
		}
		$classes[] = isset( $this->info['style'] ) ? $this->info['style'] :  '' ;
		
		return  ' class="'.implode( ' ' , $classes ) .'" '; 
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
	protected function get_layout( ){
		$class_name = get_class( $this );
		if ( isset( $this->info['layout'] ) ){
			if ( is_array( $this->info['layout'] ) ){
				$layout = 'custom';
			} else if ( method_exists( $class_name, $this->info['layout'] ) ){
				$layout = $this->info['layout'];
			} else {
				$layout = self::$default_layout; 		
			}
		} else {
			$layout = self::$default_layout; 
		}

		if ( isset( $this->info['parent_layout'] ) && $this->info['parent_layout'] === 'standard' ) {
			$layout = 'standard'; 
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
				<?php echo $this->arranged_components ; ?>
			</td>
		</tr>
		<?php
	}	
	public function custom( ){
		$layout_details = $this->info['layout']; 
		?>
			<div <?php echo $this->attributes; ?>>
				<?php echo $this->arranged_components ; ?>
			</div>		
		<?php
	}	
	protected function enqueue_script( $handle, $path = '' , $dependencies = '' ){
		Cloud_Forms_StandAlone::enqueue_script( $handle, $path, $dependencies ); 
	}
	protected function enqueue_style( $handle, $path = '', $dependencies = '' ){
		Cloud_Forms_StandAlone::enqueue_style( $handle, $path, $dependencies ); 
	}	
	protected function enqueue_scripts_and_styles(){
		$class_name = get_class( $this );
		$field_type = $this->spec['type']; 
		if ( $field_type ){		
		
			$js_abs = self::get_include_path() . '/_js/'.$field_type.'.js' ; 
			$css_abs = self::get_include_path() . '/_css/'.$field_type.'.css' ; 
			if ( file_exists( $js_abs ) ){
				$js_path = self::get_folder_url() . '/_js/'.$field_type.'.js' ; 
				$this->enqueue_script( $class_name, $js_path, array( 'Cloud_Forms' ));
			} 
			if ( file_exists( $css_abs ) ){
				$css_path = self::get_folder_url() . '/_css/'.$field_type.'.css' ; 
				$this->enqueue_style( $class_name, $css_path, array( 'Cloud_Forms' ));
			}
		}
		if ( isset( $this->spec['subfields'] ) && is_array( $this->spec['subfields'] ) ){
			$subfields = $this->spec['subfields'] ;
			foreach ( $subfields as $subfield ){
				$subfield_type = $subfield['type'] ; 
				$subfield_class = Cloud_Field::get_class_name( $subfield_type ) ; 
				if ( class_exists( $subfield_class ) ){
					call_user_func( array( $subfield_class, 'enqueue_field_scripts_and_styles' ) ); 		
				}
			}			
		}
	}
	public static function get_folder_url(){
		return Cloud_Forms::get_folder_url() . '/'. basename( __FILE__, '.php') ;
	}
	public static function get_include_path(){
		return Cloud_Forms::get_include_path() . '/'. basename( __FILE__, '.php') ;
	} 	
}