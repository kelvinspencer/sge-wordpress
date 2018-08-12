<?php
/*
 * My_Home_Query_Estates class
 *
 * This class is responsible for quering properties. Used by My_Home_API and other shortcodes which are related
 * to properties.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Query_Estates' ) ) :

class My_Home_Query_Estates {

    private $filters = array();
    private $order_by = array();
    private $page = 1;
    private $limit = 12;
    private $user;
    private $sort_by = 'newest';
    private $map = false;
    private $ids = array();
    private $offset;
    private $objects = false;
    private $all = false;
    private $keyword;
    private $featured;
    private $lang;
    private $estate_id;

    /**
     * @param My_Home_Attribute $attribute
     * @param $value
     * @param $compare
     */
    public function add_filter( $attribute, $value, $compare ) {
        if ( is_array( $value ) ) {
            foreach ( $value as $v ) {
                if ( $attribute->get_type() == 'field' ) {
                    $v = intval( $v );
                }

                array_push( $this->filters, (object) array(
                    'attribute' => $attribute,
                    'value'     => $v,
                    'compare'   => $compare
                ) );
            }
        } else {
            if ( $attribute->get_type() == 'field' ) {
                $value = intval( $value );
            } elseif ( $attribute->get_base_slug() == 'keyword' ) {
            	$this->keyword = $value;
            	return;
            } elseif ( $attribute->get_base_slug() == 'estate_id' ) {
            	$this->estate_id = intval( $value );
            	return;
            }

            array_push( $this->filters, (object) array(
                'attribute' => $attribute,
                'value'     => $value,
                'compare'   => $compare
            ) );
        }
    }

    /*
     * set_all
     *
     * Get all properties without any limit/offset
     */
    public function set_all() {
        $this->all = true;
    }

    /*
     * set_sort_by
     *
     * Define how to sort results
     */
    public function set_sort_by( $sort_by ) {
        $this->sort_by = sanitize_text_field( $sort_by );
    }

    /*
     * set_user
     *
     * Filter results by agent_id (user_id)
     */
    public function set_user( $agent_id ) {
        $this->user = $agent_id;
    }

    /*
     * set_estates_in
     *
     * Filter results by property ids
     */
    public function set_estates_in( $ids ) {
        $this->ids = $ids;
    }

    /*
     * set_page
     *
     * Define page (when paginate results)
     */
    public function set_page( $page ) {
        $this->page = intval( $page );
    }

    /*
     * set_offset
     *
     * Define offset
     */
    public function set_offset( $offset ) {
        $this->offset = intval( $offset );
    }

    /*
     * set_limit
     *
     * Define limit
     */
    public function set_limit( $limit ) {
        $this->limit = intval( $limit );
    }

    /*
     * set_featured
     *
     * Select only featured properties
     */
    public function set_featured( $featured ) {
        $this->featured = (boolean) $featured;
    }

    /*
     * set_map
     *
     * Define if results are used by map.
     * Map doesnt require all existing data, so this way we can improve performance and get only necessary.
     */
    public function set_map( $map = true ) {
        $this->map = $map;
    }

    /*
     * set_objects
     *
     * Get results as My_Home_Estate objects or just WP_Post
     */
    public function set_objects( $objects = true ) {
        $this->objects = $objects;
    }

    public function set_lang( $lang = '' ) {
        $this->lang = $lang;
    }

    /*
     * add_order_by
     */
    public function add_order_by( $order_by ) {
        array_push( $this->order_by, $order_by );
    }

    /*
     * get_results
     *
     * Query properties
     */
    public function get_results() {
        $taxonomies = array( 'relation' => 'AND' );
        $fields     = array( 'relation' => 'AND' );

        foreach ( $this->filters as $filter ) {
            if ( $filter->attribute->get_type() == 'taxonomy' ) {
                array_push( $taxonomies, array(
                    'taxonomy'  => $filter->attribute->get_slug(),
                    'field'     => 'slug',
                    'terms'     => is_array( $filter->value ) ? $filter->value : array( $filter->value )
                ) );
            } elseif( $filter->attribute->get_type() == 'field' ) {
                array_push( $fields, array(
                    'key'       => 'estate_attr_' . $filter->attribute->get_slug(),
                    'value'     => $filter->value,
                    'type'      => 'numeric',
                    'compare'   => $filter->compare,
                ) );
            }
        }

        if ( $this->map ) {
            array_push( $fields, array(
                'key'       => 'estate_location',
                'value'     => '',
                'compare'   => '!=',
            ) );
        }

        if ( $this->featured ) {
            array_push( $fields, array(
                'key'       => 'estate_featured',
                'value'     => '1',
                'compare'   => '=='
            ) );
        }

        if ( ! is_null( $this->offset ) ) {
            $offset = $this->offset;
        } else {
            $offset = $this->limit * $this->page - $this->limit;
        }

        $args = array(
            'post_type'                 => 'estate',
            'posts_per_page'            => $this->limit,
            'offset'                    => $offset,
            'ignore_sticky_posts'       => true,
            'post_status'               => 'publish',
            'update_post_meta_cache'    => true,
            'update_post_term_cache'    => true,
        );

	    if ( ! empty( $this->estate_id ) ) {
			$args['p'] = $this->estate_id;
	    }

        if ( $this->all ) {
            $args['post_status'] = 'any';
        }

        if ( count( $taxonomies ) > 1 ) {
            $args['tax_query'] = array ( $taxonomies );
        }

        if ( count( $fields ) > 1 ) {
            $args['meta_query'] = array( $fields );
        }

        if ( is_array( $this->ids ) && count( $this->ids ) > 0 ) {
            $args['post__in'] = $this->ids;
        }

        if ( ! is_null( $this->keyword ) && ! empty( $this->keyword ) ) {
            $args['s'] = $this->keyword;
        }

        if ( $this->sort_by == 'priceHighToLow' ) {
            $args['orderby']    = 'meta_value_num';
            $args['meta_key']   = 'estate_attr_price';
            $args['order']      = 'DESC';
        } elseif ( $this->sort_by == 'priceLowToHigh' ) {
            $args['orderby']    = 'meta_value_num';
            $args['meta_key']   = 'estate_attr_price';
            $args['order']      = 'ASC';
        } elseif ( $this->sort_by == 'popular' ) {
            $args['orderby']    = 'meta_value_num';
            $args['meta_key']   = 'estate_views';
            $args['order']      = 'DESC';

            array_push( $fields, array(
                'key'       => 'myhome_estate_views',
                'value'     => '',
                'compare'   => 'NOT EXISTS'
            ) );
        } else {
            $args['orderby']    = 'date';
            $args['order']      = 'DESC';
        }

        if ( ! is_null( $this->user ) ) {
            $args['author'] = intval( $this->user );
        }

        $cache_key = 'myhome_api_' . md5( http_build_query( $args ) );
        if ( $this->objects ) {
            $cache_key .= '_o';
        }
        
        if ( ! empty( $this->lang ) ) {
            $cache_key .= '_' . $this->lang;
            global $sitepress;
            $sitepress->switch_lang( $this->lang );
        }

        if ( false !== ( $results = get_transient( $cache_key ) ) ) {
            return $results;
        }

        $estates    = array();
        $the_query  = new WP_Query( $args );

        if ( $the_query->have_posts() ) {
            foreach ( $the_query->posts as $estate ) {
                if ( $this->map ) {
                    $price = intval( get_field( 'estate_attr_price', $estate->ID ) );
                    $offer_type_slug = My_Home_Core()->attributes->get_offer_type_slug();
                    $types = My_Home_Term::get_from_estate( $estate->ID, $offer_type_slug );
                    if ( isset( $types[0]->name ) ) {
                        $offer_type = $types[0]->name;
                    } else {
                        $offer_type = '';
                    }

                    $price = My_Home_Estate::format_price( $price, $offer_type );

                    array_push( $estates, array(
                        'title'     => $estate->post_title,
                        'location'  => get_field( 'estate_location', $estate->ID ),
                        'price'     => $price,
                        'link'      => get_the_permalink( $estate->ID ),
                        'image'     => get_the_post_thumbnail_url( $estate, 'myhome-standard-s' )
                    ) );
                } else {
                    $e = new My_Home_Estate( $estate );
                    if ( $this->objects ) {
                        array_push( $estates, $e );
                    }
                    else {
                        array_push( $estates, $e->get_json_data() );
                    }
                }
            }
        }

        $count = intval( $the_query->found_posts );
        $results = array(
            'estates'   => $estates,
            'count'     => $count
        );

        set_transient( $cache_key, $results, 4 * HOUR_IN_SECONDS );
        return $results;
    }

}

endif;