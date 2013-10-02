<?php 
define( 'Cloud_dir', get_stylesheet_directory_uri() . '/Cloud'  ); 
define( 'Cloud_ABS', dirname( __FILE__ ) . '/Cloud'  ); 
define( 'Cloud_prefix' , 'Cloud_' );
class Cloud_Loader {
	protected static $instance; 
	protected $ABS = Cloud_ABS ;
	protected $dir = Cloud_dir ;
	public function ABS(){
		return $this->ABS; 
	}
	public function dir(){
		return $this->dir; 
	}	
	// what directories, in addition to the one with this class name, would you like to load?
	protected $registered_styles ; 
	protected $styles ; 
	protected $styles_have_been_printed	; 
	protected $registered_scripts ; 
	protected $scripts ; 
	protected $scripts_have_been_printed ; 
	protected $global_js_vars ; 
	private function __construct( $directories = false ){
		// loads folder with this class's name	
		$this->load_directory(); 	
		$this->load_directories( $directories );
 	}
 	
	public static function init( $directories = array() ){
		if ( $directories && is_string( $directories ) ){
			$directories = array( $directories ); 
		}

		if ( !self::$instance ){
			self::$instance = new self( $directories );
		}
		return self::$instance; 	
	}
	public static function get_instance(){
		return self::init();
	}	
	/***====================================================================================================================================
		HANDLE LOADING OF FILES in DIRECTORIES
	==================================================================================================================================== ***/
	public function load_directories( $directories = array() ){
		if ( ! $directories ){
			return ;
		}
		foreach( $directories as $directory_name ){
			$this->load_directory( $directory_name );
		}
	}
	public function load_directory( $folder = false ){ 
		$load_folder = $folder ?  $this->ABS . '/' . $folder : $this->ABS ;
		$load_list = array( ) ;

		$load_list = array_merge( $load_list , $this->get_folder_files( $load_folder ) );		
		
		$main_file = $load_folder . '/'.basename( $folder ) . '.php' ;
		if ( sizeof( $load_list ) > 0 ){
			if ( file_exists( $main_file ) ){		
				include_once ( $main_file );
			}
			foreach ( $load_list as $file ){
				if ( $file !== $main_file ){
					include_once $file;	
				}
			}
		}
	}
	protected function get_folder_files( $folder_abs_path ){
		$files = array( ) ;
		if ( !$dir = @opendir( $folder_abs_path ) ){
			return $files;
		} 
		while ( false !== $file = readdir( $dir ) ){
			if ( '.' == substr( $file, 0, 1 ) || '.php' != substr( $file, -4 ) ){
				continue;
			}
			$file = $folder_abs_path.'/'.$file ;
			if ( is_file( $file ) ){
				$files[] = $file;				
			}
		}
		closedir( $dir );
		return $files;
	}	
	
	/***====================================================================================================================================
			LOADING SCRIPTS AND STYLES
		==================================================================================================================================== ***/
	public function enqueue_script( $handle, $path = false , $dependencies = false ){
		if ( $path ){
			$item_to_enqueue = array(
				'handle' => $handle, 
				'path' 		=> $path,
				'dependencies' => $dependencies
			);	
		} else {
			if ( isset( $this->registered_scripts[ $handle ] ) ){
				$item_to_enqueue = $this->registered_scripts[ $handle ] ;
			}
		}
		if( isset( $item_to_enqueue ) ){
			$this->scripts[ $handle ] = $item_to_enqueue ;
			if ( $item_to_enqueue['dependencies'] ){
					foreach( $item_to_enqueue['dependencies'] as $dep ){
						if ( !isset($this->scripts[ $dep ]) && isset( $this->registered_scripts[ $dep ])){
							$this->scripts[ $dep ] = $this->registered_scripts[ $dep ];
						}
					}
			}
		} 
	}
	public function enqueue_style( $handle, $path = false, $dependencies = false ){
		if ( $path ){
			$item_to_enqueue = array(
				'handle' => $handle, 
				'path' 		=> $path,
				'dependencies' => $dependencies
			);	
		} else {
			if ( isset( $this->registered_styles[ $handle ] ) ){
				$item_to_enqueue = $this->registered_styles[ $handle ] ;
				if ( $item_to_enqueue['dependencies'] ){
					foreach( $item_to_enqueue['dependencies'] as $dep ){
						if ( !isset($this->styles[ $dep ]) && isset( $this->registered_styles[ $dep ])){
							$this->styles[ $dep ] = $this->registered_styles[ $dep ];
						}
					}
				}				
			}
		}
		if( isset( $item_to_enqueue ) ){
			$this->styles[ $handle ] = $item_to_enqueue ;

		} 
	}
	public function register_script( $handle, $path, $dependencies = false ){
		$item_to_enqueue = array(
			'handle' => $handle, 
			'path' 		=> $path,
			'dependencies' => $dependencies
		);	
		
		$this->registered_scripts[ $handle ] = $item_to_enqueue ;	
	}
	public function register_style( $handle, $path, $dependencies = false ){
		$item_to_enqueue = array(
			'handle' => $handle, 
			'path' 		=> $path,
			'dependencies' => $dependencies
		);	
		$this->registered_styles[ $handle ] = $item_to_enqueue ;
	}
	public function add_global_js_var( $name, $value ){
		$this->global_js_vars[ $name ] = $value; 
	}
	public function print_styles(){
		if ( $this->styles_have_been_printed ) return ; 	
		array_walk( $this->styles, array( $this, 'filter_out_styles_without_needed_dependencies'), $this->styles );
		$this->styles = $this->sort_array_by_dependencies( $this->styles );	
			
		foreach( $this->styles as $style ){ ?>
		<link rel="stylesheet" href="<?php echo $style['path']; ?>" />
		<?php }
		$this->styles_have_been_printed = true; 		
	}
	public function print_scripts(){ 
		if ( $this->scripts_have_been_printed ) return ; 

		if ( $this->global_js_vars ){ ?>
		<script>
			/* <! [CDATA[ */
			<?php foreach( $this->global_js_vars as $var => $value ){ ?>
			var <?php echo $var; ?> = <?php echo json_encode( $value ) ; ?>;			
			<?php } ?>
			/* []> */			
		</script>
		<?php }
		
		array_walk( $this->scripts, array( $this, 'filter_out_scripts_without_needed_dependencies'), $this->scripts );
		$this->scripts = $this->sort_array_by_dependencies( $this->scripts );	
				
		foreach( $this->scripts as $script ){ ?>
		<script src="<?php echo $script['path']; ?>" ></script>
		<?php }
		$this->scripts_have_been_printed = true; 
	}	
	protected function sort_array_by_dependencies( $array_to_sort ){
		$sorted_array = array();
		$i = 0;
		while ( sizeof( $array_to_sort ) > 0 && $i < 100 ){ 
			$i++;
			foreach( $array_to_sort as $handle => $item ){
				$all_dependencies_present = true; 
		
				if ( is_array( $item['dependencies'] ) && sizeof( $item['dependencies'] ) > 0 ){
					foreach( $item['dependencies'] as $dependency_handle ){
						if ( ! isset( $sorted_array[$dependency_handle] ) ){
							$all_dependencies_present = false ;
							break;
						}
					}
				}
				if ( $all_dependencies_present ){
					$sorted_array[ $handle ] = $item ;
					unset( $array_to_sort[ $handle ] );					
				}
			}
		}
		return $sorted_array ;
	}
	protected function filter_out_styles_without_needed_dependencies( &$item, $key, &$array ){
		if ( is_array( $item['dependencies'] ) ){
			foreach( $item['dependencies'] as $dependency ){
				if ( ! isset( $array[ $dependency ] ) ){
					if ( isset( $this->registered_styles[ $dependency ] ) ){
						$array[ $dependency ] = $this->registered_styles[ $dependency ] ;
					} else {
						unset( $array[ $key ] );
						break;
					}
				} 
			}
		}
	}		
	protected function filter_out_scripts_without_needed_dependencies( &$item, $key, &$array ){

		if ( is_array( $item['dependencies'] ) ){
			foreach( $item['dependencies'] as $dependency ){
				$dependency_found = false;
				if ( ! isset( $array[ $dependency ] ) ){				
					if ( isset( $this->registered_scripts[ $dependency ] ) ){
						$array[ $dependency ] = $this->registered_scripts[ $dependency ] ;
					} else {
						unset( $array[ $key ] );
						break;						
					}
				} 
			}
		}
	}
	
	/***====================================================================================================================================
			REGISTER SOME COMMON SCRIPTS AND STYLES 
		==================================================================================================================================== ***/

	
	public function get_registered_styles(){
		return $this->registered_styles; 
	}
	public function get_styles(){
		return $this->styles;
	}
	public function get_registered_scripts(){
		return $this->registered_scripts; 
	}
	public function get_scripts(){	
		return $this->scripts;	
	}	
	
}