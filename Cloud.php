<?php 
// ERROR REPORTING
define( 'Cloud_dir','http://localhost/cloud/site3/wp-content/themes/Cloud_Boilerplate1/Cloud' ); 
define( 'Cloud_ABS', dirname( __FILE__ ) . '/Cloud' ); 
define( 'Cloud_prefix' , 'Cloud_' );
class Cloud_Loader {
	protected static $instance; 
	protected static $ABS = Cloud_ABS ;
	protected static $dir = Cloud_dir ;
	// what directories, in addition to the one with this class name, would you like to load?
	protected $directories_to_load = array('Cloud_Forms');	

	private function __construct(){
		// loads folder with this class's name	
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
		$load_folder = $folder ?  self::$ABS . '/' . $folder : self::$ABS ;
		$load_list = array( ) ;
		$load_list = array_merge( $load_list , self::get_folder_files( $load_folder ) );		

		$main_file = $load_folder . '/'.$folder .'.php'; 
		if ( file_exists( $main_file ) ){
			include_once ( $main_file );
		}
		foreach ( $load_list as $file ){
			if ( $file !== $main_file ){
				include_once $file;	
			}
		}
	}
	protected static function get_folder_files( $folder_abs_path ){
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
}
Cloud_Loader::get_instance();
