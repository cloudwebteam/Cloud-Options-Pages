<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_divider extends Cloud_Field {
	protected $size = 50; 

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		return false ;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	
	public function standard ( ){ ?>
		<tr valign="top" <?php echo $this->attributes; ?>>
			<th scope="row" colspan="2"><?php echo $this->label; ?></th>
		</tr>
		<?php
	}	
}