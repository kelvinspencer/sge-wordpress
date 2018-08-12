<?php
/*
 * My_Home_Attribute class
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Attribute' ) ) :

	class My_Home_Attribute {

		private $id;
		private $name;
		private $slug;
		private $display_after = '';
		private $placeholder = '';
		private $placeholder_from = '';
		private $placeholder_to = '';
		private $tags = false;
		private $form_control;
		private $default_values;
		private $dependencies;
		private $most_popular_limit = 6;
		private $values = array();
		private $type = 'text';
		private $base_slug;
		private $checkbox_full_width = false;
		private $show_card = false;
		private $show_property = true;
		private $has_archive = false;
		private $new_box = false;

		public function __construct( $attribute, $values = false ) {
			$this->id        = $attribute->ID;
			$this->name      = $attribute->attribute_name;
			$this->slug      = $attribute->attribute_slug;
			$this->base_slug = $attribute->base_slug;
			$this->type      = $attribute->attribute_type;

			if ( ! empty( My_Home_Core()->lang ) ) {
				$wpml_context = esc_html__( 'MyHome - Search form fields', 'myhome-core' );
				$wpml_name    = sprintf( esc_html__( 'Field name - %s', 'myhome-core' ), $this->name );

				do_action( 'wpml_register_single_string', $wpml_context, $wpml_name, $attribute->attribute_name );
				$this->name = apply_filters( 'wpml_translate_single_string', $attribute->attribute_name, $wpml_context, $wpml_name );
			}

			if ( ! function_exists( 'get_field' ) ) {
				return;
			}

			$options       = My_Home_Core()->attributes->get_options();
			$redux_options = get_option( 'myhome_redux' );

			if ( $this->base_slug == 'keyword' || $this->base_slug == 'estate_id' ) {
				if ( isset( $options[ 'options_' . $attribute->attribute_slug . '_placeholder' ] ) ) {
					$this->placeholder = $options[ 'options_' . $attribute->attribute_slug . '_placeholder' ];
				}
				$this->tags         = false;
				$this->form_control = 'text';

				return;
			}

			if ( isset( $options[ 'options_' . $attribute->attribute_slug . '_tags' ] ) ) {
				$this->tags = ! empty( $options[ 'options_' . $attribute->attribute_slug . '_tags' ] );
			}

			$atts = array(
				'display_after'       => '',
				'placeholder_from'    => '',
				'placeholder_to'      => '',
				'placeholder'         => '',
				'default_values'      => '',
				'checkbox_full_width' => '',
				'has_archive'         => $this->type == 'taxonomy',
				'show_card'           => false,
				'show_property'       => true,
				'new_box'             => ! empty( $this->tags )
			);

			if ( ! empty( My_Home_Core()->lang ) ) {
				$wpml_atts = array(
					'display_after'    => sprintf( esc_html__( ' %s (display after)', 'myhome' ), $attribute->attribute_name ),
					'placeholder_from' => sprintf( esc_html__( ' %s (placeholder from)', 'myhome' ), $attribute->attribute_name ),
					'placeholder_to'   => sprintf( esc_html__( ' %s (placeholder to)', 'myhome' ), $attribute->attribute_name ),
					'placeholder'      => sprintf( esc_html__( ' %s (placeholder)', 'myhome' ), $attribute->attribute_name ),
				);

				foreach ( $wpml_atts as $key => $attr ) {
					$attr_slug = 'options_' . $attribute->attribute_slug . '_' . $key;
					if ( ! empty( $options[ $attr_slug ] ) ) {
						do_action( 'wpml_register_single_string', $wpml_context, $attr, $options[ $attr_slug ] );
						$options[ $attr_slug ] = apply_filters( 'wpml_translate_single_string', $options[ $attr_slug ], $wpml_context, $attr );
					}
				}
			}

			foreach ( $atts as $key => $value ) {
				$slug = 'options_' . $attribute->attribute_slug . '_' . $key;
				if ( isset( $options[ $slug ] ) ) {
					$this->$key = $options[ $slug ];
				} else {
					$this->$key = $value;
				}
			}

			if ( $this->base_slug == 'price' && ! empty( $redux_options['mh-estate-currency_sign'] ) ) {
				$this->display_after = $redux_options['mh-estate-currency_sign'];
			}

			if ( isset( $options[ 'options_' . $attribute->attribute_slug . '_search_form_control' ] ) ) {
				$this->form_control = $options[ 'options_' . $attribute->attribute_slug . '_search_form_control' ];
			} elseif ( $attribute->attribute_type == 'taxonomy' ) {
				$this->form_control = 'select';
			} elseif ( $attribute->attribute_type == 'field' ) {
				$this->form_control = 'text';
			}

			if ( empty( $this->default_values ) && $this->type == 'taxonomy' ) {
				$this->default_values = 'all';
			}

			$this->dependencies = array();
			$property_type_slug = My_Home_Core()->attributes->get_property_type_slug();
			$property_types     = My_Home_Term::get_all( $property_type_slug );

			if ( $attribute->base_slug != 'property_type' ) {
				foreach ( $property_types as $type ) {
					$check = get_field( 'property_type_' . $attribute->attribute_slug, $property_type_slug . '_' . $type->term_id );
					if ( is_null( $check ) || ! empty( $check ) ) {
						array_push( $this->dependencies, $type->slug );
					}
				}
			}

			if ( $this->default_values == 'most_popular' ) {
				$this->most_popular_limit = (int) get_field( $attribute->attribute_slug . '_most_popular_limit', 'option' );

				if ( $this->tags ) {
					$this->values = My_Home_Term::get_from_property_type( $attribute->attribute_slug, $this->most_popular_limit );
				} else {
					$terms = My_Home_Term::get_popular( $attribute->attribute_slug, $this->most_popular_limit );
					foreach ( $terms as $term ) {
						$this->values[ $term->slug ] = $term->name;
					}
				}
			} elseif ( $this->default_values == 'static' || ( $attribute->attribute_type == 'field' && $this->form_control == 'select' || $this->form_control == 'select_range' ) ) {
				$values = get_field( $attribute->attribute_slug . '_static_values', 'option' );
				if ( is_array( $values ) && count( $values ) ) {
					foreach ( $values as $row ) {
						if ( empty( $row['value'] ) && ! empty( $row['name'] ) ) {
							$this->values[ $row['name'] ] = $row['name'];
						} elseif ( ! empty( $row['value'] ) && empty( $row['name'] ) ) {
							$this->values[ $row['value'] ] = $row['value'];
						} else {
							$this->values[ $row['value'] ] = $row['name'];
						}
					}
				}
            } elseif ( $this->default_values == 'all' ) {
                if ( $attribute->attribute_type == 'taxonomy' ) {
                    if ( $this->tags ) {
                        $this->values = My_Home_Term::get_from_property_type( $attribute->attribute_slug, 0 );
                    } else {
                        $terms = My_Home_Term::get_all( $attribute->attribute_slug );
                        foreach ( $terms as $term ) {
                            $this->values[ $term->slug ] = $term->name;
                        }
                    }
                }
            }

		}

		public function get_ID() {
			return $this->id;
		}

		public static function get_attribute( $attribute_slug ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'myhome_attributes';
			$attribute  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE attribute_slug = %s", $attribute_slug ) );

			return new My_Home_Attribute( $attribute );
		}

		public static function get_attributes() {
			$cache_key = 'myhome_attributes';
			if ( ! empty( My_Home_Core()->lang ) ) {
				$cache_key .= '_' . My_Home_Core()->lang;
			}
			if ( false !== ( $attributes = get_transient( $cache_key ) ) ) {
				return $attributes;
			}

			global $wpdb;
			$attributes = array();
			$options    = get_option( 'myhome_redux' );
			$table_name = $wpdb->prefix . 'myhome_attributes';
			$results    = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY form_order, ID" );

			foreach ( $results as $attribute ) {
				if ( $attribute->base_slug == 'offer_type' && isset( $options['mh-offer_type'] ) && ! $options['mh-offer_type'] ) {
					continue;
				}
				array_push( $attributes, new My_Home_Attribute( $attribute ) );
			}
			set_transient( $cache_key, $attributes );

			return $attributes;
		}

		public static function get_list() {
			$attributes = My_Home_Attribute::get_attributes();
			$list       = array();
			foreach ( $attributes as $attribute ) {
				$list[ $attribute->get_slug() ] = $attribute->get_name();
			}

			return $list;
		}

		public function get_vc_type() {
			if ( $this->type == 'field' ) {
				return 'textfield';
			} elseif ( $this->type == 'taxonomy' ) {
				if ( $this->form_control == 'select' || $this->form_control == 'select_range' || $this->form_control == 'radio_button' ) {
					return 'dropdown';
				} elseif ( $this->form_control == 'checkbox' ) {
					return 'dropdown';
				}
			} elseif ( $this->base_slug == 'keyword' || $this->base_slug == 'estate_id' ) {
				return 'textfield';
			}
		}

		public function get_vc_values() {
			$vc_type = $this->get_vc_type();
			if ( $vc_type == 'textfield' ) {
				return '';
			} elseif ( $vc_type == 'dropdown' ) {
				$values = array( esc_html__( 'Any' ) => 'any' );
				foreach ( $this->values as $key => $name ) {
					$values[ $name ] = $key;
				}

				return $values;
			} else {
				$values = array();
				foreach ( $this->values as $key => $name ) {
					$values[ $name ] = $key;
				}

				return $values;
			}
		}

		public function get_data() {
			$data = get_object_vars( $this );

			foreach ( $data as &$value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as &$v ) {
						if ( is_object( $v ) && method_exists( $v, 'get_data' ) ) {
							$v = $v->get_data();
						}
					}
				}

				if ( is_object( $value ) && method_exists( $value, 'get_data' ) ) {
					$value = $value->get_data();
				}
			}

			return $data;
		}

		public function load_data( $data ) {
			foreach ( $data as $key => $value ) {
				$this->$key = $value;
			}
		}

		public function get_name() {
			return $this->name;
		}

		public function get_slug() {
			return $this->slug;
		}

		public function like_tags() {
			if ( empty( $this->tags ) ) {
				return false;
			}

			return $this->tags;
		}

		public function get_form_control() {
			return $this->form_control;
		}

		public function get_type() {
			return $this->type;
		}

		public function get_base_slug() {
			return $this->base_slug;
		}

		public function get_display_after() {
			return $this->display_after;
		}

		public static function get_by_id( $id ) {
			foreach ( My_Home_Attribute::get_attributes() as $attr ) {
				if ( $attr->get_ID() == $id ) {
					return $attr;
				}
			}

			return false;
		}

		public function card_show() {
			return ! empty( $this->show_card );
		}

		public function has_archive() {
			return ! empty( $this->has_archive );
		}

		public function property_show() {
			return ! empty( $this->show_property );
		}

		public function new_box() {
			return $this->like_tags() && ! empty( $this->new_box );
		}
	}

endif;