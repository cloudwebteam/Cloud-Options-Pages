<?php 
// ERROR REPORTING
define( 'Cloud_dir', SITE_URL . '/Cloud' ); 
define( 'Cloud_ABS', dirname( __FILE__ ) . '/Cloud' ); 
define( 'Cloud_prefix' , 'CC_' );
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
	protected $directories_to_load = array('Cloud_Forms', 'DB');	

	protected $styles; 
	protected $scripts; 
	protected $global_js_vars ; 
	private function __construct(){
		// loads folder with this class's name	
		$this->register_common_scripts();
		$this->register_common_styles();		
		
		$this->load_directory(); 	
		$this->load_directories();
 	}
	public static function init(){
		return self::get_instance();
	}
	public static function get_instance(){
		if ( !self::$instance ){
			self::$instance = new self();
		}
		return self::$instance; 	
	}
	/***====================================================================================================================================
		HANDLE LOADING OF FILES in DIRECTORIES
	==================================================================================================================================== ***/
	public function load_directories( $directories = array() ){
		foreach( $this->directories_to_load as $directory_name ){
			$this->load_directory( $directory_name );
		}
	}
	public function load_directory( $folder = false ){ 
		$load_folder = $folder ?  $this->ABS . '/' . $folder : $this->ABS ;
		$load_list = array( ) ;
		$load_list = array_merge( $load_list , $this->get_folder_files( $load_folder ) );		
		
		$main_file = $load_folder . '/'.basename( $folder ) . '.php' ;
	
		if ( file_exists( $main_file ) ){		
			include_once ( $main_file );
		}
		foreach ( $load_list as $file ){
			if ( $file !== $main_file ){
				include_once $file;	
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
		array_walk( $this->styles, array( $this, 'filter_out_styles_without_needed_dependencies'), &$this->styles );
		$this->styles = $this->sort_array_by_dependencies( $this->styles );	
			
		foreach( $this->styles as $style ){ ?>
		<link rel="stylesheet" href="<?php echo $style['path']; ?>" />
		<?php }
	}
	public function print_scripts(){ 
		if ( $this->global_js_vars ){ ?>
		<script>
			/* <! [CDATA[ */
			<?php foreach( $this->global_js_vars as $var => $value ){ ?>
			var <?php echo $var; ?> = <?php echo json_encode( $value ) ; ?>;			
			<?php } ?>
			/* []> */			
		</script>
		<?php }
		
		array_walk( $this->scripts, array( $this, 'filter_out_scripts_without_needed_dependencies'), &$this->scripts );
		$this->scripts = $this->sort_array_by_dependencies( $this->scripts );	
				
		foreach( $this->scripts as $script ){ ?>
		<script src="<?php echo $script['path']; ?>" ></script>
		<?php }
	}	
	protected function sort_array_by_dependencies( $array_to_sort ){
		$sorted_array = array();
		while ( sizeof( $array_to_sort ) > 0 ){ 
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
	protected function register_common_scripts(){
		self::register_script( 'jquery', 'http://code.jquery.com/jquery-1.9.1.min.js' ); 
		self::register_script( 'bootstrap', $this->dir .'/__inc/bootstrap/js/bootstrap.min.js', array( 'jquery' ) );  
		self::register_script( 'scrollTo', $this->dir .'/__inc/jquery.scrollTo.min.js', array( 'jquery' ) );  
		
		// full jQuery UI
		self::register_script( 'jquery-ui-core', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js', array('jquery') );
		self::register_script( 'jquery-effects-core', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect.min.js', array('jquery') );
		self::register_script( 'jquery-effects-blind', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-blind.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-bounce', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-bounce.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-clip', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-clip.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-drop', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-drop.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-explode', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-explode.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-fade', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-fade.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-fold', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-fold.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-highlight', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-highlight.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-pulsate', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-pulsate.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-scale', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-scale.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-shake', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-shake.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-slide', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-slide.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-transfer', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-transfer.min.js', array('jquery-effects-core') );
	
		self::register_script( 'jquery-ui-accordion', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.accordion.min.js', array('jquery-ui-core', 'jquery-ui-widget') );
		self::register_script( 'jquery-ui-autocomplete', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.autocomplete.min.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-menu') );
		self::register_script( 'jquery-ui-button', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.button.min.js', array('jquery-ui-core', 'jquery-ui-widget') );
		self::register_script( 'jquery-ui-datepicker', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.datepicker.min.js', array('jquery-ui-core') );
		self::register_script( 'jquery-ui-dialog', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js', array('jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position') );
		self::register_script( 'jquery-ui-draggable', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-droppable', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js', array('jquery-ui-draggable') );
		self::register_script( 'jquery-ui-menu', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.menu.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ) );
		self::register_script( 'jquery-ui-mouse', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js', array('jquery-ui-widget') );
		self::register_script( 'jquery-ui-position', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js', array('jquery') );
		self::register_script( 'jquery-ui-progressbar', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.progressbar.min.js', array('jquery-ui-widget') );
		self::register_script( 'jquery-ui-resizable', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.resizable.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-selectable', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.selectable.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-slider', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.slider.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-sortable', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.sortable.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-spinner', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.spinner.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-button' ) );
		self::register_script( 'jquery-ui-tabs', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js', array('jquery-ui-core', 'jquery-ui-widget') );
		self::register_script( 'jquery-ui-tooltip', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.tooltip.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ) );
		self::register_script( 'jquery-ui-widget', $this->dir . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js', array('jquery') );

	}
	protected function register_common_styles(){	
		self::register_style( 'reset' , $this->dir .'/__inc/reset.css' );
		self::register_style( 'bootstrap', $this->dir .'/__inc/bootstrap/css/bootstrap.min.css' ); 
		self::register_style( 'bootstrap-responsive', $this->dir .'/__inc/bootstrap/css/bootstrap-responsive.min.css', array( 'bootstrap' ) ); 
		
		self::register_style( 'bootstrap-timepicker', $this->dir. '/__inc/bootstrap_timepicker/bootstrap-timepicker.min.css');
		self::register_style( 'jquery-ui-lightness', $this->dir . '/__inc/jquery-ui-1.10.1.custom/css/ui-lightness/jquery-ui-1.10.1.custom.min.css' ); 
	
	}		
}
Cloud_Loader::get_instance();
