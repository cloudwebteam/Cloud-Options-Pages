#### Cloud Forms consists of: 
- the **Cloud Loader**
- the **Cloud Forms** framework
	- a **stand-alone version**
	- a **WP version**, with the ability to add
		- options pages
		- metaboxes
		- regular forms for use throughout the site.

Cloud_Loader
===========
The Cloud loader provides a convenient way to auto-load files, folders, and enqueue scripts and styles. 

On init, it will autoload 

1. all the files directly inside of the Cloud folder
2. the files directly inside of whichever folders are specified (eg 'Cloud_Forms' will load Cloud/Cloud_Forms ). 

**To use**

1. `include 'Cloud.php'`
2. call `Cloud_Loader::init( [FOLDERS] )` where FOLDERS can be empty, a string, or an array of folders

**Manual loading methods**

1. `->load_directories( (array)[DIRS] )`
2. `->load_directory( DIR_NAME )`

**Manual style and script enqueuing**
The loader follows Wordpress's model for registering and enqueuing scripts. It functions identically. 

1. `->register_script( HANDLE, PATH [, deps ] )`
2. `->enqueue_script( HANDLE [, PATH] [, deps ] )` (doesn't need path if already registered)
3. `->register_style( HANDLE, PATH [, deps ] )`
4. `->enqueue_style( HANDLE [, PATH] [, deps ] )` (doesn't need path if already registered) 
5. `->add_global_js_var( NAME, VALUE )` similar to wp_localize_script, but prints before ALL scripts.
6. `->print_scripts()` will print all the scripts in the appropriate order. Typically in head or footer.
7. `->print_styles()` will print all the styles in the appropriate order. Typically in head.
