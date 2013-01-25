<?php 
	class Metabox_Layout extends Layout {
		public static function get_layout_function( $layout = null , $sub_classname = null , $parent_layout = null ){
			$layout_function = parent::get_layout_function( $layout , $sub_classname );
		
			return $layout_function; 
		}	
		private static function get_layout_info( $args ){

			$metabox = $args['args'] ;
			$metabox_title = $args['title'];
						
			$metabox_info = array();
			
			// place items with particular importance for every layout here. 
			$metabox_info['id']		= $args['id'] ; 
			$metabox_info['title'] 	= $args['title'] ; 
			$metabox_info['fields'] = Cloud_Metaboxes::get_fields( $args['id'], $metabox );
						
			if ( isset( $metabox['description'] ) && $metabox['description'] ){
				$metabox_info['description'] = '<span class="description">'.$metabox['description'] .'</span>';
			} else {
				$metabox_info['description'] = '';
			}
			
			//set up section classes
			$classes = array(); 
			$classes[] = 'cloud' ;
			$classes[] = 'metabox';
			$classes[] = 'cloud-options';
			$classes[] = $metabox['metabox_context'] ;
			$classes[] = $metabox['layout'] ; 
			if ( $metabox['_has_settable_defaults'] ){
				$classes[] = 'has-settable-defaults' ;
			}					

			$metabox_info['classes'] = implode ( ' ', $classes ); 
			
			return $metabox_info; 		
		}
		public function standard( $post = '', $args = '' ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $args ) );
			ob_start();
			?>

			    <div class="<?php echo $classes; ?>">
					<div class="header">			    
				    	<?php echo $description; ?>
					</div>
					<div class="row-fluid">
				    <?php echo $fields; ?>
					</div>
				</div>
			<?php	
			$output = ob_get_clean();
			echo $output;
			
		}
								
	}
?>