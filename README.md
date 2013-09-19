[fields]: https://github.com/cloudwebteam/Cloud-Options-Pages/blob/master/Cloud/Cloud_Forms/defaults.php "Field Defaults. Cloud_Forms/defaults.php"
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
2. call `Cloud_Loader::init( [FOLDERS] )` where FOLDERS can be empty, a string (single folder), or an array of folders

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

Cloud_Forms
===========

	
Differences between Cloud_Forms_StandAlone (SA) and Cloud_Forms_WP (WP):
- SA has no Wordpress specific fields, eg. media, post, wysiwyg
- SA fields have no 'code_link', 'editor_list', or '_lock' options
- SA uses the Cloud_Loader's enqueueing functions, WP uses Wordpress's
- SA has `->add_forms()`
- WP has `->add_pages()` and `->add_metaboxes()` and `add_forms()`
- SA requires calling `->head()` to print form styles and scripts

Basically, WP is for when you are using Wordpress, and need metaboxes/options pages. 
Otherwise, StandAlone is a good choice.

**To Use** 

1. Include Cloud_Forms using Cloud_Loader

	```php 
	include 'Cloud.php';
	Cloud_Loader::init( 'Cloud_Forms' );
	```
2. Initialize the appropriate class (need WP metaboxes/pages?), and store in a global variable

	```php
	$Forms = Cloud_Forms_StandAlone::get_instance()
	// or 
	$Forms = Cloud_Forms_WP::get_instance()
	```
	
### Adding Forms
To add a form, call either `->add_forms( $array_of_forms )` **OR** `->add_forms( 'form_slug', $args )`

**Simple, one section form**

```php 
'form_slug' => array(
	// form options
	'title'			=> 'Default Form Title',
	'layout'		=> 'standard', // 'table', '<custom html (see docs)>'
	'description'		=> null,
	'ajax'			=> false, // if enabled, will do real-time ajax validation
	'submit_text' 		=> 'Save', // text of submit button
	'hide_on_success' 	=> false, // show the form once successfully submitted?
	'success'       	=> 'Form successfully validated and sent', // success message
	'success_function' 	=> false, // php function to call on successful submit. (Passed to call_user_fnct())
	'success_function_js' 	=> false, // global javascript function to call on successful submit
	'fields' 		=> array(
		'field1' 	=> array( 
			// field options (see docs)
		)
	)
)
```

[See field options and explanation][fields]

**Multi-section form**

```php 
'form_slug' => array(
	// form options
	'title'			=> 'Default Form Title',
	'layout'		=> 'standard', // 'standard', 'tabs', 'tabs_animated'
	'description' 		=> null,
	'ajax' 			=> false, // if enabled, will do real-time ajax validation
	'submit_text' 		=> 'Save', // text of submit button
	'hide_on_success' 	=> false, // show the form once successfully submitted?
	'success'       	=> 'Form successfully validated and sent', // success message
	'success_function' 	=> false, // php function to call on successful submit. (Passed to call_user_fnct())
	'success_function_js' 	=> false, // the name of a javascript function to call on successful submit(must be global, clearly)
	'sections'		=> array (
		'section1' 	=> array(
			'title'		=> 'Default Section Title',
			'layout'	=> 'standard', // 'table', '<custom html (see docs)>'
			'description'	=> false,
			'fields' 	=> array(
				'field1'	=> array( 
					// field options (see docs)
				),
			)
		)
	)
)
```
[See field options and explanation][fields]

#### Displaying Forms

1. use `->add_forms( $array )` to add your forms
2. call `->head()` in the header of your site
3. call `->display( 'form_slug' )` to display the desired form

**Note:** It is wise to only add a form on pages where you will need it, otherwise you'll be doing a bunch of processing and script loading for a form you don't need.

### Adding Options Pages
To add an options page, call `->add_pages( $array )`
The array should have this form

```php 
array(
	'first_page' => array( // top level slug
		'image' 	=> 'IMAGE_URL', // Full path to the menu icon
		'priority'  	=> 52, // Where in the menu it is located ( 0 = top )
		'subpages' => array(
			'first_page' => array( // first subpage slug must match top level slug
				// page options
				'title'		=> 'Page Title',
				'menu_title' 	=> 'Menu title of page', 
				'capability'	=> 'create_users', // limit to users with capability (optional)
				'layout'	=> 'standard', // tabs, tabs_animated, standard (optional)
				'description'	=> false, // (optional)
				'sections' 	=> array(
					'section1' => array(
						'title'		=> 'Default Section Title',
						'layout'	=> 'standard', // 'table', '<custom html (see docs)>'
						'description'	=> null,
						'fields' 	=> array(
							'field1' => array( 
								// field options (see docs)
							),
						)
					),
					'section2' => array(
						// ... same as above
					)
				)
			),
			'subpage2' => array( // slug can be whatever
				// ... same as above
			)
		)
	),
	'page2' => array(
		// ... same as above
	)
);
```

[See field options and explanation][fields]

#### Displaying Pages

Automatically added to WP's menus.

### Adding Metaboxes
To add a metabox, call `->add_metaboxes( $to_what, $array [, $context][, $priority ] )`

The `$to_what`: The first argument specifies which posts to add the metabox to.

It can be 

1. A post ID
2. A post title (NOTE: *all* posts that match will get the metabox)
3. A post slug (NOTE: *all* posts that match will get the metabox)
4. The same arguments accepted by Wordpress's `get_posts()`, with the following additions
	- `'template' => 'template name'`, 
		- a page template file name, with or without the '.php'. eg. 'tpl-photo-gallery', or 'tpl-photo-gallery.php'
		- A registered page template name. eg. 'Photo Gallery', or 'Left Sidebar'
	- `'exclude_template' => 'template name'`
		- a page template file name, with or without the '.php'. eg. 'tpl-photo-gallery', or 'tpl-photo-gallery.php'
		- A registered page template name. eg. 'Photo Gallery', or 'Left Sidebar'

*Note: You can use this same array to add/remove post supports*


1. `$Forms->add_supports( $to_what, (string/array) );`
	- eg. `$Forms->add_supports( 'Sample Page', 'editor' )`
	- eg. `$Forms->add_supports( 'Sample Page', array( 'editor', 'revisions', 'author' ) )`
2. `$Forms->remove_supports( $to_what, (string/array) );`
	- eg. `$Forms->remove_supports( 'Sample Page', 'editor' )`
	- eg. `$Forms->remove_supports( 'Sample Page', array( 'editor', 'revisions', 'author' ) )`

The *metabox array* should have this form

```php 
'metabox_slug' => array(
	'title'		=> 'Default Metabox Title',
	'layout'	=> 'standard', // 'table', '<custom html (see docs)>'
	'description'	=> null,
	'fields' 	=> array(
		'field1' => array( 
			// field options (see docs)
		),
	)
)
```

[See field options and explanation][fields]

#### Displaying Metaboxes

Automatically added to all posts matching the `$to_what`


