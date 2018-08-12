<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( ! class_exists( 'My_Home_Facebook_Widget' ) ) :

	class My_Home_Facebook_Widget extends WP_Widget {

		/**
		 * construct
		 *
		 * Set widget name
		 *
		 * @date 23/06/16
		 * @since 1.0.0
		 *
		 * @param N/A
		 * @return N/A
		 *
		 */
		public function __construct() {
			$widget_opts = array(
				'classname'		=> 'widget-mh-facebook',
				'description'	=> esc_html__( 'Display facebook page', 'myhome-core' ),
			);
			parent::__construct( 'myhome-facebook-widget', esc_html__( 'MH Facebook', 'myhome-core' ), $widget_opts );
		}

		/**
		 * widget
		 *
		 * Outputs the content of the widget
		 *
		 * @date 23/06/16
		 * @since 1.0.0
		 *
		 * @param $args (array)
		 * @param $instance (array)
		 * @return N/A
		 *
		 */
		public function widget( $args, $instance ) {
			$widget_data = array_merge( array(
				'title'					=> '',
				'page_url'				=> '',
				'small_header'			=> 0,
				'hide_cover_photo' 		=> 0,
				'show_friends_faces'	=> 1,
				'show_timeline'			=> 1,
				'page_height'			=> 500
			), $instance );

			extract( $args );

            if ( ! empty( My_Home_Core()->lang ) ) {
                $widget_data['title'] = apply_filters(
                    'wpml_translate_single_string',
                    $widget_data['title'],
                    esc_html__( 'MyHome - Widgets', 'myhome-core' ),
                    esc_html__( 'Facebook widget', 'myhome-core' )
                );
            }

			$title = apply_filters( 'widget_title', $widget_data['title'] );

			$options = array();
			// options for facebook page plugin
			$options['page_url']			= $widget_data['page_url'];
			$options['page_height']			= intval( $widget_data['page_height'] );
			$options['small_header']		= $widget_data['small_header'] ? 'true' : 'false';
			$options['hide_cover_photo'] 	= $widget_data['hide_cover_photo'] ? 'true' : 'false';
			$options['show_friends_faces']	= $widget_data['show_friends_faces'] ? 'true' : 'false';
			$options['show_timeline']		= $widget_data['show_timeline'] ? 'timeline' : '';

			echo $before_widget;

			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}
			?>
			<div class="mh-widget-facebook">
				<iframe height="<?php echo esc_attr( $options['page_height'] ); ?>"
				        data-src="https://www.facebook.com/plugins/page.php?href=<?php
					echo urldecode( $options['page_url'] );
				?>&tabs=<?php echo $options['show_timeline']; ?>&height=<?php echo esc_attr( $options['page_height'] );
				?>&small_header=<?php echo $options['small_header'];
				?>&adapt_container_width=true&hide_cover=<?php echo $options['hide_cover_photo'];
				?>&show_facepile=<?php echo $options['show_friends_faces']; ?>"></iframe>
			</div>
			<?php
			echo $after_widget;
		}

		/**
		 * form
		 *
		 * Outputs the options form on admin
		 *
		 * @date 23/06/16
		 * @since 1.0.0
		 *
		 * @param $instance (array)
		 * @return N/A
		 *
		 */
		public function form( $instance ) {
			// prepare options
			$instance = wp_parse_args( (array) $instance, array(
				'title'					=> '',
				'page_url'				=> '',
				'small_header'			=> 0,
				'hide_cover_photo' 		=> 0,
				'show_friends_faces' 	=> 1,
				'show_timeline'			=> 1,
				'page_height'			=> 500
			) );

			$title 				= $instance['title'];
			$page_url			= $instance['page_url'];
			$page_height		= $instance['page_height'];
			$small_header		= $instance['small_header'];
			$hide_cover_photo	= $instance['hide_cover_photo'];
			$show_friends_faces	= $instance['show_friends_faces'];
			$show_timeline		= $instance['show_timeline'];

			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'myhome-core' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'page_url' ) ); ?>"><?php esc_html_e( 'Page (url):', 'myhome-core' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'page_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_url' ) ); ?>" type="text" value="<?php echo esc_url( $page_url ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'page_height' ) ); ?>"><?php esc_html_e( 'Page height:', 'myhome-core' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'page_height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_height' ) ); ?>" type="text" value="<?php echo esc_attr( $page_height ); ?>" />
			</p>
			<p>
				<input class="checkbox" id="<?php echo $this->get_field_id( 'small_header' ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'small_header' ) ); ?>" type="checkbox" value="1" <?php checked( $small_header, 1 ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'small_header' ) ); ?>"><?php esc_html_e( 'Use small header', 'myhome-core' ); ?></label>
			</p>
			<p>
				<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'hide_cover_photo' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_cover_photo' ) ); ?>" type="checkbox" value="1" <?php checked( $hide_cover_photo, 1 ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'hide_cover_photo' ) ); ?>"><?php esc_html_e( 'Hide cover photo', 'myhome-core' ); ?></label>
			</p>
			<p>
				<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_friends_faces' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_friends_faces' ) ); ?>" type="checkbox" value="1" <?php checked( $show_friends_faces, 1 ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_friends_faces' ) ); ?>"><?php esc_html_e( 'Display faces of friend', 'myhome-core' ); ?></label>
			</p>
			<p>
				<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_timeline' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_timeline' ) ); ?>" type="checkbox" value="1" <?php checked( $show_timeline, 1 ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_timeline' ) ); ?>"><?php esc_html_e( 'Display timeline', 'myhome-core' ); ?></label>
			</p>
			<?php
		}

		/**
		 * update
		 *
		 * Processing widget options on save
		 *
		 * @date 23/06/16
		 * @since 1.0.0
		 *
		 * @param $new_instance (array)
		 * @param $old_instance (array)
		 * @return $instance (array)
		 *
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title']				= strip_tags( $new_instance['title'] );
			$instance['page_url']			= strip_tags( $new_instance['page_url'] );
			$instance['page_height'] 		= intval( $new_instance['page_height'] );
			$instance['small_header']		= intval( $new_instance['small_header'] );
			$instance['hide_cover_photo']	= intval( $new_instance['hide_cover_photo'] );
			$instance['show_friends_faces']	= intval( $new_instance['show_friends_faces'] );
			$instance['show_timeline']		= intval( $new_instance['show_timeline'] );

			if ( ! empty( My_Home_Core()->lang ) ) {
                do_action(
                    'wpml_register_single_string',
                    esc_html__( 'MyHome - Widgets', 'myhome-core' ),
                    esc_html__( 'Facebook widget', 'myhome-core' ),
                    $instance['title']
                );
            }

			return $instance;
		}

	}

endif; // class exists check
