<?php
/**
 * Adds shortcode to display employees
 * Adds shortcode to display post meta
 */

add_shortcode( 'employees', 'impress_agents_shortcode' );

function impress_agents_shortcode($atts, $content = null) {
	extract(shortcode_atts(array(
		'id'       => '',
		'taxonomy' => '',
		'term'     => '',
		'limit'    => '',
		'columns'  => ''
	), $atts ) );

	/**
	 * if limit is empty set to all
	 */
	if(!$limit) {
		$limit = -1;
	}

	/**
	 * if columns is empty set to 0
	 */
	if(!$columns) {
		$columns = 0;
	}

	/*
	 * query args based on parameters
	 */
	$query_args = array(
		'post_type'       => 'employee',
		'posts_per_page'  => $limit
	);

	if($id) {
		$query_args = array(
			'post_type'       => 'employee',
			'post__in'        => explode(',', $id)
		);
	}

	if($term && $taxonomy) {
		$query_args = array(
			'post_type'       => 'employee',
			'posts_per_page'  => $limit,
			'tax_query'       => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'     => $term
				)
			)
		);
	}

	/*
	 * start loop
	 */
	global $post;

	$employees_array = get_posts( $query_args );

	$count = 0;

	$output = '<div class="impress-agents-shortcode">';

	foreach ( $employees_array as $post ) : setup_postdata( $post );

		$count = ( $count == $columns ) ? 1 : $count + 1;

		$first_class = ( 1 == $count ) ? 'first' : '';

		$output .= '<div class="employee-wrap ' . get_column_class($columns) . ' ' . $first_class . '"><div class="employee-widget-thumb"><a href="' . get_permalink() . '" class="employee-image-link">' . get_the_post_thumbnail( $post->ID, 'employees' ) . '</a>';

		if ( '' != impress_agents_get_status() ) {
			$output .= '<span class="employee-status ' . strtolower(str_replace(' ', '-', impress_agents_get_status())) . '">' . impress_agents_get_status() . '</span>';
		}

		$output .= '<div class="employee-thumb-meta">';

		if ( '' != get_post_meta( $post->ID, '_employee_text', true ) ) {
			$output .= '<span class="employee-text">' . get_post_meta( $post->ID, '_employee_text', true ) . '</span>';
		} elseif ( '' != impress_agents_get_employee_types() ) {
			$output .= '<span class="employee-employee-type">' . impress_agents_get_employee_types() . '</span>';
		}

		if ( '' != get_post_meta( $post->ID, '_employee_price', true ) ) {
			$output .= '<span class="employee-price">' . get_post_meta( $post->ID, '_employee_price', true ) . '</span>';
		}

		$output .= '</div><!-- .employee-thumb-meta --></div><!-- .employee-widget-thumb -->';

		if ( '' != get_post_meta( $post->ID, '_employee_open_house', true ) ) {
			$output .= '<span class="employee-open-house">' . __( "Open House", 'impress_agents' ) . ': ' . get_post_meta( $post->ID, '_employee_open_house', true ) . '</span>';
		}

		$output .= '<div class="employee-widget-details"><h3 class="employee-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
		$output .= '<p class="employee-address"><span class="employee-address">' . impress_agents_get_address() . '</span><br />';
		$output .= '<span class="employee-city-state-zip">' . impress_agents_get_city() . ', ' . impress_agents_get_state() . ' ' . get_post_meta( $post->ID, '_employee_zip', true ) . '</span></p>';

		if ( '' != get_post_meta( $post->ID, '_employee_bedrooms', true ) || '' != get_post_meta( $post->ID, '_employee_bathrooms', true ) || '' != get_post_meta( $post->ID, '_employee_sqft', true )) {
			$output .= '<ul class="employee-beds-baths-sqft"><li class="beds">' . get_post_meta( $post->ID, '_employee_bedrooms', true ) . '<span>' . __( "Beds", 'impress_agents' ) . '</span></li> <li class="baths">' . get_post_meta( $post->ID, '_employee_bathrooms', true ) . '<span>' . __( "Baths", 'impress_agents' ) . '</span></li> <li class="sqft">' . get_post_meta( $post->ID, '_employee_sqft', true ) . '<span>' . __( "Square Feet", 'impress_agents' ) . '</span></li></ul>';
		}

		$output .= '</div><!-- .employee-widget-details --></div><!-- .employee-wrap -->';

	endforeach;

	$output .= '</div><!-- .impress-agents-shortcode -->';

	wp_reset_postdata();

	return $output;

}

add_shortcode('impress_agents_meta', 'impress_agents_meta_shortcode');
/**
 * Returns meta data for employees
 * @param  array $atts meta key
 * @return string meta value wrapped in span
 */
function impress_agents_meta_shortcode($atts) {
	extract(shortcode_atts(array(
		'key' => ''
	), $atts ) );
	$postid = get_the_id();

	return '<span class=' . $key . '>' . get_post_meta($postid, '_employee_' . $key, true) . '</span>';
}
