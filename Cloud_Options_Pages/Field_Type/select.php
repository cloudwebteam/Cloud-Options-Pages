<?php 
class select extends Field_Type {
	protected $info ;
	protected $field ;
	protected $label ;
	protected $options = 'post' ; 
	protected $attributes = '' ;
	protected $multiple ; 
	protected function __construct( $args ){
		$this->info = parent::get_field_info($args);
		$this->multiple = isset( $args['info']['multiple'] ) ? $args['info']['multiple'] : false ; 
		$this->options_list = $this->get_options_list( $args );
		if ( $this->multiple ){
			$multiple = ' multiple="multiple"' ; 
			$this->info['name'] = $this->info['name'] . '[]'; 
		} else {
			$multiple = '';
		}
		if ( $this->options_list ){
			$this->field = '<select id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" '.$multiple.' value="' . $this->info['value'] . '">'. $this->options_list.'</select>';
		} else {
			return false;
		}
		
 
		//$this->field = '<input type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';
		
		parent::__construct( __CLASS__, $args ); 	
	}
	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
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
					$html .= $this->multiple ? '' : '<option>Please select one...</option>'; 
					foreach( $options as $value => $option ){ 
						if ( $this->info['value'] === $value ){
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
						'numberposts' => 0, 
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
	public function standard ( $args ){
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->label; ?></th>
			<td <?php echo $this->attributes; ?>>
				<?php echo $this->field; ?>
				<?php echo $this->description; ?>
			</td>
		</tr>
		<?php
	}
	public function expandable( $args ){
		$field_info = parent::get_field_info($args);
			
	}
	public function custom( $args ){
		$layout_details = $this->info['layout']; 

		switch ( $layout_details['label'] ){
			case 'left' : ?>
				<p><?php echo $this->label; ?><?php echo $this->field; ?></p>
				<?php echo $this->description; ?>
				
				<?php 
				break;
				
			case 'right' : ?>
				<p><?php echo $this->label; ?><?php echo $this->field; ?></p>
				<?php echo $this->description; ?>

				<?php 
				break;

			case 'top' : ?>
				<p><?php echo $this->label; ?></p>
				<p><?php echo $this->field; ?></p>
				<?php echo $this->description; ?>

				<?php 
				break;	
			default : ?>
				<p><?php echo $this->label; ?></p>
				<p><?php echo $this->field; ?></p>
				<?php echo $this->description; ?>

				<?php 
				break; 
		}
	}
	
}