<?php 
// ERROR REPORTING
ini_set('display_errors',1); 
error_reporting(E_ALL);
error_reporting(-1);
	
define( 'Cloud_dir', 'localhost/cloud/wp-content/themes/Cloud_Boilerplate' ); 
define( 'Cloud_ABS', dirname( __FILE__ ) ); 
define( 'Cloud_prefix' , 'Cloud_' );
class Cloud_Loader {
	protected static $instance; 
	protected static $ABS ;
	protected static $main_folder = 'Cloud';
	// what directories, in addition to the one with this class name, would you like to load?
	protected $directories_to_load = array('Field');	

	private function __construct(){
		self::$ABS = Cloud_ABS .'/'.self::$main_folder; 
		// loads folder with this class's name	
		$this->load_directory(); 	
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
		foreach ( $load_list as $file ){
			include_once $file;
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
