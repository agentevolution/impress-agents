<?php
/**
 * The template for displaying Employee Archive pages
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package IMPress Agents
 * @since 0.9.0
 */

add_action('wp_enqueue_scripts', 'enqueue_single_employee_scripts');
function enqueue_single_employee_scripts() {
	wp_enqueue_style( 'font-awesome' );
}

function archive_employee_loop() {
	echo '<div class="employee-archive">';

	$class = '';

	if ( have_posts() ) : while ( have_posts() ) : the_post();

	// starting at 0
	$class = ( $class == 'even agent-wrap' ) ? 'odd agent-wrap' : 'even agent-wrap';

	$thumb_id = get_post_thumbnail_id();
	$thumb_url = wp_get_attachment_image_src($thumb_id, 'employee-thumbnail', true);

	?>

	<div <?php post_class($class); ?> itemscope itemtype="http://schema.org/Person">
	<?php echo '<a href="' . get_permalink() . '"><img src="' . $thumb_url[0] . '" alt="' . get_the_title() . ' photo" class="attachment-employee-thumbnail wp-post-image" itemprop="image" /></a>'; ?>
		<div class="agent-details vcard">
		<?php

		printf('<p><a class="fn" href="%s" itemprop="name">%s</a></p>', get_permalink(), get_the_title() );

		echo impa_employee_archive_details();

		if (function_exists('_p2p_init') && function_exists('agentpress_listings_init') || function_exists('_p2p_init') && function_exists('wp_listings_init')) {
			$listings = impa_get_connected_posts_of_type('agents_to_listings');
			if ( !empty($listings) ) {
				echo '<p><a class="agent-listings-link" href="' . get_permalink() . '#agent-listings">View My Listings</a></p>';
			}
		}

		//echo impa_employee_social();

		?>
		</div><!-- .agent-details -->
	</div> <!-- .agent-wrap -->

	<?php endwhile;
		if (function_exists('equity')) {
			equity_posts_nav();
		} elseif (function_exists('genesis_init')) {
			genesis_posts_nav();
		} else {
			impress_agents_paging_nav();
		}

	else : ?>

	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif;

	echo '</div><!-- end .employee-archive -->';

}

if (function_exists('equity')) {

	add_filter( 'equity_pre_get_option_site_layout', '__equity_return_full_width_content' );
	remove_action( 'equity_entry_header', 'equity_post_info', 12 );
	remove_action( 'equity_entry_footer', 'equity_post_meta' );

	remove_action( 'equity_loop', 'equity_do_loop' );
	add_action( 'equity_loop', 'archive_employee_loop' );

	equity();

} elseif (function_exists('genesis_init')) {

	add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
	remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
	remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
	remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
	remove_action( 'genesis_after_entry', 'genesis_do_author_box_single' );

	remove_action( 'genesis_loop', 'genesis_do_loop' );
	add_action( 'genesis_loop', 'archive_employee_loop' );

	genesis();

} else {

get_header();
if($options['impress_agents_custom_wrapper'] && $options['impress_agents_start_wrapper']) {
	echo $options['impress_agents_start_wrapper'];
} else {
	echo '<div id="primary" class="content-area container inner">
		<div id="content" class="site-content" role="main">';
}
	if ( have_posts() ) : ?>

		<header class="archive-header">
			<?php
			$object = get_queried_object();

			if ( !isset($object->label) ) {
				$title = '<h1 class="archive-title">' . $object->name . '</h1>';
			} else {
				$title = '<h1 class="archive-title">' . get_bloginfo('name') . ' Employees</h1>';
			}

			echo $title; ?>

            <small><?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<p id="breadcrumbs">','</p>'); } ?></small>
		</header><!-- .archive-header -->

	<?php

	archive_employee_loop();

	else :
		// If no content, include the "No posts found" template.
		get_template_part( 'content', 'none' );

	endif;

if($options['impress_agents_custom_wrapper'] && $options['impress_agents_end_wrapper']) {
	echo $options['impress_agents_end_wrapper'];
} else {
	echo '</div><!-- #content -->
	</div><!-- #primary -->';
}
get_sidebar();
get_footer();

}
