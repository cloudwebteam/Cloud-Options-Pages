<?php
	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once( $parse_uri[0] . 'wp-load.php' );

	$info = Cloud_Options_Pages::get_mce_options_list_info();
	?>
	<link href="css/options_list.css" rel="stylesheet">
	<script>
	</script>

	<?php
	foreach ($info as $subpage_slug => $subpage ){ ?>
		<h3 class="subpage-title"><?php echo $subpage['title']; ?></h3>
		<ul class="sections">
		<?php foreach ($subpage['sections'] as $section_slug => $section ){ ?>
			<li><span class="section-title"><?php echo $section['title']; ?></span>
				<ul class="fields">
			<?php foreach ($section['fields'] as $field_slug => $shortcode_info ){ ?>
					<li><a href="#" class="option_shortcode" data-shortcode="<?php echo $shortcode_info['shortcode']; ?>" ><?php echo $shortcode_info['field_title']; ?></a></li>
			<?php } ?>
				</ul>
			</li>
		<?php } ?>
		</ul>
	<?php } ?>