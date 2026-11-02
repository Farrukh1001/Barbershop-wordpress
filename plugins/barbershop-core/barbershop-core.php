<?php
/**
 * Plugin Name: Barbershop Core
 * Description: Site-specific functionality for the barbershop site.
 * Version: 0.1.0
 */

defined("ABSPATH") || exit;

function barbershop_register_post_types() {

    register_post_type( 'bs_barber', array(
        'labels' => array(
            'name'          => 'Barbers',
            'singular_name' => 'Barber',
            'add_new_item'  => 'Add New Barber',
            'edit_item'     => 'Edit Barber',
        ),
        'public'       => true,
        'has_archive'  => 'barbers',
        'rewrite'      => array( 'slug' => 'barber' ),
        'menu_icon'    => 'dashicons-businessperson',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest' => true,
    ) );

    register_post_type( 'bs_service', array(
        'labels' => array(
            'name'          => 'Services',
            'singular_name' => 'Service',
            'add_new_item'  => 'Add New Service',
            'edit_item'     => 'Edit Service',
        ),
        'public'       => true,
        'has_archive'  => 'services',
        'rewrite'      => array( 'slug' => 'service' ),
        'menu_icon'    => 'dashicons-list-view',
        'supports'     => array( 'title', 'editor', 'excerpt' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'barbershop_register_post_types' );