<?php
/*
 * My_Home_Translations class
 *
 * Since MyHome use a lot of JS (VueJS) we need some way to translate strings which appear inside js files.
 * Here are defined strings used by VueJS components. These strings are provided to VueJS components as props.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Translations' ) ) :

class My_Home_Translations {

    /*
     * get_search_form
     *
     * Listing search form strings
     */
	public static function get_search_form() {
		return array(
			'keyword'               => wp_kses( __( 'Keyword', 'myhome-core' ), array() ),
			'property_type'         => wp_kses( __( 'Property type', 'myhome-core' ), array() ),
			'price_from'            => wp_kses( __( 'Price from', 'myhome-core' ), array() ),
			'price_to'              => wp_kses( __( 'Price to', 'myhome-core' ), array() ),
			'baths_from'            => wp_kses( __( 'Bathrooms from', 'myhome-core' ), array() ),
			'baths_to'              => wp_kses( __( 'Bathrooms to', 'myhome-core' ), array() ),
			'beds_from'             => wp_kses( __( 'Bedrooms from', 'myhome-core' ), array() ),
			'beds_to'               => wp_kses( __( 'Bedrooms to', 'myhome-core' ), array() ),
			'offer_type'            => wp_kses( __( 'Offer type', 'myhome-core' ), array() ),
            'all_areas'             => wp_kses( __( 'All areas', 'myhome-core' ), array() ),
			'year'                  => wp_kses( __( 'Year built', 'myhome-core' ), array() ),
			'property_size_from'    => wp_kses( __( 'Property size from', 'myhome-core' ), array() ),
			'property_size_to'      => wp_kses( __( 'Property size to', 'myhome-core' ), array() ),
			'lot_size_from'         => wp_kses( __( 'Lot size from', 'myhome-core' ), array() ),
			'lot_size_to'           => wp_kses( __( 'Lot size to', 'myhome-core' ), array() ),
			'sell'                  => wp_kses( __( 'Sell', 'myhome-core' ), array() ),
			'rent'                  => wp_kses( __( 'Rent', 'myhome-core' ), array() ),
			'areas'                 => wp_kses( __( 'Areas', 'myhome-core' ), array() ),
			'built_year_from'       => wp_kses( __( 'Built year from', 'myhome-core' ), array() ),
			'built_year_to'         => wp_kses( __( 'Built year to', 'myhome-core' ), array() ),
			'any'                   => wp_kses( __( 'Any', 'myhome-core' ), array() ),
			'reset_settings'        => wp_kses( __( 'Reset settings', 'myhome-core' ), array() ),
            'search'                => wp_kses( __( 'Search', 'myhome-core' ), array() ),
            'from'                  => wp_kses( __( 'From', 'myhome-core' ), array() ),
            'to'                    => wp_kses( __( 'To', 'myhome-core' ), array() ),
            'features'              => wp_kses( __( 'Features', 'myhome-core' ), array() ),
            'bathrooms'             => wp_kses( __( 'Bathrooms', 'myhome-core' ), array() ),
            'bedrooms'              => wp_kses( __( 'Bedrooms', 'myhome-core' ), array() ),
            'lot_size'              => wp_kses( __( 'Lot size', 'myhome-core' ), array() ),
            'property_size'         => wp_kses( __( 'Property Size', 'myhome-core' ), array() ),
            'build_year'            => wp_kses( __( 'Build Year', 'myhome-core' ), array() ),
            'price'                 => wp_kses( __( 'Price', 'myhome-core' ), array() ),
            'keywords'              => wp_kses( __( 'Keywords', 'myhome-core' ), array() ),
            'categories'            => wp_kses( __( 'Categories', 'myhome-core' ), array() ),
            'parameters'            => wp_kses( __( 'Parameters', 'myhome-core' ), array() ),
            'clear_selections'      => wp_kses( __( 'Clear selections', 'myhome-core' ), array() ),
            'clear_search'          => wp_kses( __( 'Clear search', 'myhome-core' ), array() ),
            'advanced'              => wp_kses( __( 'Advanced', 'myhome-core' ), array() ),
            'hide_advanced'         => wp_kses( __( 'Hide', 'myhome-core' ), array() ),
            'show_location'         => wp_kses( __( 'Show Location', 'myhome-core' ), array() ),
            'show_places'           => wp_kses( __( 'Show Places', 'myhome-core' ), array() ),
            'show_near'             => wp_kses( __( 'Show Near', 'myhome-core' ), array() ),
            'clear'                 => wp_kses( __( 'clear', 'myhome-core' ), array() ),
		);
	}

	/*
	 * get_listing
	 *
	 * Listing strings
	 */
	public static function get_listing() {
		return array(
			'sell'              => wp_kses( __( 'Sell', 'myhome-core' ), array() ),
			'rent'              => wp_kses( __( 'Rent', 'myhome-core' ), array() ),
			'compare'           => wp_kses( __( 'Compare', 'myhome-core' ), array() ),
			'added'             => wp_kses( __( 'Added', 'myhome-core' ), array() ),
			'details'           => wp_kses( __( 'Details', 'myhome-core' ), array() ),
			'more'              => wp_kses( __( 'More', 'myhome-core' ), array() ),
			'newest'            => wp_kses( __( 'Newest', 'myhome-core' ), array() ),
            'sort_by'           => wp_kses( __( 'Sort by:', 'myhome-core' ), array() ),
			'popular'           => wp_kses( __( 'Popular', 'myhome-core' ), array() ),
			'price'             => wp_kses( __( 'Price', 'myhome-core' ), array() ),
            'reset'             => wp_kses( __( 'reset', 'myhome-core' ), array() ),
            'full_screen'       => wp_kses( __( 'Full Screen', 'myhome-core' ), array() ),
            'prev'              => wp_kses( __( 'previous', 'myhome-core' ), array() ),
            'next'              => wp_kses( __( 'next', 'myhome-core' ), array() ),
            'price_high_to_low' => wp_kses( __( 'Price (high to low)', 'myhome-core' ), array() ),
            'price_low_to_high' => wp_kses( __( 'Price (low to high)', 'myhome-core' ), array() ),
            'results'           => wp_kses( __( 'results', 'myhome-core' ), array() ),
            'found'             => wp_kses( __( 'Found', 'myhome-core' ), array() )
		);
	}

	/*
	 * get_listing_map
	 *
	 * Listing Map strings
	 */
	public static function get_listing_map() {
	    return array(
            'street_view'       => wp_kses( __( 'Street View', 'myhome-core' ), array() ),
            'prev'              => wp_kses( __( 'Previous', 'myhome-core' ), array() ),
            'next'              => wp_kses( __( 'Next', 'myhome-core' ), array() ),
            'fullscreen'        => wp_kses( __( 'Full screen', 'myhome-core' ), array() ),
            'fullscreen_close'  => wp_kses( __( 'Close full screen', 'myhome-core' ), array() ),
            'clear_search'      => wp_kses( __( 'Clear search', 'myhome-core' ), array() ),
            'reset'             => wp_kses( __( 'Reset', 'myhome-core' ), array() ),
            'no_results'        => wp_kses( __( 'No results found', 'myhome-core' ), array() )
        );
    }

    /*
     * get_estate_map
     *
     * Estate Map strings
     */
	public static function get_estate_map() {
	    return array(
            'street_view'   => wp_kses( __( 'Street View', 'myhome-core' ), array() ),
            'show_location' => wp_kses( __( 'Show location', 'myhome-core' ), array() ),
            'near'          => wp_kses( __( 'Show near', 'myhome-core' ), array() ),
            'fullscreen'    => wp_kses( __( 'Fullscreen', 'myhome-core' ), array() ),
            'prev'          => wp_kses( __( 'Previous', 'myhome-core' ), array() ),
            'next'          => wp_kses( __( 'Next', 'myhome-core' ), array() ),
        );
    }

    /*
     * get_attributes_form
     *
     * Attributes form (backend admin) strings
     */
    public static function get_attributes_form() {
	    return array(
	        'delete_are_you_sure'   => wp_kses( __( 'Are you sure you want to delete this field?', 'myhome-core' ), array() ),
	        'up'                    => wp_kses( __( 'Up', 'myhome-core'), array() ),
	        'down'                  => wp_kses( __( 'Down', 'myhome-core'), array() ),
            'edit'                  => wp_kses( __( 'Edit', 'myhome-core' ), array() ),
            'delete'                => wp_kses( __( 'Delete', 'myhome-core '), array() ),
            'name'                  => wp_kses( __( 'Name', 'myhome-core '), array() ),
            'type'                  => wp_kses( __( 'Type', 'myhome-core' ), array() ),
            'search_form_edit'      => wp_kses( __( 'Edit', 'myhome-core' ), array() ),
            'slug'                  => wp_kses( __( 'Slug', 'myhome-core' ), array() ),
            'required'              => wp_kses( __( 'required', 'myhome-core' ), array() ),
            'separate'              => wp_kses( __( '- separate with commas' ), array() ),
            'yes'                   => wp_kses( __( 'Yes', 'myhome-core' ), array() ),
            'no'                    => wp_kses( __( 'No', 'myhome-core' ), array() )
        );
    }

    /*
     * get_compare
     *
     * Compare (VueJS component) strings
     */
    public static function get_compare() {
	    return array(
            'hide'          => wp_kses( __( 'Hide', 'myhome-core' ), array() ),
            'show'          => wp_kses( __( 'Show', 'myhome-core' ), array() ),
            'details'       => wp_kses( __( 'Details', 'myhome-core' ), array() ),
            'clear'         => wp_kses( __( 'Clear', 'myhome-core' ), array() ),
            'more'          => wp_kses( __( 'More', 'myhome-core' ), array() ),
            'compare'       => wp_kses( __( 'Compare', 'myhome-core' ), array() ),
            'attributes'    => wp_kses( __( 'Attributes', 'myhome-core' ), array() )
        );
    }

    /*
     * get_compare_button
     *
     * Compare button strings
     */
    public static function get_compare_button() {
	    return array(
	        'added'     => wp_kses( __( 'Added', 'myhome-core' ), array() ),
            'compare'   => wp_kses( __( 'Compare', 'myhome-core' ), array() )
        );
    }

    /*
     * get_contact_form
     *
     * Contact form strings
     */
    public static function get_contact_form() {
	    return array(
	        'email'                 => wp_kses( __( 'Email', 'myhome-core' ), array() ),
	        'phone'                 => wp_kses( __( 'Phone', 'myhome-core' ), array() ),
	        'message'               => wp_kses( __( 'Your message', 'myhome-core' ), array() ),
            'send'                  => wp_kses( __( 'Send', 'myhome-core' ), array() ),
            'msg_to_short'          => wp_kses( __( 'Message is to short (min 5 characters) ', 'myhome-core' ), array() ),
            'email_invalid'         => wp_kses( __( 'Email address is invalid', 'myhome-core' ), array() ),
            'msg_error'             => wp_kses( __( 'Error...', 'myhome-core' ), array() ),
            'msg_success'           => wp_kses( __( 'Your message was sent successfully. Thanks.', 'myhome-core' ), array() ),
            'sending'               => wp_kses( __( 'Sending...', 'myhome-core'), array() )
        );
    }

    /*
     * get_frontend_admin
     *
     * Frontend agent panel strings
     */
    public static function get_frontend_admin() {
	    return array(
	        'tour'                      => wp_kses( __( 'Virtual tour', 'myhome-core' ), array() ),
	        'tour_info'                 => wp_kses( __( 'Add embed code', 'myhome-core' ), array() ),
	        'remove_file'               => wp_kses( __( 'Remove file', 'myhome-core' ), array() ),
	        'upload_profile_picture'    => wp_kses( __( 'Upload profile picture', 'myhome-core' ), array() ),
	        'upload_plan_image'         => wp_kses( __( 'Drop plan image here or click to upload', 'myhome-core' ), array() ),
	        'upload_image'              => wp_kses( __( 'Drop featured image here or click to upload', 'myhome-core' ), array() ),
	        'upload_images'             => wp_kses( __( 'Drop all images here or click to upload', 'myhome-core' ), array() ),
	        'cancel_upload'             => wp_kses( __( 'Cancel upload', 'myhome-core' ), array() ),
	        'cancel_upload_confirm'     => wp_kses( __( 'Are you sure you want to cancel this upload?', 'myhome-core' ), array() ),
	        'image_upload_failed'       => wp_kses( __( 'Image upload failed', 'myhome-core' ), array() ),
	        'image_upload_wrong_type'   => wp_kses( __( 'You can\'t upload files of this type.', 'myhome-core' ), array() ),
	        'pay_with_credit_card'      => wp_kses( __( 'Pay with credit card', 'myhome-core' ), array() ),
	        'pay_with_paypal'           => wp_kses( __( 'Pay with PayPal', 'myhome-core' ), array() ),
	        'you_can_login'             => wp_kses( __( 'Thank you for registering. You can log in now.', 'myhome-core' ), array() ),
            'image'                     => wp_kses( __( 'Image', 'myhome-core' ), array() ),
            'title'                     => wp_kses( __( 'Title', 'myhome-core' ), array() ),
            'status'                    => wp_kses( __( 'Status', 'myhome-core' ), array() ),
            'payment'                   => wp_kses( __( 'Payment', 'myhome-core' ), array() ),
            'edit'                      => wp_kses( __( 'Edit', 'myhome-core' ), array() ),
            'log_in'                    => wp_kses( __( 'Log in', 'myhome-core' ), array() ),
            'log_out'                   => wp_kses( __( 'Log out', 'myhome-core' ), array() ),
            'login'                     => wp_kses( __( 'Login', 'myhome-core' ), array() ),
            'password'                  => wp_kses( __( 'password', 'myhome-core' ), array() ),
            'user_name'                 => wp_kses( __( 'user name', 'myhome-core' ), array() ),
            'email'                     => wp_kses( __( 'email', 'myhome-core' ), array() ),
            'name'                      => wp_kses( __( 'Name', 'myhome-core' ), array() ),
            'public_name'               => wp_kses( __( 'Public name', 'myhome-core' ), array() ),
            'welcome'                   => wp_kses( __( 'Welcome', 'myhome-core' ), array() ),
            'submit_property'           => wp_kses( __( 'Submit property', 'myhome-core' ), array() ),
            'my_properties'             => wp_kses( __( 'My properties', 'myhome-core' ), array() ),
            'edit_profile'              => wp_kses( __( 'Edit profile', 'myhome-core' ), array() ),
            'view_my_profile'           => wp_kses( __( 'View my profile', 'myhome-core' ), array() ),
            'phone_number'              => wp_kses( __( 'Phone number', 'myhome-core' ), array() ),
            'profile_picture'           => wp_kses( __( 'Profile picture', 'myhome-core' ), array() ),
            'add_image'                 => wp_kses( __( 'Add image', 'myhome-core' ), array() ),
            'set_image'                 => wp_kses( __( 'Set image', 'myhome-core' ), array() ),
            'facebook_profile_link'     => wp_kses( __( 'Facebook profile link', 'myhome-core' ), array() ),
            'twitter_profile_link'      => wp_kses( __( 'Twitter profile link', 'myhome-core' ), array() ),
            'instagram_profile_link'    => wp_kses( __( 'Instagram profile link', 'myhome-core' ), array() ),
            'linkedin_profile_link'     => wp_kses( __( 'Linkedin profile link', 'myhome-core' ), array() ),
            'update_profile'            => wp_kses( __( 'Update profile', 'myhome-core' ), array() ),
            'register'                  => wp_kses( __( 'Register', 'myhome-core' ), array() ),
            'email_address'             => wp_kses( __( 'email address', 'myhome-core' ), array() ),
            'edit_property'             => wp_kses( __( 'Edit property', 'myhome-core' ), array() ),
            'update_property'           => wp_kses( __( 'Update property', 'myhome-core' ), array() ),
            'description'               => wp_kses( __( 'Description', 'myhome-core' ), array() ),
            'featured_image'            => wp_kses( __( 'Featured image', 'myhome-core' ), array() ),
            'all_images'                => wp_kses( __( 'All images', 'myhome-core' ), array() ),
            'plans'                     => wp_kses( __( 'Plans', 'myhome-core' ), array() ),
            'add_plan'                  => wp_kses( __( 'Add plan', 'myhome-core' ), array() ),
            'delete'                    => wp_kses( __( 'Delete', 'myhome-core' ), array() ),
            'address'                   => wp_kses( __( 'Address', 'myhome-core' ), array() ),
            'visit_my_profile'          => wp_kses( __( 'View my profile', 'myhome-core' ), array() ),
            'reset_password'            => wp_kses( __( 'reset password', 'myhome-core' ), array() ),
            'retrieve_password'         => wp_kses( __( 'Reset password', 'myhome-core' ), array() ),
            'back_to_login'             => wp_kses( __( 'Back to login form', 'myhome-core' ), array() ),
            'remember_me'               => wp_kses( __( 'remember me', 'myhome-core' ), array() ),
            'empty_field'               => wp_kses( __( 'Empty field', 'myhome-core' ), array() ),
            'no_properties'             => wp_kses( __( 'You haven’t added any property yet', 'myhome-core' ), array() ),
            'video'                     => wp_kses( __( 'Video Link (Youtube / Vimeo / Facebook / Twitter / Instagram / link to .mp4)', 'myhome-core' ), array() ),
            'yes'                       => wp_kses( __( 'Yes', 'myhome-core' ), array() ),
            'no'                        => wp_kses( __( 'No', 'myhome-core' ), array() ),
            'delete_property'           => wp_kses( __( 'Are you sure you want to delete', 'myhome-core' ), array() ),
            'user_name_change'          => wp_kses( __( 'Username ( cannot be changed )', 'myhome-core' ), array() ),
            'profile_updated'           => wp_kses( __( 'Profile updated', 'myhome-core' ), array() ),
            'unknown_error'             => wp_kses( __( 'Unknown error', 'myhome-core' ), array() ),
            'logged_in'                 => wp_kses( __( 'You are logged in', 'myhome-core' ), array() ),
            'logged_out'                => wp_kses( __( 'You are logged out', 'myhome-core' ), array() ),
            'select_or_upload_media'    => wp_kses( __( 'Select or upload', 'myhome-core' ), array() ),
            'use_this_media'            => wp_kses( __( 'Use this media', 'myhome-core' ), array() ),
            'tags_instruction'          => wp_kses( __( 'Use commas to separate', 'myhome-core' ), array() ),
            'awaiting_moderation'       => wp_kses( __( 'This property is waiting for approval.', 'myhome-core' ), array() ),
            'make_payment'              => wp_kses( __( 'Make a payment', 'myhome-core' ), array() ),
            'payed'                     => wp_kses( __( 'payed', 'myhome-core' ), array() ),
            'pay'                       => wp_kses( __( 'pay', 'myhome-core' ), array() ),
            'submit_thank_you'          => wp_kses( __( 'Thank you for submitting your property information.', 'myhome-core' ), array() ),
            'submit_thank_you_mod'      => wp_kses( __( 'Thank you for submitting your property information. Your property is waiting for approval.', 'myhome-core' ), array() ),
            'moderation_update'         => wp_kses( __( 'If you edit your property, it won\'t be visible to the public until it is approved again.', 'myhome-core' ), array() ),
            'awaiting_payment'          => wp_kses( __( 'Awaiting payment', 'myhome-core' ), array() ),
            'publish'                   => wp_kses( __( 'Publish', 'myhome-core' ), array() ),
            'pending'                   => wp_kses( __( 'Pending', 'myhome-core' ), array() ),
            'draft'                     => wp_kses( __( 'Draft', 'myhome-core' ), array() ),
            'private'                   => wp_kses( __( 'Private', 'myhome-core' ), array() ),
            'trash'                     => wp_kses( __( 'Trash', 'myhome-core' ), array() ),
            'address_auto_complete'     => wp_kses( __( 'Set map marker without changing address field', 'myhome-core' ), array() )
        );
    }

    public static function get_user_bar() {
        return array(
            'login'             => wp_kses( __( 'Login', 'myhome-core' ), array() ),
            'register'          => wp_kses( __( 'Register', 'myhome-core' ), array() ),
            'submit_property'   => wp_kses( __( 'Submit property', 'myhome-core' ), array() ),
            'my_properties'     => wp_kses( __( 'My properties', 'myhome-core' ), array() ),
            'edit_profile'      => wp_kses( __( 'Edit profile', 'myhome-core' ), array() ),
            'view_profile'      => wp_kses( __( 'View my profile', 'myhome-core' ), array() ),
            'log_out'           => wp_kses( __( 'Log out', 'myhome-core' ), array() )
        );
    }
}

endif;