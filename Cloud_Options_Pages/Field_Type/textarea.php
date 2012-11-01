<?php 
class textarea extends Field_Type {
	private $rows = 5;
	private $cols = 70;

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		$this->info = parent::get_field_info($args);
		
		$this->rows = isset( $args['info']['rows'] ) ? $args['info']['rows'] : $this->rows; 
		$this->cols = isset( $args['info']['cols'] ) ? $args['info']['cols'] : $this->cols; 
		
		$field = '<textarea id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" rows="'.$this->rows.'" cols="'.$this->cols.'" >' . $this->info['value'] . '</textarea>';
		
		return $field;
	}
	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/		
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