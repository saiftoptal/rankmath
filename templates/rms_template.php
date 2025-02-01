<?php
/**
 * Plugin Template for Cool Kids Network Pages.
 * This template displays only the custom header menu and the page content.
 * It is used exclusively for pages created by the plugin.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title( '' ); ?></title>
	<?php wp_head(); ?>
	<style>
	</style>
</head>
<body <?php body_class(); ?>>
<?php
// Render our custom header menu.
echo do_shortcode( '[rms_header_menu]' );
?>
<div class="rms-page-content">
	<?php
	// Output the page content.
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;
	?>
</div>
<?php wp_footer(); ?>
</body>
</html>