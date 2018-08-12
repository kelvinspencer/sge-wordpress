<?php
/*
 * My_Home_Estates class
 *
 * This class provide custom fields, meta boxes, custom post type related to estates (properties).
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Estates' ) ) :

class My_Home_Estates {

    public function __construct() {
        add_action( 'init', array( $this, 'register_estate_post_type' ) );
        add_filter( 'acf/update_value/name=estate_location', array( $this, 'update_estate_location' ), 10, 3 );
        add_action( 'acf/init', array( $this, 'set_fields' ) );
        add_filter( 'manage_estate_posts_columns', array( $this, 'columns_head' ) );
        add_action( 'manage_estate_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
        add_action( 'edit_form_top', array( $this, 'order_meta_boxes' ), 0, 11 );
        add_action( 'wp_insert_post', array( $this, 'set_views' ), 10, 3 );
    }

    /*
     * set_views
     *
     * Set 0 views right after new property is created
     */
    public function set_views( $post_id, $post, $update ) {
        if ( $post->post_type == 'estate' ) {
            add_post_meta( $post_id, 'estate_views', 0, true );
        }
    }

    /*
     * order_meta_boxes
     *
     * Reorder meta boxes below estate custom post type for better UI experience.
     */
    public function order_meta_boxes() {
        global $post;
        if ( $post->post_type != 'estate' ) {
            return;
        }
        global $wp_meta_boxes;
        if ( isset( $wp_meta_boxes['estate']['normal']['high']['mm_general'] ) ) {
            $wp_meta_boxes['estate']['normal']['low']['mm_general'] = $wp_meta_boxes['estate']['normal']['high']['mm_general'];
            unset( $wp_meta_boxes['estate']['normal']['high']['mm_general'] );
        }
        if ( isset( $wp_meta_boxes['estate']['normal']['high']['essb_metabox_optmize'] ) ) {
	        $wp_meta_boxes['estate']['normal']['low']['essb_metabox_optmize'] = $wp_meta_boxes['estate']['normal']['high']['essb_metabox_optmize'];
	        unset( $wp_meta_boxes['estate']['normal']['high']['essb_metabox_optmize'] );
        }
    }

    /*
     * columns_head
     *
     * Modify table which display estates (add and modify columns).
     */
    public function columns_head( $columns ) {
        $new = array();
        foreach ( $columns as $key => $title ) {
            if ( $key == 'title' ){
                $new['featured_image'] = esc_html__( 'Image', 'myhome-core' );
                $new[$key] = $title;
            } elseif ( $key == 'author' ) {
                $new[$key] = esc_html__( 'Agent', 'myhome-core' );
                $new['price'] = esc_html__( 'Price', 'myhome-core' );
            } else {
                $new[$key] = $title;
            }
        }
        return $new;
    }

    /*
     * columns_content
     *
     * Modify table which display estates (add and modify columns).
     */
    public function columns_content( $column_name, $post_ID ) {
        if ( $column_name == 'featured_image' ) {
            $estate = My_Home_Estate::get_estate( $post_ID );
            if ( $estate->has_image() ) {
                ?><div><a href="<?php echo esc_url( get_edit_post_link( $post_ID ) ); ?>"><?php
                My_Home_Image::the_image( $estate->get_image_id(), 'standard' );
                    ?></a></div><?php
            }
        } elseif ( $column_name == 'price' ) {
            $estate = My_Home_Estate::get_estate( $post_ID );
            echo esc_html( $estate->get_price() );
        }
    }

    /*
     * register_estate_post_type
     *
     * Register estate post type
     */
    public function register_estate_post_type() {
        $options = get_option( 'myhome_redux' );
        // define post type slug
        if ( ! empty( $options['mh-estate-slug'] ) ) {
            $slug = $options['mh-estate-slug'];
        } else {
            $slug = 'properties';
        }

        register_post_type( 'estate', array(
            'labels' => array(
                'name'			     => esc_html__( 'Properties', 'myhome-core' ),
                'singular_name'	     => esc_html__( 'Property', 'myhome-core' ),
                'menu_name'          => esc_html__( 'Properties', 'myhome-core' ),
                'name_admin_bar'     => esc_html__( 'Add New Property', 'myhome-core' ),
                'add_new'            => esc_html__( 'Add New Property', 'myhome-core' ),
                'add_new_item'       => esc_html__( 'Add New Property', 'myhome-core' ),
                'new_item'           => esc_html__( 'New Property', 'myhome-core' ),
                'edit_item'          => esc_html__( 'Edit Property', 'myhome-core' ),
                'view_item'          => esc_html__( 'View Property', 'myhome-core' ),
                'all_items'          => esc_html__( 'Properties', 'myhome-core' ),
                'search_items'       => esc_html__( 'Search property', 'myhome-core' ),
                'not_found'          => esc_html__( 'No Property Found found.', 'myhome-core' ),
                'not_found_in_trash' => esc_html__( 'No Property found in Trash.', 'myhome-core' )
            ),
            'show_in_rest'      => true,
            'query_var'         => true,
            'public'		    => true,
            'has_archive'	    => true,
            'menu_position'     => 21,
            'menu_icon'         => 'dashicons-admin-home',
            'capability_type'   => array( 'estate', 'estates' ),
            'map_meta_cap'      => true,
            'rewrite'		    => array( 'slug' => $slug ),
            'supports'		    => array(
                'title',
                'author',
                'editor',
                'thumbnail',
            )
        ) );
    }

    /*
     * create_table
     *
     * Create additional tables.
     */
    public static function create_table() {
        global $wpdb;
        $charset_collate    = $wpdb->get_charset_collate();
        // myhome_locations store locations. It helps when filtering properties by locations (lat/lng)
        $table_name         = $wpdb->prefix . 'myhome_locations';

        $query = "CREATE TABLE $table_name (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			post_id bigint(20) UNSIGNED NOT NULL,
			lat decimal(10, 8) NOT NULL,
			lng decimal(11, 8) NOT NULL,
			PRIMARY KEY  (id)
			) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $query );
    }

    /*
     * update_estate_location
     *
     * When ACF location field is filled, myhome_locations needs to be updated.
     */
    public function update_estate_location( $value, $post_id, $field ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'myhome_locations';

        $wpdb->delete(
            $table_name,
            array(
                'post_id' => $post_id
            ),
            array(
                '%d'
            )
        );

        $wpdb->insert(
            $table_name,
            array(
                'post_id'   => $post_id,
                'lat'       => $value['lat'],
                'lng'       => $value['lng']
            ),
            array(
                '%s',
                '%f',
                '%f'
            )
        );

        return $value;
    }

    /*
     * set_fields
     *
     * Define custom fields related to estate post type.
     */
    public static function set_fields() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }
        $options = get_option( 'myhome_redux' );
        $fields = array(
            /*
             * General tab
             */
            array(
                'key'       => 'myhome_estate_tab_general',
                'label'     => esc_html__( 'General', 'myhome-core' ),
                'type'      => 'tab',
                'placement' => 'top',
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
            ),
            // Featured
            array(
                'key'           => 'myhome_estate_featured',
                'label'         => esc_html__( 'Featured', 'myhome-core' ),
                'name'          => 'estate_featured',
                'type'          => 'true_false',
                'default_value' => false,
                'wrapper'       => array(
                    'class' => 'acf-1of3'
                ),
            ),
        );

        foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            if ( $attr->get_type() != 'field' ) {
                continue;
            }

            $name = $attr->get_name();
            $display_after = $attr->get_display_after();
            if ( ! empty( $display_after ) ) {
                $name .= ' (' . $display_after . ')';
            }

            array_push( $fields, array(
                'key'           => 'myhome_estate_attr_' . $attr->get_slug(),
                'label'         => $name,
                'name'          => 'estate_attr_' . $attr->get_slug(),
                'type'          => 'text',
                'default_value' => '',
                'wrapper'       => array(
                    'class' => 'acf-1of3'
                ),
            ) );
        }


        $fields = array_merge( $fields, array(
            /*
             * Location tab
             */
            array(
                'key'   => 'myhome_estate_tab_location',
                'label' => esc_html__( 'Location', 'myhome-core' ),
                'type'  => 'tab',
            ),
            // Location
            array(
                'key'   => 'myhome_estate_location',
                'label' => esc_html__( 'Location', 'myhome-core' ),
                'name'  => 'estate_location',
                'type'  => 'google_map',
            ),
            /*
             * Gallery tab
             */
            array(
                'key'   => 'myhome_estate_tab_gallery',
                'label' => 'Gallery',
                'instructions' => 'Click "Add to gallery" below to upload files',
                'type'  => 'tab',
            ),
            // Gallery
            array(
                'key'          => 'myhome_estate_gallery',
                'label'        => 'Gallery',
                'name'         => 'estate_gallery',
                'type'         => 'gallery',
                'preview_size' => 'thumbnail',
                'library'      => 'all',
            )
        ) );

        if ( isset( $options['mh-estate_plans'] ) && $options['mh-estate_plans'] ) {
            $fields = array_merge( $fields, array(
                /*
                 * Plans tab
                 */
                array(
                    'key'       => 'myhome_estate_tab_plans',
                    'label'     => esc_html__( 'Plans', 'myhome-core' ),
                    'type'      => 'tab',
                    'placement' => 'left',
                ),
                // Plan
                array(
                    'key'          => 'myhome_estate_plans',
                    'label'        => esc_html__( 'Plans', 'myhome-core' ),
                    'name'         => 'estate_plans',
                    'type'         => 'repeater',
                    'button_label' => esc_html__( 'Add plan', 'myhome-core' ),
                    'sub_fields'   => array(
                        // Name
                        array(
                            'key'   => 'myhome_estate_plans_name',
                            'label' => esc_html__( 'Name', 'myhome-core' ),
                            'name'  => 'estate_plans_name',
                            'type'  => 'text',
                        ),
                        // Image
                        array(
                            'key'   => 'myhome_estate_plans_image',
                            'label' => esc_html__( 'Image', 'myhome-core' ),
                            'name'  => 'estate_plans_image',
                            'type'  => 'image',
                        ),
                    ),
                )
            ) );
        }

        if ( ! empty( $options['mh-estate_video'] ) ) {
            $fields = array_merge( $fields, array(
                /*
                 * Video tab
                 */
                array(
                    'key'       => 'myhome_estate_tab_video',
                    'label'     => esc_html__( 'Video', 'myhome-core' ),
                    'type'      => 'tab',
                    'placement' => 'left',
                ),
                // Video
                array(
                    'key'   => 'myhome_estate_video',
                    'label' => esc_html__( 'Video link (Youtube / Vimeo / Facebook / Twitter / Instagram / link to .mp4)', 'myhome-core' ),
                    'name'  => 'estate_video',
                    'type'  => 'oembed',
                )
            ) );
        }

        if ( ! empty( $options['mh-estate_virtual_tour'] ) ) {
            $fields = array_merge( $fields, array(
                /*
                 * Virtual tour tab
                 */
                array(
                    'key'       => 'myhome_estate_tab_virtual_tour',
                    'label'     => esc_html__( 'Virtual tour', 'myhome-core' ),
                    'type'      => 'tab',
                    'placement' => 'left',
                ),
                // Virtual tour
                array(
                    'key'   => 'myhome_estate_virtual_tour',
                    'label' => esc_html__( 'Add embed code', 'myhome-core' ),
                    'name'  => 'virtual_tour',
                    'type'  => 'text',
                )
            ) );
        }

        acf_add_local_field_group(
            array(
                'key'        => 'myhome_estate',
                'title'      => '<span class="dashicons dashicons-admin-home"></span> ' . esc_html__( 'Property details', 'myhome-core' ),
                'fields'     => $fields,
                'menu_order' => 10,
                'location'   => array(
                    array(
                        array(
                            'param'    => 'post_type',
                            'operator' => '==',
                            'value'    => 'estate',
                        ),
                    ),
                ),
            )
        );
    }
}

endif;