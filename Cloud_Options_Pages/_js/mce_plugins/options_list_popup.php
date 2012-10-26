<?php
	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once( $parse_uri[0] . 'wp-load.php' );

	$info = MCE_Plugins::$options_list_info;
	?>
	<link href="css/options_list.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript">
 
	var OptionsListDialog = {
		local_ed : 'ed',
		init : function(ed) {		
			OptionsListDialog.local_ed = ed;
			tinyMCEPopup.resizeToInnerSize();		
			var popup_body = jQuery(tinyMCEPopup.dom.doc.body); // the popup body
			console.log( popup_body );
			tinyMCEPopup.params.mce_height = popup_body.outerHeight() + 25;
            tinyMCEPopup.params.mce_width = popup_body.outerWidth();			
		},
		
		insert : function insertShortcode(ed, selected) {
			 
			// Get Properties
			var shortcode = selected.data('shortcode');
			 
			// parse styles
			tinyMCEPopup.execCommand('mceReplaceContent', false, shortcode);
			// Return
			tinyMCEPopup.close();
		}
	};
	tinyMCEPopup.onInit.add(OptionsListDialog.init, OptionsListDialog);
	jQuery( function($){
		$('.option_shortcode').click( function(){ 
			OptionsListDialog.insert( OptionsListDialog.local_ed, $(this) ) 
		}); 
	});
	</script>
	<div id="options-list">

	<?php	if ( is_array($info) && sizeof( $info ) > 0) {
		foreach ($info as $subpage_slug => $subpage ){ ?>
		<div class="subpage">
			<h3 class="subpage-title"><?php echo $subpage['title']; ?></h3>
			<ul class="sections">
			<?php foreach ($subpage['sections'] as $section_slug => $section ){ ?>
				<li><span class="section-title"><?php echo $section['title']; ?></span>
					<ul class="fields">
					<?php foreach ($section['fields'] as $field_slug => $shortcode_info ){ ?>
						<?php if ( isset( $shortcode_info['groups'] ) ){ ?>
						<?php } else { ?>
						<li>
							<a href="#" class="option_shortcode" data-shortcode="<?php echo $shortcode_info['shortcode']; ?>" ><?php echo $shortcode_info['field_title']; ?></a>
						</li>
						<?php } ?>
					<?php } ?>
					</ul>
				</li>
			<?php } ?>
			</ul>
		</div>
		<p class="explanation">Click the links to insert a shortcode into your code.</p>		
		<?php } ?>
	<?php } else { ?>
		<div class="sorry">
			<p>Sorry! No options have been set as available.</p>
		</div>
	<?php } ?>
	</div>