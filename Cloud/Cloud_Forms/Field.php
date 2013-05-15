<?php 
class Cloud_Field {
	public static $default_type = 'text' ;
	public static $default_layout = 'custom' ; 
	public static $class_prefix = 'Cloud_Field_' ; 
	public static $is_WP = false; 
	protected static $default_value ;
	protected $layout = 'default'; // fallback layout type
	protected $layouts = array();
	
	protected $info ; 
	protected $components ; 
	protected $attributes = array();
	
	protected function __construct( $spec ){
		$this->spec = $spec ;
		$this->is_subfield = isset( $this->spec['subfield_slug'] ) ;
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
		if ( !empty( $this->spec['form_slug'] ) ){
			$info = Cloud_Field_Atts::get( $this->spec ); 	
		} else {
			$info = WP_Cloud_Field_Atts::get( $this->spec ); 
		}
		return $info ;
		
	}
	protected function get_value( $field_slug, $section_slug = '' , $form_slug = '' ){
		if ( isset( $_REQUEST['form_id'] ) && $_REQUEST['form_id'] === $this->spec['form_slug'] ){
			if ( $field_slug && $section_slug && $form_slug ){
				return isset( $_REQUEST[$form_slug][$section_slug][$field_slug] ) ? $_REQUEST[$form_slug][$section_slug][$field_slug] : false ; 	
			} else if ( $field_slug && $section_slug ){
				return isset( $_REQUEST[$section_slug][$field_slug] ) ? $_REQUEST[$section_slug][$field_slug] : false ; 	
			} else {
				return isset( $_REQUEST[$field_slug] ) ? $_REQUEST[$field_slug] : false ; 	
			}
		}
	}
	protected function construct_field(){
		if ( $label = $this->get_label() ){
			$components['label'] = '<div class="label">' .$label.'</div>' ; 
		}
		if ( $this->info['cloneable'] ){
			$components['field'] = $this->make_cloneable( );
		} else {
			if ( $field = $this->get_field_html( ) ){
				$components['field'] = '<div class="input cf">'.$field. $this->copy_to_use() .'</div>'; 		
			}
		}			
			
		if ( $description = $this->get_description() ) {
			$components['description'] = $description ; 
		}
		if ( $error = $this->get_error() ){
			$components['error'] =  '<div class="cloud-error">'.$error.'</div>';		
		} else {
			$components['error'] =  '<div class="cloud-error"></div>' ; 
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
				if ( $row && is_array( $row ) && sizeof( $row ) > 0 ){ 
					$row_components = '' ; 
					foreach( $row as $row_item ){ 
						if ( !empty( $this->components[ $row_item ] ) ){
							$row_components .= $this->components[$row_item] ; 
							unset( $this->components[ $row_item ] ); 
						}
					}
					if ( $label_in_left_column && $row_item == 'label' ){
					 // do nothing
					} else { 				
						if ( isset( $this->components[$row_item] ) && $this->components[$row_item] && is_string( $this->components[$row_item] ) ){
							$row_components .= $this->components[ $row_item ] ; 
							unset( $this->components[$row_item] );
						}
					}
					
					if ( $row_components ){ ?>
					<div class="field-row inline-field-elements">
						<?php echo $row_components ; ?>
					</div>
					<?php }
				// otherwise, check if the provided string is a valid field element, then place it in the row. 
				} else {
					// if its a standard layout ( table based  ) the label is needed on the left, so don't put it here					
					if ( $label_in_left_column && $row == 'label' ){
						// do nothing						
					} else {
						if ( $row && isset( $this->components[$row] ) && $this->components[$row] && is_string( $this->components[$row] ) ){ ?>
							<div class="field-row"><?php echo $this->components[$row] ; ?></div>
						<?php 
							unset( $this->components[$row] );
						}
					}
				}
			}
			// any unspecified elements are appended so they aren't left out. Used items have been unset (see above). 
			foreach( $this->components as $unspecified_item ){ 
				if ( $unspecified_item ){ ?>
				<div class="field-row"><?php echo $unspecified_item ; ?></div>
			<?php }
			}
		}
		return ob_get_clean() ;
	}
	// wraps the field/fields in html that makes it cloneable
	protected function make_cloneable( ){
		$this->enqueue_script( 'jquery-ui-sortable' ); 
		$name = $this->info['name']; 
		$values = $this->info['value'] ;
		
		$clones = array(); 
		if ( is_array( $values ) ){
			foreach ( $values as $clone_number => $clone_value ){
				$clones[$clone_number][ 'clone' ] = $this->make_clone( $clone_number, $clone_value, $name ); 
				$clones[$clone_number][ 'error' ] = isset( $this->spec['validation_error'][ $clone_number ] ) ? '<span class="error">'.$this->spec['validation_error'][ $clone_number ] .'</span>' : '' ;
			}
		} else {
			$clones[0]['clone'] = $this->make_clone( 0, '', $name ); 
			$clones[0]['error'] = false; 
		}

		ob_start(); ?>
		<div class="input">
			<ul class="cloneable cf">
			<?php foreach( $clones as $clone_number => $clone ){ ?>
				<li class="clone cf">
					<div class="number"><?php echo $clone_number + 1 ; ?></div>
					<?php echo $clone['clone']; ?>
					<?php echo $clone['error']; ?>
					<div class="add-remove"><a class="remove">-</a><a class="add">+</a></div>
				</li>
			<?php } ?>
			</ul>
		</div>
		<?php $output = ob_get_clean(); 
		return $output; 
		 
	}
	protected function make_clone( $clone_number, $clone_value, $name  ){	
	
		$this->info['name'] = $name.'['.$clone_number.']';
		$this->info['value'] = $clone_value;	
		$clone = $this->get_field_html(); 

		return $clone ;
	}
	protected function get_label(){
		$label = "<label class='title' for='". $this->info['id'] . "' >" . $this->info['title'] . "</label>";
		return $label;
	}
	protected function copy_to_use(){
		if ( !empty( $this->info['to_retrieve'] ) ){
			ob_start();?>
			<span class="copy_to_use">
				<span class="copy-label">Use:</span> <input type="text" value='<?php echo $this->info['to_retrieve'] ; ?>' />
			</span>
		<?php 
			return ob_get_clean();
		} else {
			return false; 
		}
	}
	protected function get_description( ){
		if ( isset( $this->info['description'] ) && $this->info['description'] ){
			$description = '<span class="description">'.$this->info['description'] . '</span>' ;
		} else {
			$description = false;
		}
		return $description;
	}
	protected function get_error(){
		return isset( $this->spec['validation_error'] ) && ! is_array( $this->spec['validation_error'] ) ? $this->spec['validation_error'] : false ;
	}	
	protected function get_attributes( ){

		$classes = array(); 
		$classes[] = 'field' ;
		$classes[] = $this->info['is_subfield'] ? 'subfield_slug-' . $this->spec['subfield_slug'] : 'field_slug-' . $this->spec['field_slug'] ; 
		$classes[] = 'cf' ;
		$classes[] = 'type-'.$this->spec['type'] ;
 
		$classes[] = $this->info['sort'] ? '' : 'no-sort';
		if (  $this->info['cloneable'] ){
			$classes[] = 'has-cloneable' ;
		}
		if ( isset( $this->info['parent_layout'] ) && $this->info['parent_layout'] === 'grid' ){
			$classes[] = isset( $this->info['width'] ) ? 'span' . $this->info['width'] : 'span6';
		}
		if ( isset( $this->spec['validation_error'] ) && $this->spec['validation_error'] ){
			$classes[] = 'has-error' ; 
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

		if ( isset( $this->spec['parent_layout'] ) && $this->spec['parent_layout'] === 'standard' ) {
			$layout = 'standard'; 
		}
		
		return $layout;
	}
	public static function get_option( $value, $spec ){
		return $value ;
	}
	
	public function standard ( ){ ?>
		<tr valign="top" <?php echo $this->attributes; ?>>
			<th scope="row"><?php echo $this->components['label'];  ?></th>
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
	protected static function enqueue_script( $handle, $path = '' , $dependencies = '' ){
		if ( self::$is_WP ){
			Cloud_Forms_WP::enqueue_script( $handle, $path, $dependencies ); 
		} else {
			Cloud_Forms_StandAlone::enqueue_script( $handle, $path, $dependencies ); 
		}
	}
	protected static function enqueue_style( $handle, $path = '', $dependencies = '' ){
		if ( self::$is_WP ){
			Cloud_Forms_WP::enqueue_style( $handle, $path, $dependencies ); 		
		} else {
			Cloud_Forms_StandAlone::enqueue_style( $handle, $path, $dependencies ); 
		}
	}	
	public static function enqueue_scripts_and_styles( $field_type = false ){

		$class_name = self::get_class_name( $field_type ); 
		if ( $field_type ){		
			$js_abs = self::get_include_path() . '/_js/'.$field_type.'.js' ; 
			$css_abs = self::get_include_path() . '/_css/'.$field_type.'.css' ; 
			
			if ( file_exists( $js_abs ) ){
			
				$js_path = self::get_folder_url() . '/_js/'.$field_type.'.js' ; 				
				self::enqueue_script( $class_name, $js_path, array( 'Cloud_Forms' ));
			} 
			if ( file_exists( $css_abs ) ){
			
				$css_path = self::get_folder_url() . '/_css/'.$field_type.'.css' ; 
				self::enqueue_style( $class_name, $css_path, array( 'Cloud_Forms' ));
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