<?php
/*
 * My_Home_Estate class
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Estate' ) ) :

	class My_Home_Estate {

		private $short_excerpt_limit = 125;
		private $long_excerpt_limit = 160;
		// fields
		private $id;
		private $agent_id;
		private $title;
		private $content;
		private $excerpt;
		private $short_excerpt;
		private $long_excerpt;
		private $link;
		private $offer_type;
		private $price;
		private $location;
		private $is_featured;
		private $gallery;
		private $attributes;
		private $views;
		private $days_ago;
		private $image_id;
		private $image_url;
		private $tags;
		private $status;
		private $plans;
		private $video_plan;
		private $virtual_tour;
		private $state;
		private $labels;

		public static function get_estate( $post_id = null ) {
			global $post;

			if ( ! is_null( $post_id ) ) {
				$post = get_post( $post_id );
			}

			if ( ! is_null( $post ) ) {
				return new My_Home_Estate( $post );
			} else {
				return null;
			}
		}

		public function __construct( $estate ) {
			global $post;
			$post = $estate;
			setup_postdata( $post );
			$this->id            = get_the_ID();
			$this->status        = get_post_status();
			$this->agent_id      = $post->post_author;
			$this->title         = get_the_title();
			$this->content       = get_the_content();
			$this->excerpt       = get_the_excerpt();
			$this->short_excerpt = $this->set_short_excerpt( $post->post_content );
			$this->long_excerpt  = $this->set_long_excerpt( $post->post_content );
			$this->link          = get_permalink();
			$this->state         = get_post_meta( $this->id, 'myhome_state', true );

			if ( function_exists( 'get_fields' ) ) {
				$this->attributes  = $this->set_attributes();
				$this->tags        = $this->set_tags();
				$this->offer_type  = $this->set_offer_type();
				$this->price       = $this->set_price();
				$this->location    = $this->set_location();
				$this->is_featured = $this->set_featured();
				$this->labels      = $this->set_labels();

				$options = get_option( 'myhome_redux' );
				if ( isset( $options['mh-estate_plans'] ) && $options['mh-estate_plans'] ) {
					$this->plans = $this->set_plans();
				}
				if ( isset( $options['mh-estate_video'] ) && $options['mh-estate_video'] ) {
					$this->video_plan = $this->set_video_plan();
				}
				if ( isset( $options['mh-estate_virtual_tour'] ) && $options['mh-estate_virtual_tour'] ) {
					$this->virtual_tour = $this->set_virtual_tour();
				}
				$this->views   = $this->set_views();
				$this->gallery = $this->set_gallery();
			}
			$this->days_ago = $this->set_days_ago( $post->post_date );

			if ( has_post_thumbnail() ) {
				$this->image_id  = get_post_thumbnail_id();
				$this->image_url = get_the_post_thumbnail_url();
			}
		}

		private function set_short_excerpt( $post_content ) {
			$content = wp_kses( strip_shortcodes( $post_content ), array() );
			if ( mb_strlen( $content, 'UTF-8' ) > $this->short_excerpt_limit ) {
				$content = mb_strimwidth( $content, 0, $this->short_excerpt_limit, '...', 'UTF-8' );
			}

			return $content;
		}

		private function set_long_excerpt( $post_content ) {
			$content = wp_kses( strip_shortcodes( $post_content ), array() );
			if ( mb_strlen( $content, 'UTF-8' ) > $this->long_excerpt_limit ) {
				$content = mb_strimwidth( $content, 0, $this->long_excerpt_limit, '...', 'UTF-8' );
			}

			return $content;
		}

		public function set_gallery() {
			$images  = array();
			$gallery = get_field( 'estate_gallery', $this->id );

			if ( ! is_array( $gallery ) ) {
				return $images;
			}

			foreach ( $gallery as $image ) {
				array_push( $images, array(
					'id'  => $image['ID'],
					'url' => $image['url'],
				) );
			}

			return $images;
		}

		public function set_labels() {
			$labels       = array();
			$labels_field = get_field( 'estate_labels', $this->id );
			if ( ! empty( $labels_field ) && is_array( $labels_field ) ) {
				foreach ( $labels_field as $lab ) {
					array_push( $labels, array(
						'name'     => $lab['estate_label_name'],
						'position' => $lab['estate_label_position'],
						'bg_color' => $lab['estate_label_bg_color'],
						'color'    => $lab['estate_label_color'],
					) );
				}
			}

			return $labels;
		}

		public function get_labels() {
			return $this->labels;
		}

		public function has_attributes() {
			return count( $this->attributes );
		}

		private function set_attributes() {
			$attributes = array();

			foreach ( My_Home_Attribute::get_attributes() as $attr ) {
				if ( $attr->get_type() == 'field' ) {
					$value = get_field( 'estate_attr_' . $attr->get_slug(), $this->id );
					if ( empty( $value ) ) {
						continue;
					}

					$attribute = array(
						'slug'          => $attr->get_slug(),
						'name'          => $attr->get_name(),
						'has_archive'   => false,
						'elements'      => array( (object) array( 'name' => $value, 'link' => '' ) ),
						'show'          => $attr->property_show(),
						'card'          => $attr->card_show(),
						'display_after' => $attr->get_display_after(),
						'new_box'       => false,
					);
				} elseif ( $attr->get_type() == 'taxonomy' ) {
					$elements = My_Home_Term::get_from_estate( $this->id, $attr->get_slug() );
					if ( ! count( $elements ) ) {
						continue;
					}

					if ( ! is_array( $elements ) ) {
						continue;
					}

					$els = array();
					foreach ( $elements as $e ) {
						array_push( $els, (object) array(
							'name' => $e->name,
							'link' => get_term_link( $e ),
						) );
					}

					$attribute = array(
						'slug'          => $attr->get_slug(),
						'name'          => $attr->get_name(),
						'has_archive'   => $attr->has_archive(),
						'elements'      => $els,
						'card'          => $attr->card_show(),
						'show'          => $attr->property_show(),
						'display_after' => '',
						'new_box'       => $attr->new_box(),
					);
				} else {
					continue;
				}

				if ( $attr->get_base_slug() == 'price' ) {
					$attribute['show'] = false;
				}

				array_push( $attributes, (object) $attribute );
			}

			return $attributes;
		}

		public function get_attributes() {
			return $this->attributes;
		}

		public function has_tags() {
			return count( $this->tags );
		}

		private function set_tags() {
			$tags = array();
			foreach ( My_Home_Attribute::get_attributes() as $attr ) {
				if ( ! $attr->new_box() ) {
					continue;
				}

				$elements = My_Home_Term::get_from_estate( $this->id, $attr->get_slug() );
				if ( ! is_array( $elements ) ) {
					continue;
				}

				$els = array();
				foreach ( $elements as $e ) {
					array_push( $els, (object) array(
						'name' => $e->name,
						'link' => get_term_link( $e ),
					) );
				}

				array_push( $tags, (object) array(
					'name'        => $attr->get_name(),
					'elements'    => $els,
					'has_archive' => $attr->has_archive(),
				) );
			}

			return $tags;
		}

		public function get_tags() {
			return $this->tags;
		}

		public function update_views() {
			$views = intval( get_post_meta( $this->id, 'estate_views', true ) );
			$views++;
			update_post_meta( $this->id, 'estate_views', $views );
		}

		private function set_offer_type() {
			$offer_type_slug = My_Home_Core()->attributes->get_offer_type_slug();
			$types           = My_Home_Term::get_from_estate( $this->id, $offer_type_slug );
			if ( isset( $types[0]->name ) ) {
				$offer_type = $types[0]->name;
			} else {
				$offer_type = '';
			}

			return $offer_type;
		}

		public function set_days_ago( $post_date ) {
			$now          = time();
			$publish_date = strtotime( $post_date );

			return sprintf( esc_html__( '%s ago', 'myhome-core' ), human_time_diff( $publish_date, $now ) );
		}

		public static function get_estates_list() {
			$cache_key = 'myhome_estates_list';
			if ( false !== ( $estates_list = get_transient( $cache_key ) ) ) {
				return $estates_list;
			}

			global $wpdb;
			$query        = "
            SELECT  ID, post_title
            FROM    {$wpdb->posts}
            WHERE   post_type = 'estate'
                AND post_status = 'publish'
            ORDER BY ID DESC
        ";
			$estates      = $wpdb->get_results( $query );
			$estates_list = array();

			foreach ( $estates as $estate ) {
				$estates_list[ $estate->ID ] = $estate->post_title;
			}
			set_transient( $cache_key, $estates_list, 4 * HOUR_IN_SECONDS );

			return $estates_list;
		}

		public function load_json_data( $data ) {
			foreach ( $data as $key => $value ) {
				$this->$key = $value;
			}
		}

		public function get_json_data() {
			$data = get_object_vars( $this );

			foreach ( $data as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $k => $v ) {
						if ( is_object( $v ) && method_exists( $v, 'get_data' ) ) {
							$data[ $key ][ $k ] = $v->get_data();
						}
					}
				}

				if ( is_object( $value ) && method_exists( $value, 'get_data' ) ) {
					$data[ $key ] = $value->get_data();
				}
			}

			if ( $data['image_id'] ) {
				$data['image']        = esc_url( wp_get_attachment_image_url( $data['image_id'], 'myhome-standard-s' ) );
				$data['image_srcset'] = wp_get_attachment_image_srcset( $data['image_id'], 'myhome-standard-xs' );
			} else {
				$data['image']        = '';
				$data['image_srcset'] = '';
			}

			$data['content'] = wp_strip_all_tags( $data['content'] );

			return $data;
		}

		public function has_map() {
			$options = get_option( 'myhome_redux' );

			return ! ( empty( $this->location ) || empty( $options['mh-google-api-key'] ) );
		}

		public function map() {
			$options   = get_option( 'myhome_redux' );
			$map_style = empty( $options['mh-map-style'] ) ? 'gray' : $options['mh-map-style'];
			$config    = array(
				'estatesNear'       => $this->get_estates_near(),
				'site'              => site_url(),
				'estate'            => $this->get_json_data(),
				'mapStyle'          => $map_style,
				'estatesNearActive' => ! empty( $options['mh-estate-show_near_active'] ),
			);
			?>
			<div id="myhome-estate-map">
				<estate-map :config='<?php echo esc_attr( json_encode( $config ) ); ?>'
				            :translations='<?php echo esc_attr( json_encode( My_Home_Translations::get_estate_map() ) ); ?>'
				></estate-map>
			</div>
			<?php
		}

		private function set_plans() {
			$plans_field = get_field( 'estate_plans', $this->id );
			$plans       = array();

			if ( $plans_field ) {
				foreach ( $plans_field as $plan ) {
					array_push( $plans, array(
						'label' => $plan['estate_plans_name'],
						'image' => array(
							'id'  => $plan['estate_plans_image']['ID'],
							'url' => $plan['estate_plans_image']['url'],
						),
					) );
				}
			}

			return $plans;
		}

		public function has_plans() {
			return count( $this->plans ) ? true : false;
		}

		public function get_plans() {
			if ( ! function_exists( 'get_field' ) ) {
				return array();
			}

			$plans_field = get_field( 'estate_plans' );
			$plans       = array();

			foreach ( $plans_field as $plan ) {
				if ( empty( $plan['estate_plans_name'] ) && empty( $plan['estate_plans_image']['ID'] ) ) {
					continue;
				}

				if ( isset( $plan['estate_plans_image']['ID'] ) ) {
					$image_srcset = wp_get_attachment_image_srcset( $plan['estate_plans_image']['ID'], 'myhome-standard-xs' );
					$image        = $plan['estate_plans_image']['url'];
				} else {
					$image_srcset = '';
					$image        = '';
				}

				array_push( $plans, array(
					'name'         => $plan['estate_plans_name'],
					'image'        => $image,
					'image_srcset' => $image_srcset,
				) );
			}

			return $plans;
		}

		public function has_sidebar() {
			$options = get_option( 'myhome_redux' );

			return is_null( $options['mh-estate_sidebar'] ) || ! empty( $options['mh-estate_sidebar'] );
		}

		public function has_contact_form() {
			$options = get_option( 'myhome_redux' );

			return is_null( $options['mh-estate_sidebar_contact_form'] ) || ! empty( $options['mh-estate_sidebar_contact_form'] );
		}

		public function has_user_profile() {
			$options = get_option( 'myhome_redux' );

			return is_null( $options['mh-estate_sidebar_user_profile'] ) || ! empty( $options['mh-estate_sidebar_user_profile'] );
		}

		public function get_offer_type() {
			return $this->offer_type;
		}

		public function get_added() {
			return $this->days_ago;
		}

		public function get_views() {
			return $this->views;
		}

		public function get_ID() {
			return $this->id;
		}

		public function get_agent() {
			return My_Home_Agent::get_agent( $this->agent_id );
		}

		public function get_estates_near() {
			if ( empty( $this->location ) ) {
				return array();
			}

			$options       = get_option( 'myhome_redux' );
			$distance_unit = $options['mh-estate-distance_unit'];
			$range         = $options['mh-estate-near_estates_range'];

			if ( $distance_unit == 'miles' ) {
				$unit = 3959;
			} else {
				$unit = 6371;
			}

			global $wpdb;
			$args  = array(
				$unit,
				$this->location['lat'],
				$this->location['lng'],
				$this->location['lat'],
				$this->id,
			);
			$query = "
			SELECT key1.ID,
				( %d * acos( cos( radians(%f) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( lat ) ) ) ) AS distance
			FROM {$wpdb->posts} key1
				INNER JOIN {$wpdb->prefix}myhome_locations key2
				ON key1.ID = key2.post_id
			WHERE key1.post_status = 'publish'
				AND key1.post_type = 'estate'
				AND key1.ID != %d
			HAVING distance < $range
		";

			$results = $wpdb->get_col( $wpdb->prepare( $query, $args ) );
			if ( ! $results ) {
				return array();
			}

			$query = new My_Home_Query_Estates();
			$query->set_estates_in( $results );
			$query->set_map();
			$query->set_limit( -1 );
			if ( ! empty( My_Home_Core()->lang ) ) {
				$query->set_lang( My_Home_Core()->lang );
			}
			$results = $query->get_results();

			return $results['estates'];
		}

		public function get_image_id() {
			return $this->image_id;
		}

		public function has_image() {
			return ! is_null( $this->image_id );
		}

		public function image( $size = 'myhome-standards' ) {
			if ( is_null( $this->image_id ) ) {
				return;
			}
			$image_srcset = wp_get_attachment_image_srcset( $this->image_id, $size );

			ob_start();
			?>
			<a href="<?php the_permalink(); ?>">
				<img data-srcset="<?php echo esc_attr( $image_srcset ); ?>"
				     data-sizes="auto"
				     alt="<?php the_title_attribute(); ?>"
				     class="lazyload">
			</a>
			<?php
			echo ob_get_clean();
		}

		public function has_address() {
			return isset( $this->location['address'] );
		}

		public function get_address() {
			return $this->location['address'];
		}

		public function get_slider_name() {
			$options = get_option( 'myhome_redux' );

			return $options['mh-estate_slider'];
		}

		public function get_link() {
			return $this->link;
		}

		public function get_name() {
			return $this->title;
		}

		public function has_price() {
			return ! empty( $this->price );
		}

		public function get_price() {
			return $this->price;
		}

		public function get_excerpt() {
			return wp_trim_words( $this->excerpt, 20, esc_html__( '...', 'myhome' ) );
		}

		public function is_featured() {
			return $this->is_featured;
		}

		private function set_views() {
			return intval( get_post_meta( $this->id, 'estate_views', true ) );
		}

		private function set_video_plan() {
			$src = get_post_meta( $this->id, 'estate_video', true );

			return array(
				'src'   => $src,
				'video' => '',
			);
		}

		public function has_video_plan() {
			$video_plan = get_field( 'estate_video', $this->id );

			return ! empty( $video_plan );
		}

		public function video_plan() {
			$video_plan = get_field( 'estate_video', $this->id );

			if ( strpos( $video_plan, '<iframe' ) !== false || strpos( $video_plan, '<object' ) !== false || strpos( $video_plan, '<embed' ) !== false ) {
				echo $video_plan;
			} elseif ( strpos( $video_plan, '[video' ) !== false ) {
				echo do_shortcode( $video_plan );
			}
		}

		public function has_virtual_tour() {
			$virtual_tour = get_field( 'myhome_estate_virtual_tour', $this->id );

			return ! empty( $virtual_tour );
		}

		public function virtual_tour() {
			echo get_field( 'myhome_estate_virtual_tour', $this->id );
		}

		public function set_virtual_tour() {
			return get_post_meta( $this->id, 'virtual_tour', true );
		}

		private function set_featured() {
			$featured = get_field( 'estate_featured', $this->id );

			if ( empty( $featured ) ) {
				return false;
			}

			return $featured;
		}

		private function set_location() {
			$location = get_field( 'estate_location', $this->id );

			return empty( $location ) ? false : $location;
		}

		private function set_price() {
			$price = floatval( get_field( 'estate_attr_price', $this->id ) );
			if ( $price == 0 ) {
				return false;
			}

			return $this->format_price( $price, $this->offer_type );
		}

		public static function format_price( $price, $offer_type ) {
			$options           = get_option( 'myhome_redux' );
			$thousands_sep     = empty( $options['mh-estate-price_thousands_sep'] ) ? '.' : $options['mh-estate-price_thousands_sep'];
			$decimal_number    = empty( $options['mh-estate-price_decimal'] ) ? 0 : intval( $options['mh-estate-price_decimal'] );
			$decimal_separator = empty( $options['mh-estate-price_decimal_sep'] ) ? '.' : $options['mh-estate-price_decimal_sep'];
			$price             = number_format( $price, $decimal_number, $decimal_separator, $thousands_sep );

			if ( isset( $options['mh-estate-currency_sign'] ) && isset( $options['mh-estate-currency_location'] ) ) {
				if ( $options['mh-estate-currency_location'] == 'after_price' ) {
					$price = $price . ' ' . $options['mh-estate-currency_sign'];
				} else {
					$price = $options['mh-estate-currency_sign'] . ' ' . $price;
				}
			}

			if ( $offer_type == $options['mh-estate_rent'] ) {
				$rent_label = $options['mh-estate_rent_label'];
				$price      = $price . ' ' . $rent_label;
			}

			return $price;
		}

		public function has_gallery() {
			return count( $this->gallery ) ? true : false;
		}

		public function get_gallery() {
			return $this->gallery;
		}

	}

endif;