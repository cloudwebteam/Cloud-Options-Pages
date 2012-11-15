<?php 
class color extends Field_Type {
	protected $info ;
	protected $size = 45; 
	protected $field ;
	protected $label ;
	protected $url_button ; 
	protected $attributes = 'class="color-picker"' ;

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}
	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}	

	protected function get_field_html( $args ){	
		$this->enabled = true;	
		$hidden = $this->enabled ? '' : 'hidden' ; 
		$field = '<span class="option '.$hidden.'"><input size="5" class="color-picker-input miniColors" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'].'" type="text" value="'.$this->info['value'].'" /></span>';
		return $field;
	}
	protected function get_field_components( $args ){
		$has_default = true; 
		if ( $has_default ){
			$this->default_swab = '<span class="default-swab" title="Click for default color" style="background:'.$this->info['value'] .'">df</span>' ;
		} else { 
			$this->default_swab = '<span class="no-default">no</span>';
		}	
		
		$this->enabler = '<input type="checkbox" name="'. $this->info['name'].'" class="option_enabler color" />';
	}
	public function enqueue_field_scripts_and_styles(){
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
		wp_enqueue_script( 'miniColors', parent::get_include_path(). '/_js/jquery.miniColors.min.js', array( 'jquery' ) ); 
		wp_enqueue_style( 'miniColors', parent::get_include_path(). '/_css/jquery.miniColors.css' ); 
		
	}
	
	private function get_image(){
		if ( isset( $this->info['value'] ) && $this->info['value'] !== '' ){
			return '<img class="preview-image img-polaroid" src="'.$this->info['value'].'" title="'.$this->info['value'].'" />';	
		} else {
			return '<img class="hidden preview-image img-polaroid" title="'.$this->info['value'].'" />';	
		}
	}	
  
  
  
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	public function standard ( $args ){
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->label; ?></th>
			<td <?php echo $this->attributes; ?>>
				<?php echo $this->default_swab; ?><?php echo $this->field; ?><?php echo $this->enabler; ?>
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
		?>
			<div <?php echo $this->attributes; ?>>
				<p><?php echo $this->label; ?></p>			
				<p><?php echo $this->default_swab; ?><?php echo $this->field; ?><?php echo $this->enabler; ?></p>
				<p><?php echo $this->description; ?></p>
			</div>		
		<?php
	}
	
}