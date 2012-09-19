<?php 
class text extends Field_Type {
	private $info ;
	private $size = 45; 
	private $field ;
	private $label ;
	private $attributes = array() ;

	private function __construct( $args ){
		
		$this->info = parent::get_field_info($args);

		$this->size = isset( $this->info['size'] ) ? $this->info['size'] : $this->size; 
		$this->field = '<input type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';
		$this->label = parent::get_label( $this->info ); 
		$this->description = isset( $this->info['description']) && $this->info['description'] !== '' ? '<p class="description">'.$this->info['description'] . '</p>' : '';

		$layout = parent::get_layout( __CLASS__, $this->info );
		$this->$layout( $args ); 
	}
	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
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
		}
	}
	
}