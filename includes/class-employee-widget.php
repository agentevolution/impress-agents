<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * This widget displays a featured employee.
 *
 * @since 0.9.0
 * @author Agent Evolution
 */
class IMPress_Agents_Widget extends WP_Widget {

	function IMPress_Agents_Widget() {
		$widget_ops = array( 'classname' => 'featured-employee', 'description' => __( 'Display a featured employee or employees contact info.', 'impress_agents' ) );
		$control_ops = array( 'width' => 300, 'height' => 350 );
		parent::__construct( 'featured-employee', __( 'IMPress Agents', 'impress_agents' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		global $post;

		/** defaults */
		$instance = wp_parse_args( $instance, array(
			'post_id'	=> '',
			'show_all'	=> 0
		) );

		extract( $args );

		$title = $instance['title'];

		echo $before_widget;

			if ( $instance['show_all'] == 1 ) {
				echo $before_title . apply_filters( 'widget_title', $title , $instance, $this->id_base ) . $after_title;
				$query_args = array(
					'post_type'			=> 'employee',
					'posts_per_page'	=> -1,
					'orderby'	=> 'menu_order',
					'order'		=> 'ASC'
				);
			} elseif ( !empty( $instance['post_id'] ) ) {
				$post_id = explode( ',', $instance['post_id']);
				echo $before_title . apply_filters( 'widget_title', $title , $instance, $this->id_base ) . $after_title;
				$query_args = array(
					'post_type'			=> 'employee',
					'p'					=> $post_id[0],
					'posts_per_page'	=> 1
				);
			}

			query_posts( $query_args );

			if ( have_posts() ) : while ( have_posts() ) : the_post();

				if ( $instance['show_all'] == 1 )
				echo '<div ', post_class('widget-agent-wrap'), '>';
				echo '<a href="', get_permalink(), '">', get_the_post_thumbnail( $post->ID, 'employee-thumbnail' ), '</a>';
				printf('<div class="widget-agent-details"><a class="fn" href="%s">%s</a>', get_permalink(), get_the_title() );
				echo impa_employee_details();
				// if (function_exists('_p2p_init') && function_exists('agentpress_listings_init') || function_exists('_p2p_init') && function_exists('wp_listings_init')) {
				// 	echo '<a class="agent-listings-link" href="' . get_permalink() . '#agent-listings">View My Listings</a>';
				// }

				echo '</div>';
				echo impa_employee_social();

				if ( $instance['show_all'] == 1 )
					echo '</div><!-- .widget-agent-wrap -->';

			endwhile; endif;
			wp_reset_query();

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $new_instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( $instance, array(
			'post_id'   =>	'',
			'title'		=>	'Featured Employees',
			'show_all'	=>	0
		) );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php esc_attr_e( $instance['title'] ); ?>" />
		</p>

		<?php
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'post_id' ) . '">Select an Employee or check the box to show all:</label>';
		echo '<select id="' . $this->get_field_id( 'post_id' ) . '" name="' . $this->get_field_name( 'post_id' ) . '" class="widefat" style="width:100%;">';
			global $post;
			$args = array('post_type' => 'employee', 'posts_per_page'	=> -1);
			$agents = get_posts($args);
			foreach( $agents as $post ) : setup_postdata($post);
				echo '<option style="margin-left: 8px; padding-right:10px;" value="' . $post->ID . ',' . $post->post_title . '" ' . selected( $post->ID . ',' . $post->post_title, $instance['post_id'], false ) . '>' . $post->post_title . '</option>';
			endforeach;
		echo '</select>';
		echo '</p>';

		?>
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_all'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_all' ); ?>" name="<?php echo $this->get_field_name( 'show_all' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_all' ); ?>">Show all agents?</label>
		</p>
		<?php
	}

}
