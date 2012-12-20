<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_select extends Field_Type {
	protected $info ;
	protected $field ;
	protected $label ;
	protected $options = 'post' ; 
	protected $attributes = '' ;
	protected $multiple ; 

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}	

	protected function get_field_html( $args ){
		$this->multiple = isset( $args['info']['multiple'] ) ? $args['info']['multiple'] : false ; 
		$this->options_list = $this->get_options_list( $args );
		if ( $this->multiple ){
			$multiple = ' multiple="multiple"' ; 
			$this->info['name'] = $this->info['name'] . '[]'; 
		} else {
			$multiple = '';
		}
		if ( $this->options_list ){
			$field = '<select id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" '.$multiple.' value="' . $this->info['value'] . '">'. $this->options_list.'</select>';
		} else {
			$field = 'No options available.';
		}
		return $field ;
	}
	private function get_options_list( $args ){
		$html = '';
		if ( isset( $args['info'][ 'options' ] ) ){
			$options = $args['info'][ 'options' ] ; 

			if ( is_array( $options ) && sizeof( $options ) > 0 ){
				if ( $this->multiple ){
					foreach( $posts as $post ){ 
						if ( is_array( $this->info['value'] ) && in_array( $post->ID, $this->info['value'] )){
							$selected = 'selected';
						} else {
							$selected = '';
						}					
						$html .= '<option '.$selected .' value="'.$post->ID.'">'.$post->post_title.'</option>' ; 
					}				
				} else {
					$html .= $this->multiple ? '' : '<option value="">Please select one...</option>'; 
					foreach( $options as $value => $option ){ 
						if ( $this->info['value'] == $value ){
							$selected = 'selected';
						} else {
							$selected = '';
						}
						$html .= '<option '.$selected.' value="'.$value.'">'.$option.'</option>' ; 
					}
				}
			} else {
				if ( post_type_exists( $options ) ){
					$posts = get_posts( array(
						'numberposts' => -1, 
						'post_type'		=> $options
					) );
					if ( sizeof( $posts ) > 0 ){
						if ( $this->multiple ){
							foreach( $posts as $post ){ 
								if ( is_array( $this->info['value'] ) && in_array( $post->ID, $this->info['value'] )){
									$selected = 'selected';
								} else {
									$selected = '';
								}					
								$html .= '<option '.$selected .' value="'.$post->ID.'">'.$post->post_title.'</option>' ; 
							}
						} else {
							$html .= $this->multiple ? '' : '<option>Please select one...</option>'; 
							foreach( $posts as $post ){ 
								if ( $this->info['value'] == $post->ID ){
									$selected = 'selected';
								} else {
									$selected = '';
								}					
								$html .= '<option '.$selected .' value="'.$post->ID.'">'.$post->post_title.'</option>' ; 
							}						
						}
					}
				}
			}
		}
		return $html;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	
	public function standard ( $args ){
		?>
		<tr valign="top" <?php echo $this->attributes; ?>>
			<th scope="row"><?php echo $this->label; ?></th>
			<td <?php echo $this->attributes; ?>>
				<?php echo $this->field; ?>
				<p><?php echo $this->description; ?></p>
			</td>
		</tr>
		<?php
	}
	public function expandable( $args ){
		$field_info = parent::get_field_info($args);
			
	}
	public function custom( $args ){
		$layout_details = $this->info['layout']; ?>
		<div <?php echo $this->attributes; ?>>
		<?php
		switch ( $layout_details['label'] ){
			case 'left' : ?>
				<p><?php echo $this->label; ?><?php echo $this->field; ?></p>
				<p><?php echo $this->description; ?></p>
				
				<?php 
				break;
				
			case 'right' : ?>
				<p><?php echo $this->label; ?><?php echo $this->field; ?></p>
				<p><?php echo $this->description; ?></p>

				<?php 
				break;

			case 'top' : ?>
				<p><?php echo $this->label; ?></p>
				<p><?php echo $this->field; ?></p>
				<p><?php echo $this->description; ?></p>

				<?php 
				break;	
			default : ?>
				<p><?php echo $this->label; ?></p>
				<p><?php echo $this->field; ?></p>
				<p><?php echo $this->description; ?></p>

				<?php 
				break; 
		} ?>
		</div>
		<?php
	}
	
}