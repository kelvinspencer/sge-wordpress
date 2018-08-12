<?php
/*
 * My_Home_Term class
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Term' ) ) :

class My_Home_Term {

    private $id;
    private $name;
    private $slug;
    private $description;
    private $count;
    private $link;
    private $image_id;
    private $image_wide;
    private $image_wide_id;
    private $taxonomy;

    public function __construct( $term ) {
        $this->id           = $term->term_id;
        $this->name         = $term->name;
        $this->slug         = $term->slug;
        $this->count        = $term->count;
        $this->link         = get_term_link( $term );
        $this->taxonomy     = $term->taxonomy;
        $this->description  = $term->description;

        if ( function_exists( 'get_field' ) ) {
            $image = get_field( 'myhome_term_image', $term );
            if ( ! empty( $image ) && $image ) {
                $this->image_id = $image['id'];
            }

            $image_wide = get_field( 'myhome_term_image_wide', $term );
            if ( ! empty( $image_wide ) && $image_wide ) {
                $this->image_wide    = $image_wide['url'];
                $this->image_wide_id = $image_wide['id'];
            }
        }
    }

    public function get_description() {
        return $this->description;
    }

    public function has_image() {
        return ! empty( $this->image_id );
    }

    public function has_image_wide() {
        return ! empty( $this->image_wide_id );
    }

    public function get_image_wide() {
        return $this->image_wide;
    }

    public static function get_term( $term_id = null ) {
        if ( is_null( $term_id ) ) {
            $term_id = get_queried_object()->term_id;
        }
        $term = get_term( $term_id );
        return new My_Home_Term( $term );
    }

    public static function get( $taxonomy, $limit = 0, $hide_empty = true, $objects = false ) {
        $cache_key = 'myhome_terms_' . $taxonomy . '_' . $limit;

        if ( $hide_empty ) {
            $cache_key .= '_hide-empty';
        }

        if ( $objects ) {
            $cache_key .= '_objects';
        }

        if ( ! empty( My_Home_Core()->lang ) ) {
            $cache_key .= '_' . My_Home_Core()->lang;
        }

        if ( false !== ( $terms = get_transient( $cache_key ) ) ) {
            return $terms;
        }

        $terms = get_terms( array(
            'taxonomy'      => $taxonomy,
            'hide_empty'    => $hide_empty,
            'number'        => $limit
        ) );

        if ( ! is_array( $terms ) ) {
            $terms = array();
        }

        if ( ! $objects ) {
            set_transient( $cache_key, $terms, 4 * HOUR_IN_SECONDS );
            return $terms;
        }

        $term_objects = array();
        foreach ( $terms as $term ) {
            array_push( $term_objects, new My_Home_Term( $term ) );
        }
        set_transient( $cache_key, $term_objects, 4 * HOUR_IN_SECONDS );

        return $term_objects;
    }

    public static function get_popular( $taxonomy, $limit ) {
        $cache_key = 'myhome_terms_popular_' . $taxonomy . '_' . $limit;
        if ( ! empty( My_Home_Core()->lang ) ) {
            $cache_key .= '_' . My_Home_Core()->lang;
        }

        if ( false !== ( $terms = get_transient( $cache_key ) ) ) {
            return $terms;
        }

        $terms = get_terms( array(
            'taxonomy'      => $taxonomy,
            'hide_empty'    => true,
            'number'        => $limit,
            'orderby'       => 'count',
            'order'         => 'DESC'
        ) );

        if ( ! is_array( $terms ) ) {
            $terms = array();
        }
        set_transient( $cache_key, $terms, 4 * HOUR_IN_SECONDS );

        return $terms;
    }

    public static function get_from_property_type( $taxonomy, $limit = 0 ) {
        $property_type_slug = My_Home_Core()->attributes->get_property_type_slug();
        $output = array();
        global $wpdb;

        // for specific property type
        $query = "
            SELECT COUNT(p.ID) as count, t.slug as property_type, t2.name, t2.slug
            FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
                INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
                INNER JOIN {$wpdb->term_relationships} tr2 ON tr2.object_id = p.ID
                INNER JOIN {$wpdb->term_taxonomy} tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
                INNER JOIN {$wpdb->terms} t2 ON t2.term_id = tt2.term_id
            WHERE tt.taxonomy = %s
                AND tt2.taxonomy = %s
            GROUP BY t.slug, t2.slug
            ORDER BY count DESC
        ";

        $results = $wpdb->get_results( $wpdb->prepare( $query, $property_type_slug, $taxonomy ) );

        foreach ( $results as $result ) {
            if ( ! isset( $output[$result->property_type] ) ) {
                $output[$result->property_type] = array();
            }

            if ( count( $output[$result->property_type] ) == $limit && $limit != 0 ) {
                continue;
            }

            $output[$result->property_type][$result->slug] = $result->name;
        }

        // for any property type
        $query = "
            SELECT COUNT(p.ID) as count, t2.name, t2.slug
            FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->term_relationships} tr2 ON tr2.object_id = p.ID
                INNER JOIN {$wpdb->term_taxonomy} tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
                INNER JOIN {$wpdb->terms} t2 ON t2.term_id = tt2.term_id
            WHERE tt2.taxonomy = %s
                AND p.post_status = 'publish'
                AND p.post_type = 'estate'
            GROUP BY t2.slug
            ORDER BY count DESC
        ";

        $results = $wpdb->get_results( $wpdb->prepare( $query, $taxonomy ) );
        $output['any'] = array();
        foreach ( $results as $result ) {
            if ( count( $output['any'] ) == $limit && $limit != 0 ) {
                break;
            }
            $output['any'][$result->slug] = $result->name;
        }

        return $output;
    }

    public static function get_all( $taxonomy ) {
        return My_Home_Term::get( $taxonomy, 0, false );
    }

    public static function get_from_estate( $estate_id, $taxonomy ) {
        return get_the_terms( $estate_id, $taxonomy );
    }

    public static function get_list( $taxonomy ) {
        $terms = My_Home_Term::get_all( $taxonomy );
        $list = array();

        foreach ( $terms as $term ) {
            $list[$term->get_ID()] = $term->get_name();
        }

        return $list;
    }

    public function get_data() {
        $data = get_object_vars( $this );
        return $data;
    }

    public function load_data( $data ) {
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }

    public function get_ID() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_slug() {
        return $this->slug;
    }

    public function get_count() {
        return $this->count;
    }

    public function get_link() {
        return $this->link;
    }

    public function get_image_id() {
        return $this->image_id;
    }

    public function get_image_wide_id() {
        return $this->image_wide_id;
    }

    public function listing() {
        $options = get_option( 'myhome_redux' );
        $show_advanced = is_null( $options['mh-listing-show_advanced'] ) ? true : intval( $options['mh-listing-show_advanced'] );
        $show_clear = is_null( $options['mh-listing-show_clear'] ) ? true : intval( $options['mh-listing-show_clear'] );
        $show_sort_by = is_null( $options['mh-listing-show_sort_by'] ) ? true : intval( $options['mh-listing-show_sort_by'] );
        $show_view_types = is_null( $options['mh-listing-show_view_types'] ) ? true : intval( $options['mh-listing-show_view_types'] );
        $advanced_number = is_null( $options['mh-listing-search_form_advanced_number'] ) ? 3 : intval( $options['mh-listing-search_form_advanced_number'] );

        $atts = array(
            'lazy_loading'                  => $options['mh-listing-lazy_loading'] ? 'true' : 'false',
            'lazy_loading_limit'            => intval( $options['mh-listing-load_more_button_number'] ),
            'load_more_button'              => $options['mh-listing-load_more_button_label'],
            'load_prev_button'              => $options['mh-listing-load_prev_button_label'],
            'listing_default_view'          => $options['mh-listing-default_view'],
            'estates_per_page'              => $options['mh-listing-estates_limit'],
            'search_form_position'          => $options['mh-listing-search_form_position'],
            'search_form_advanced_number'   => $advanced_number,
            'show_advanced'                 => $show_advanced ? 'true' : 'false',
            'show_clear'                    => $show_clear ? 'true' : 'false',
            'show_sort_by'                  => $show_sort_by ? 'true' : 'false',
            'show_view_types'               => $show_view_types ? 'true' : 'false',
            'label'                         => $options['mh-listing-label'],
            'map'                           => false,
            $this->taxonomy                 => $this->slug
        );

        if ( My_Home_Core()->attributes->get_property_type_slug() == $this->taxonomy ) {
            $atts['mh-property_type']   = $this->slug;
        }

        foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            if ( $attr->get_slug() == $this->taxonomy ) {
                $value = false;
            } else {
                $value = $options['mh-listing-' . $attr->get_slug() . '_show'];
            }
            $atts[$attr->get_slug() . '_show'] = $value ? 'true' : 'false';
        }

        $listing = new My_Home_Listing( $atts );

        if ( $atts['search_form_position'] == 'top' ) : ?>
            <div>
                <?php $listing->search_form(); ?>
                <?php $listing->listing(); ?>
            </div>
        <?php endif; ?>

        <?php if ( $atts['search_form_position'] == 'left' ) : ?>
            <div class="mh-layout__sidebar-left">
                <?php $listing->search_form(); ?>
                <?php dynamic_sidebar( 'mh-listing' ); ?>
            </div>
            <div class="mh-layout__content-right">
                <?php $listing->listing(); ?>
            </div>
        <?php endif; ?>

        <?php if ( $atts['search_form_position'] == 'left-boxed' ) : ?>
            <div class="mh-listing--vertical-boxed">
                <div class="mh-layout__sidebar-left">
                    <?php $listing->search_form(); ?>
                    <?php dynamic_sidebar( 'mh-listing' ); ?>
                </div>
                <div class="mh-layout__content-right">
                    <?php $listing->listing(); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $atts['search_form_position'] == 'right' ) : ?>
            <div class="mh-layout__content-left"><?php $listing->listing(); ?></div>
            <div class="mh-layout__sidebar-right"><?php $listing->search_form(); ?></div>
        <?php endif; ?>


        <?php if ( $atts['search_form_position'] == 'right-boxed' ) : ?>
            <div class="mh-listing--vertical-boxed">
                <div class="mh-layout__content-left">
                    <?php $listing->listing(); ?>
                </div>
                <div class="mh-layout__sidebar-right">
                    <?php $listing->search_form(); ?>
                </div>
            </div>
        <?php endif;
    }

}

endif;