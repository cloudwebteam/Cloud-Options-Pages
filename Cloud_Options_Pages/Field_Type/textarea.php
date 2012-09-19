<?php 
class textarea extends Field_Type {
	private $rows = 5;
	private $cols = 70;
	protected function __construct( $args ){
		
		$this->info = parent::get_field_info($args);

		$this->rows = isset( $this->info['rows'] ) ? $this->info['rows'] : $this->rows; 
		$this->cols = isset( $this->info['cols'] ) ? $this->info['cols'] : $this->cols; 
		
		$this->field = '<textarea id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" rows="'.$this->rows.'" cols="'.$this->cols.'" >' . $this->info['value'] . '</textarea>';
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
			<th scope="row"><?php echo $this->label; ?>
			<td>
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
		$field_info = parent::get_field_info($args);
		$layout_details = $field_info['layout']; 
		
		$field = '<input type="text" id="'.$field_info['prefix'] . $field_info['id'] . '" name="'.$field_info['name'] . '" size="'.$size.'" type="text" value="' . $field_info['value'] . '" />';
		$label = "<label for='".$field_info['prefix'] . $field_info['id'] . "' >" . $field_info['title'] . "</label>";
		switch ( $layout_details['label'] ){
			case 'left' : ?>
				<p><?php echo $label; ?><?php echo $field; ?></p>
				<?php 
				break;
				
			case 'right' : ?>
				<p><?php echo $field; ?><?php echo $label; ?></p>
				<?php 
				break;

			case 'top' : ?>
				<p><?php echo $label; ?></p>
				<p><?php echo $field; ?></p>
				<?php 
				break;	
		}
	}
	
}