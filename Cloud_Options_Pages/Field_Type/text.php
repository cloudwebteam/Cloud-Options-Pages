<?php 
class text extends Field_Type {
	protected $info ;
	protected $size = 45; 
	protected $field ;
	protected $label ;
	protected $attributes = array() ;

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : $this->size; 	
		$field = '<input type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';	
		return $field;
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
		<div <?php echo $this->attributes; ?>>
		<?php
		switch ( $layout_details['label'] ){
			case 'left' : ?>
				<?php echo $this->label; ?><?php echo $this->field; ?></p>
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
		} ?>
		</div>
		<?php
	}
	
}